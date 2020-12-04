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

use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\Exception\AccessDeniedException;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Contao\Template;
use Contao\Widget;
use Haste\Form\Form;
use Haste\Util\StringUtil as HasteStringUtil;
use InspiredMinds\ContaoMemberInvites\Event\ModifyMemberInviteFormEvent;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use NotificationCenter\Model\Notification;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Security;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @FrontendModule(MemberInviteFormController::TYPE, category="memberinvites")
 */
class MemberInviteFormController extends AbstractFrontendModuleController
{
    public const TYPE = 'member_invite_form';

    private $translator;
    private $security;
    private $eventDispatcher;

    public function __construct(TranslatorInterface $translator, Security $security, EventDispatcherInterface $eventDispatcher)
    {
        $this->translator = $translator;
        $this->security = $security;
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
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

        // Load language file before creating a model instance (see https://github.com/contao/contao/pull/2536)
        System::loadLanguageFile('tl_member_invite');

        if ($request->query->has('resend')) {
            $invite = MemberInviteModel::findById((int) $request->query->get('resend'));
        }

        if (null === $invite) {
            $invite = new MemberInviteModel();
        } else {
            if ((int) $invite->pid !== (int) $member->id) {
                throw new AccessDeniedException('Invite belongs to different member.');
            }

            if ('expired' !== $invite->status || time() > $invite->date_expire) {
                throw new BadRequestHttpException('Invite cannot be resent.');
            }

            $isResend = true;
            $email = $invite->email;
        }

        $form->bindModel($invite);

        $form->addFieldsFromDca('tl_member_invite', function (string $field, array &$config) use ($isResend) {
            // Set email field to readonly in resend mode
            if ($isResend && 'email' === $field) {
                $config['eval']['readonly'] = true;
            }

            return $config['eval']['feEditable'] ?? false;
        });

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
            $invite->date_expire = strtotime($model->member_invite_expiration);
            $invite->uuid = Uuid::uuid4()->toString();

            $invite->save();

            // Send notification
            if (!empty($model->nc_notification) && null !== ($notification = Notification::findByPk($model->nc_notification))) {
                $tokens = [
                    'invite_link' => $this->buildInviteUrl($invite, $model, $request),
                    'admin_email' => $GLOBALS['TL_ADMIN_EMAIL'],
                ];

                HasteStringUtil::flatten($member->getData(), 'member', $tokens);
                HasteStringUtil::flatten($invite->row(), 'invite', $tokens);

                $notification->send($tokens);

                $template->message = $this->translator->trans('MSC.memberInviteSent', [], 'contao_default');
            } else {
                $template->message = $this->translator->trans('MSC.memberInviteCreated', [], 'contao_default');
            }

            // Reset the form
            foreach ($form->getWidgets() as $widget) {
                $widget->value = null;
                $widget->readonly = false;
            }
        }

        $template->form = $form->generate();

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
