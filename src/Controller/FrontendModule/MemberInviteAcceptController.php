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

use Contao\Controller;
use Contao\CoreBundle\Controller\FrontendModule\AbstractFrontendModuleController;
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\MemberModel;
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\Template;
use Haste\Form\Form;
use Haste\Util\StringUtil as HasteStringUtil;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use NotificationCenter\Model\Notification;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @FrontendModule(MemberInviteAcceptController::TYPE, category="memberinvites")
 */
class MemberInviteAcceptController extends AbstractFrontendModuleController
{
    public const TYPE = 'member_invite_accept';

    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $uuid = $request->query->get('invite');

        if (empty($uuid)) {
            $template->message = $this->translator->trans('MSC.invalidInviteLink', [], 'contao_default');

            return $template->getResponse();
        }

        $invite = MemberInviteModel::findOneByUuid($uuid);

        // Check if invite exists and it is either still active or expired, otherwise it is invalid
        if (null === $invite || !$this->isInviteValid($invite)) {
            $template->message = $this->translator->trans('MSC.invalidInviteLink', [], 'contao_default');

            return $template->getResponse();
        }

        // Expire the invite link
        $this->expireInvite($invite, $request);

        // Check if invite link expired
        if (MemberInviteModel::STATUS_EXPIRED === $invite->status) {
            $template->message = $this->translator->trans('MSC.inviteLinkExpired', [], 'contao_default');

            $form = new Form('member-invite-accept-'.$model->id, Request::METHOD_POST, function ($form) use ($request) {
                return $request->request->get('FORM_SUBMIT') === $form->getFormId();
            });

            $form->addSubmitFormField('submit', $this->translator->trans('MSC.requestInvite', [], 'contao_default'));

            if ($form->validate()) {
                if (!empty($model->nc_notification) && null !== ($notification = Notification::findByPk($model->nc_notification))) {
                    $tokens = [
                        'resend_link' => $this->buildResendUrl($invite, $model, $request),
                        'admin_email' => $GLOBALS['TL_ADMIN_EMAIL'],
                    ];

                    $member = MemberModel::findByPk((int) $invite->pid);

                    HasteStringUtil::flatten($member->row(), 'member', $tokens);
                    HasteStringUtil::flatten($invite->row(), 'invite', $tokens);

                    $notification->send($tokens);
                }

                $invite->status = MemberInviteModel::STATUS_REQUESTED;
                $invite->tstamp = time();
                $invite->save();

                $template->message = $this->translator->trans('MSC.inviteLinkRequestSent', [], 'contao_default');
            } else {
                $template->form = $form->generate();
            }

            return $template->getResponse();
        }

        // Otherwise show registration module
        return new Response(Controller::getFrontendModule($model->member_invite_registration_module));
    }

    private function isInviteValid(MemberInviteModel $invite): bool
    {
        // Only invites that are in status 'invited' or 'expired' can be processed by this module
        $validStates = [
            MemberInviteModel::STATUS_INVITED,
            MemberInviteModel::STATUS_EXPIRED,
        ];

        return \in_array($invite->status, $validStates, true);
    }

    private function expireInvite(MemberInviteModel $invite, Request $request): void
    {
        // Only invites that are sent or requested can be expired
        if (!\in_array($invite->status, [MemberInviteModel::STATUS_INVITED, MemberInviteModel::STATUS_REQUESTED], true)) {
            return;
        }

        // Do not expire invites during POST requests
        if ($request->isMethod(Request::METHOD_POST)) {
            return;
        }

        // Check if invite is actually expired
        if (time() < $invite->date_expire) {
            return;
        }

        // Expire the invite
        $invite->status = MemberInviteModel::STATUS_EXPIRED;
        $invite->tstamp = time();
        $invite->save();
    }

    private function buildResendUrl(MemberInviteModel $invite, ModuleModel $module, Request $request): string
    {
        $queryString = '?resend='.$invite->id;

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
