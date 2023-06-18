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
use Contao\CoreBundle\ServiceAnnotation\FrontendModule;
use Contao\Date;
use Contao\FrontendUser;
use Contao\MemberModel;
use Contao\ModuleModel;
use Contao\System;
use Contao\Template;
use Doctrine\DBAL\Connection;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * @FrontendModule(MemberInviteOverview::TYPE, category="memberinvites", template="mod_member_invite_overview")
 */
class MemberInviteOverview extends AbstractFrontendModuleController
{
    public const TYPE = 'member_invite_overview';

    private $security;

    private $db;

    public function __construct(Security $security, Connection $db)
    {
        $this->security = $security;
        $this->db = $db;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): Response
    {
        $user = $this->security->getUser();

        if (!$user instanceof FrontendUser) {
            return new Response();
        }

        $inviteCollection = MemberInviteModel::findAll(['order' => 'date_invited DESC']);

        if (null === $inviteCollection) {
            return new Response();
        }

        $groupedInvites = [];

        global $objPage;

        foreach ($inviteCollection as $invite) {
            if (!isset($groupedInvites[$invite->email])) {
                $groupedInvites[$invite->email] = (object) ['invites' => [], 'count' => 0];
            }

            $record = (object) $invite->row();
            $record->inviter = MemberModel::findById($record->pid);

            $record->date_invited = Date::parse($objPage->datimFormat, $invite->date_invited);
            $record->date_accepted = Date::parse($objPage->datimFormat, $invite->date_accepted);
            $record->date_expire = Date::parse($objPage->datimFormat, $invite->date_expire);

            $record->model = $invite;

            $groupedInvites[$invite->email]->invites[] = $record;
            $groupedInvites[$invite->email]->count += $invite->count;
        }

        System::loadLanguageFile('tl_member_invite');

        $template->groupedInvites = $groupedInvites;

        return $template->getResponse();
    }
}
