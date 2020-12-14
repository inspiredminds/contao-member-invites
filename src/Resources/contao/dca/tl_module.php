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

$GLOBALS['TL_DCA']['tl_module']['fields']['nc_notification']['eval']['ncNotificationChoices'][MemberInviteFormController::TYPE] = ['member_invite'];
$GLOBALS['TL_DCA']['tl_module']['fields']['nc_notification']['eval']['ncNotificationChoices'][MemberInviteAcceptController::TYPE] = ['member_invite_request'];

$GLOBALS['TL_DCA']['tl_module']['fields']['member_invite_expiration'] = [
    'exclude' => true,
    'inputType' => 'select',
    'options' => ['+1 day', '+7 days', '+30 days', '+1 year'],
    'reference' => &$GLOBALS['TL_LANG']['tl_module']['member_invite_expiration_durations'],
    'eval' => ['tl_class' => 'w50'],
    'sql' => ['type' => 'string', 'length' => 16, 'default' => '+30 days'],
];

$GLOBALS['TL_DCA']['tl_module']['fields']['member_invite_registration_module'] = [
    'exclude' => true,
    'inputType' => 'select',
    'reference' => &$GLOBALS['TL_LANG']['tl_module'],
    'eval' => ['includeBlankOption' => true, 'tl_class' => 'w50', 'mandatory' => true],
    'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
];

$GLOBALS['TL_DCA']['tl_module']['palettes'][MemberInviteFormController::TYPE] =
    '{title_legend},name,headline,type;{config_legend},nc_notification,member_invite_expiration;{redirect_legend},jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID'
;

$GLOBALS['TL_DCA']['tl_module']['palettes'][MemberInviteAcceptController::TYPE] =
    '{title_legend},name,headline,type;{config_legend},nc_notification,member_invite_registration_module;{redirect_legend},jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID'
;

$GLOBALS['TL_DCA']['tl_module']['palettes'][MemberInviteTable::TYPE] =
    '{title_legend},name,headline,type;{redirect_legend},jumpTo;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID'
;

$GLOBALS['TL_DCA']['tl_module']['palettes'][MemberInviteOverview::TYPE] =
    '{title_legend},name,headline,type;{template_legend:hide},customTpl;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID'
;
