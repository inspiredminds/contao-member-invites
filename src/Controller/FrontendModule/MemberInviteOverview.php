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
use Contao\FrontendUser;
use Contao\ModuleModel;
use Contao\System;
use Contao\Template;
use Doctrine\DBAL\Connection;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Security;

/**
 * @FrontendModule(MemberInviteOverview::TYPE, category="memberinvites")
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

    protected function getResponse(Template $template, ModuleModel $model, Request $request): ?Response
    {
        $user = $this->security->getUser();

        if (!$user instanceof FrontendUser) {
            return new Response();
        }

        $invites = $this->db
            ->executeQuery(
                'SELECT firstname, lastname, email, SUM(count) AS count, member, status FROM tl_member_invite WHERE member = 0 GROUP BY email'
            )
            ->fetchAll()
        ;

        if (empty($invites)) {
            return new Response();
        }

        System::loadLanguageFile('tl_member_invite');

        $records = [];

        /** @var MemberInviteModel $invite */
        foreach ($invites as $invite) {
            $record = (object) $invite;

            $inviters = $this->db
                ->executeQuery(
                    'SELECT pid AS id, SUM(count) AS count FROM tl_member_invite WHERE email = ? GROUP BY pid', [$record->email]
                )
                ->fetchAll()
            ;

            if (\is_array($inviters)) {
                $inviters = array_map(function ($e) {
                    return (object) $e;
                }, $inviters);
            }

            $record->inviters = $inviters ?: [];
            $records[] = $record;
        }

        $template->invites = $records;

        return $template->getResponse();
    }
}
