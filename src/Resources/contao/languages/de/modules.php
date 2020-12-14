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
use InspiredMinds\ContaoMemberInvites\Controller\FrontendModule\MemberInviteOverview;
use InspiredMinds\ContaoMemberInvites\Controller\FrontendModule\MemberInviteTable;

$GLOBALS['TL_LANG']['MOD']['tl_member_invite'] = 'Einladungen';
$GLOBALS['TL_LANG']['FMD']['memberinvites'] = 'Mitglieder Einladungen';
$GLOBALS['TL_LANG']['FMD'][MemberInviteFormController::TYPE] = ['Mitglieds-Einladungs-Formular', 'Formular zum erzeugen von Einladungen.'];
$GLOBALS['TL_LANG']['FMD'][MemberInviteAcceptController::TYPE] = ['Einladung akzeptieren', 'Verarbeitet die Annahme von Einladungen und zeigt das Registrierungsmodul, wenn der Einladungs-Link gültig war.'];
$GLOBALS['TL_LANG']['FMD'][MemberInviteTable::TYPE] = ['Einladungs-Tabelle', 'Diese Tabelle zeigt alle gesendeten Einladungen des aktuellen Mitglieds.'];
$GLOBALS['TL_LANG']['FMD'][MemberInviteOverview::TYPE] = ['Übersicht an Einladungen', 'Diese Tabelle zeigt eine Übersicht aller Einladungen im System im Frontend.'];
