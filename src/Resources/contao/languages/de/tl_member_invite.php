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
$GLOBALS['TL_LANG']['tl_member_invite']['message'] = ['Nachricht', 'Nachricht an die Person.'];
$GLOBALS['TL_LANG']['tl_member_invite']['date_invited'] = ['Einladungsdatum', 'Datum an dem die Einladung gesendet oder erneut gesendet wurde.'];
$GLOBALS['TL_LANG']['tl_member_invite']['date_accepted'] = ['Annahmedatum', 'Datum an dem die Einladung angenommen wurde.'];
$GLOBALS['TL_LANG']['tl_member_invite']['date_expire'] = ['Ablaufdatum', 'Ablaufdatum der Einladugn.'];
$GLOBALS['TL_LANG']['tl_member_invite']['uuid'] = ['UUID', 'UUID der Einladung.'];
$GLOBALS['TL_LANG']['tl_member_invite']['status'] = ['Status', 'Status der Einladung.'];
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_INVITED] = 'Eingeladen';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_ACCEPTED] = 'Angenommen';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_OTHER] = 'Von Anderem Sender angenommen';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_EXPIRED] = 'Abgelaufen';
$GLOBALS['TL_LANG']['tl_member_invite']['statuses'][MemberInviteModel::STATUS_REQUESTED] = 'Angefordert';
$GLOBALS['TL_LANG']['tl_member_invite']['member'] = ['Mitglied', 'Eingeladenes Mitglied.'];
$GLOBALS['TL_LANG']['tl_member_invite']['count'] = ['Anzahl', 'Anzahl an Einladungen.'];
