<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoMemberInvites\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use Contao\MemberModel;
use Contao\Module;
use Contao\ModuleModel;
use Doctrine\DBAL\Connection;
use InspiredMinds\ContaoMemberInvites\Controller\FrontendModule\MemberInviteAcceptController;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @Hook("createNewUser")
 */
class MemberInviteCreateUserListener
{
    private $db;
    private $requestStack;

    public function __construct(Connection $db, RequestStack $requestStack)
    {
        $this->db = $db;
        $this->requestStack = $requestStack;
    }

    public function __invoke(int $userId, array $userData, Module $module): void
    {
        $request = $this->requestStack->getCurrentRequest();
        $uuid = $request->query->get('invite');

        // Check if we have an invite uuid
        if (empty($uuid)) {
            return;
        }

        // Check if this module is actually an invite registration module
        if (!$this->isInviteRegistrationModule($module)) {
            return;
        }

        $invite = MemberInviteModel::findOneByUuid($uuid);

        if (null === $invite) {
            return;
        }

        $member = MemberModel::findById($userId);

        if (null === $member) {
            return;
        }

        // Update all invites belonging to the same email address
        $this->db->executeQuery('UPDATE tl_member_invite SET status = ?, tstamp = ?, member = ? WHERE email = ?', [
            MemberInviteModel::STATUS_OTHER, time(), (int) $member->id, $invite->email,
        ]);

        // Set status for this invite to accepted
        $invite->status = MemberInviteModel::STATUS_ACCEPTED;
        $invite->date_accepted = time();
        $invite->save();
    }

    private function isInviteRegistrationModule(Module $module): bool
    {
        return null !== ModuleModel::findOneBy(['type = ?', 'member_invite_registration_module = ?'], [MemberInviteAcceptController::TYPE, (int) $module->id]);
    }
}
