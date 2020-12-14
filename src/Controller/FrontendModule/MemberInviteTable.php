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
use Contao\ModuleModel;
use Contao\PageModel;
use Contao\System;
use Contao\Template;
use Doctrine\DBAL\Connection;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * @FrontendModule(MemberInviteTable::TYPE, category="memberinvites")
 */
class MemberInviteTable extends AbstractFrontendModuleController
{
    public const TYPE = 'member_invite_table';

    private $security;

    private $db;

    public function __construct(Security $security, Connection $db)
    {
        $this->security = $security;
        $this->db = $db;
    }

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $user = $this->security->getUser();

        if (!$user instanceof FrontendUser) {
            return new Response();
        }

        $invites = MemberInviteModel::findBy(['pid = ?'], [(int) $user->id], ['order' => 'date_invited ASC']);

        if (null === $invites) {
            return new Response();
        }

        System::loadLanguageFile('tl_member_invite');

        $records = [];

        global $objPage;

        /** @var MemberInviteModel $invite */
        foreach ($invites as $invite) {
            $record = (object) $invite->row();
            $record->model = $invite;
            $record->count = $this->getInviteCount($invite);
            $record->date_invited = Date::parse($objPage->datimFormat, $invite->date_invited);
            $record->date_accepted = Date::parse($objPage->datimFormat, $invite->date_accepted);
            $record->date_expire = Date::parse($objPage->datimFormat, $invite->date_expire);
            $record->link = $this->getInviteLink($model, $invite);
            $records[] = $record;
        }

        $template->invites = $records;

        return $template->getResponse();
    }

    private function getInviteCount(MemberInviteModel $invite): int
    {
        if ($invite->member) {
            return (int) $this->db->executeQuery('SELECT SUM(`count`) FROM tl_member_invite WHERE member = ?', [(int) $invite->member])->fetchColumn();
        }

        return (int) $this->db->executeQuery('SELECT SUM(`count`) FROM tl_member_invite WHERE email = ?', [$invite->email])->fetchColumn();
    }

    private function getInviteLink(ModuleModel $module, MemberInviteModel $invite): ?string
    {
        if (!$invite->canResend()) {
            return null;
        }

        global $objPage;

        $targetPage = $objPage;

        if (!empty($module->jumpTo) && null !== ($page = PageModel::findById((int) $module->jumpTo))) {
            $targetPage = $page;
        }

        /* @var PageModel $targetPage */
        return $targetPage->getFrontendUrl().'?resend='.(int) $invite->id;
    }
}
