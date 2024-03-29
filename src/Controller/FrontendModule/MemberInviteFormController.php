<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoMemberInvites\Controller\FrontendModule;

use Codefog\HasteBundle\Form\Form;
use Codefog\HasteBundle\StringParser;
use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\StringUtil;
use Contao\System;
use Contao\Template;
use Contao\Widget;
use InspiredMinds\ContaoMemberInvites\Event\ModifyMemberInviteFormEvent;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use NotificationCenter\Model\Notification;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @FrontendModule(MemberInviteFormController::TYPE, category="memberinvites", template="mod_member_invite_form")
 */
class MemberInviteFormController extends AbstractFrontendModuleController
{
    public const TYPE = 'member_invite_form';

    private const SESSION_MESSAGE_KEY = 'member-invite-form-message';

    private $translator;
    private $security;
    private $eventDispatcher;
    private $session;

    public function __construct(TranslatorInterface $translator, Security $security, EventDispatcherInterface $eventDispatcher, SessionInterface $session)
    {
        $this->translator = $translator;
        $this->security = $security;
        $this->eventDispatcher = $eventDispatcher;
        $this->session = $session;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $member = $this->security->getUser();

        if (!$member instanceof FrontendUser) {
            return new Response();
        }

        $form = new Form('member-invite-form-'.$model->id, Request::METHOD_POST, function ($form) use ($request) {
            return $request->request->get('FORM_SUBMIT') === $form->getFormId();
        });

        $invite = null;
        $isResend = false;
        $email = null;
        $stringParser = new StringParser();

        // Load language file before creating a model instance (see https://github.com/contao/contao/pull/2536)
        System::loadLanguageFile('tl_member_invite');
        System::loadLanguageFile('tl_module');
        Controller::loadDataContainer('tl_module');

        if ($request->query->has('resend')) {
            $invite = MemberInviteModel::findById((int) $request->query->get('resend'));
        }

        if (null === $invite) {
            $invite = new MemberInviteModel();
        } else {
            if ((int) $invite->pid !== (int) $member->id) {
                throw new AccessDeniedException('Invite belongs to different member.');
            }

            if (!$invite->canResend()) {
                throw new BadRequestHttpException('Invite cannot be resent.');
            }

            $isResend = true;
            $email = $invite->email;
        }

        $form->setBoundModel($invite);

        $form->addFieldsFromDca('tl_member_invite', function (string $field, array &$config) use ($isResend, $member, $stringParser) {
            // Set email field to readonly in resend mode
            if ($isResend && 'email' === $field) {
                $config['eval']['readonly'] = true;
            }

            if (!$isResend) {
                $config['ignoreModelValue'] = true;
            }

            // Transform default value for message
            if ('message' === $field && !empty($config['default'])) {
                $tokens = [];
                $stringParser->flatten($member->getData(), 'member', $tokens);
                $config['default'] = $stringParser->recursiveReplaceTokensAndTags($config['default'], $tokens);
            }

            return $config['eval']['feEditable'] ?? false;
        });

        $expirationDca = $GLOBALS['TL_DCA']['tl_module']['fields']['member_invite_expiration'];
        $expirationDca['default'] = $model->member_invite_expiration;
        $expirationDca['ignoreModelValue'] = true;

        $form->addFormField('expiration', $expirationDca);

        $form->addSubmitFormField('submit', $this->translator->trans('MSC.sendInvite', [], 'contao_default'));

        // Do not allow to send invitations to the same email again
        $form->addValidator('email', function ($value, Widget $widget, Form $form) use ($isResend, $member) {
            if (!$isResend && null !== MemberInviteModel::findBy(['email = ?', 'pid = ?'], [strtolower($value), (int) $member->id])) {
                throw new \Exception($this->translator->trans('ERR.unique', [], 'contao_default'));
            }

            if (null !== MemberInviteModel::findBy(['email = ?', "status = '".MemberInviteModel::STATUS_ACCEPTED."'"], [strtolower($value)])) {
                throw new \Exception($this->translator->trans('ERR.unique', [], 'contao_default'));
            }

            return $value;
        });

        // Require ##invite_link## token
        if ($model->member_invite_require_link_token) {
            $form->addValidator('message', function ($value, Widget $widget, Form $form) {
                if (!str_contains(StringUtil::decodeEntities($value), '##invite_link##')) {
                    $widget->value .= "\n\n##invite_link##";

                    throw new \Exception($this->translator->trans('ERR.inviteLinkTokenMissing', [], 'contao_default'));
                }

                return $value;
            });
        }

        $this->eventDispatcher->dispatch(new ModifyMemberInviteFormEvent($form, $model));

        if ($form->validate()) {
            // Reset the email address, just in case it has been changed
            if ($isResend) {
                $invite->email = $email;
            }

            // Transform email to lower case
            $invite->email = strtolower($invite->email);

            $invite->pid = (int) $member->id;
            $invite->tstamp = time();
            $invite->date_invited = time();
            $invite->status = MemberInviteModel::STATUS_INVITED;
            $invite->date_expire = strtotime($form->fetch('expiration'));
            $invite->uuid = Uuid::uuid4()->toString();
            $invite->count = (int) $invite->count + 1;

            $invite->save();

            // Send notification
            if (!empty($model->nc_notification) && null !== ($notification = Notification::findByPk($model->nc_notification))) {
                $tokens = [
                    'invite_link' => $this->buildInviteUrl($invite, $model, $request),
                    'admin_email' => $GLOBALS['TL_ADMIN_EMAIL'],
                ];

                $stringParser->flatten($member->getData(), 'member', $tokens);
                $stringParser->flatten($invite->row(), 'invite', $tokens);

                $notification->send($tokens);

                $this->session->set(self::SESSION_MESSAGE_KEY, $this->translator->trans('MSC.memberInviteSent', [], 'contao_default'));
            } else {
                $this->session->set(self::SESSION_MESSAGE_KEY, $this->translator->trans('MSC.memberInviteCreated', [], 'contao_default'));
            }

            return new RedirectResponse($request->getUri(), Response::HTTP_SEE_OTHER);
        }

        $template->form = $form->generate();

        // Add JavaScript
        $GLOBALS['TL_BODY'][] = Template::generateScriptTag('bundles/contaomemberinvites/replace-message-tokens.js', true, null);

        if ($this->session->has(self::SESSION_MESSAGE_KEY)) {
            $template->message = $this->session->get(self::SESSION_MESSAGE_KEY);
            $this->session->remove(self::SESSION_MESSAGE_KEY);
        }

        return $template->getResponse();
    }

    private function buildInviteUrl(MemberInviteModel $invite, ModuleModel $module, Request $request): string
    {
        $queryString = '?invite='.$invite->uuid;

        if (!empty($module->jumpTo) && null !== ($page = PageModel::findByPk($module->jumpTo))) {
            return $page->getAbsoluteUrl().$queryString;
        }

        global $objPage;

        /** @var PageModel $objPage */
        if (null !== ($page = PageModel::findFirstPublishedRegularByPid($objPage->rootId))) {
            return $page->getAbsoluteUrl().$queryString;
        }

        return $request->getSchemeAndHttpHost().$request->getBaseUrl().$queryString;
    }
}
