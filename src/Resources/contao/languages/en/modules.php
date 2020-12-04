<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use InspiredMinds\ContaoMemberInvites\Controller\FrontendModule\MemberInviteAcceptController;
use InspiredMinds\ContaoMemberInvites\Controller\FrontendModule\MemberInviteFormController;

$GLOBALS['TL_LANG']['MOD']['tl_member_invite'] = 'Invites';
$GLOBALS['TL_LANG']['FMD']['memberinvites'] = 'Member invites';
$GLOBALS['TL_LANG']['FMD'][MemberInviteFormController::TYPE] = ['Member invite form', 'Form for sending member invites.'];
$GLOBALS['TL_LANG']['FMD'][MemberInviteAcceptController::TYPE] = ['Member invite accept', 'Handles accepting invites and shows the registration module, if the invite link was valid.'];
