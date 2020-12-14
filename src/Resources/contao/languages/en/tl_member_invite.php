<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use Contao\System;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;

System::loadLanguageFile('tl_member');

$GLOBALS['TL_LANG']['tl_member_invite']['email'] = &$GLOBALS['TL_LANG']['tl_member']['email'];
$GLOBALS['TL_LANG']['tl_member_invite']['firstname'] = &$GLOBALS['TL_LANG']['tl_member']['firstname'];
$GLOBALS['TL_LANG']['tl_member_invite']['lastname'] = &$GLOBALS['TL_LANG']['tl_member']['lastname'];
$GLOBALS['TL_LANG']['tl_member_invite']['message'] = ['Message', 'Message to the invitee.'];
$GLOBALS['TL_LANG']['tl_member_invite']['date_invited'] = ['Invite date', 'Date the invite was sent or resent.'];
$GLOBALS['TL_LANG']['tl_member_invite']['date_accepted'] = ['Accept date', 'Date the invite was accepted.'];
$GLOBALS['TL_LANG']['tl_member_invite']['date_expire'] = ['Expire date', 'Date the invite expires.'];
$GLOBALS['TL_LANG']['tl_member_invite']['uuid'] = ['UUID', 'UUID of the invite.'];
$GLOBALS['TL_LANG']['tl_member_invite']['status'] = ['Status', 'Status of the invite.'];
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_INVITED] = 'Invited';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_ACCEPTED] = 'Accepted';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_OTHER] = 'Accepted from other sender';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_EXPIRED] = 'Expired';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_REQUESTED] = 'Requested';
$GLOBALS['TL_LANG']['tl_member_invite']['member'] = ['Member', 'Invited member.'];
$GLOBALS['TL_LANG']['tl_member_invite']['count'] = ['Count', 'Invite count.'];
