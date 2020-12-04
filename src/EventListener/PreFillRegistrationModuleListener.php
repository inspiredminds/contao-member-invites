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

use Contao\Controller;
use Contao\CoreBundle\Routing\ScopeMatcher;
use Contao\CoreBundle\ServiceAnnotation\Callback;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * This adjusts the DCA of tl_member in the front end in order to set some defaults for
 * tl_member fields that are also present in tl_member_invite.
 *
 * @Callback(table="tl_member", target="config.onload")
 */
class PreFillRegistrationModuleListener
{
    private $requestStack;
    private $scopeMatcher;

    public function __construct(RequestStack $requestStack, ScopeMatcher $scopeMatcher)
    {
        $this->requestStack = $requestStack;
        $this->scopeMatcher = $scopeMatcher;
    }

    public function __invoke(): void
    {
        $request = $this->requestStack->getCurrentRequest();

        if (!$this->scopeMatcher->isFrontendRequest($request)) {
            return;
        }

        // Check for valid invite
        $uuid = $request->query->get('invite');

        if (empty($uuid)) {
            return;
        }

        $invite = MemberInviteModel::findOneByUuid($uuid);

        if (null === $invite || MemberInviteModel::STATUS_INVITED !== $invite->status) {
            return;
        }

        Controller::loadDataContainer('tl_member_invite');

        $memberFields = &$GLOBALS['TL_DCA']['tl_member']['fields'];
        $inviteFields = &$GLOBALS['TL_DCA']['tl_member_invite']['fields'];

        foreach ($inviteFields as $name => $config) {
            if (!$config['eval']['feEditable']) {
                continue;
            }

            if (!isset($memberFields[$name])) {
                continue;
            }

            $memberFields[$name]['default'] = $invite->{$name};
        }
    }
}
