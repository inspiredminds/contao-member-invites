<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;

$GLOBALS['BE_MOD']['accounts']['member']['tables'][] = 'tl_member_invite';
$GLOBALS['TL_MODELS']['tl_member_invite'] = MemberInviteModel::class;

$tokensContent = ['member_*', 'invite_*', 'invite_url', 'admin_email'];
$tokensAddress = ['admin_email', 'member_*', 'invite_*'];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['member_invites']['member_invite'] = [
    'recipients' => $tokensAddress,
    'email_subject' => $tokensContent,
    'email_text' => $tokensContent,
    'email_html' => $tokensContent,
    'email_sender_name' => $tokensAddress,
    'email_sender_address' => $tokensAddress,
    'email_recipient_cc' => $tokensAddress,
    'email_recipient_bcc' => $tokensAddress,
    'email_replyTo' => $tokensAddress,
];

$tokensContent = ['member_*', 'invite_*', 'resend_url', 'admin_email'];
$tokensAddress = ['admin_email', 'member_*', 'invite_*'];

$GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['member_invites']['member_invite_request'] = [
    'recipients' => $tokensAddress,
    'email_subject' => $tokensContent,
    'email_text' => $tokensContent,
    'email_html' => $tokensContent,
    'email_sender_name' => $tokensAddress,
    'email_sender_address' => $tokensAddress,
    'email_recipient_cc' => $tokensAddress,
    'email_recipient_bcc' => $tokensAddress,
    'email_replyTo' => $tokensAddress,
];

foreach ($GLOBALS['NOTIFICATION_CENTER']['NOTIFICATION_TYPE']['contao']['member_registration'] as &$tokens) {
    if (\in_array('link', $tokens, true)) {
        $tokens[] = 'backend_link';
    }
}
