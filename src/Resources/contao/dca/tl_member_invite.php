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

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_member_invite'] = [
    'config' => [
        'dataContainer' => 'Table',
        'ptable' => 'tl_member',
        'notEditable' => true,
        'notDeletable' => true,
        'notCopyable' => true,
        'notCreatable' => true,
        'sql' => [
            'keys' => [
                'id' => 'primary',
                'uuid' => 'index',
            ],
        ],
    ],
    'list' => [
        'sorting' => [
            'mode' => 4,
            'flag' => 12,
            'headerFields' => ['firstname', 'lastname'],
            'panelLayout' => 'filter;limit',
            'fields' => ['date_invited'],
        ],
        'label' => [
            'fields' => ['firstname', 'lastname', 'email'],
            'format' => '%s %s <%s>',
        ],
        'operations' => [
            'show' => [
                'href' => 'act=show',
                'icon' => 'show.svg',
            ],
        ],
    ],
    'fields' => [
        'id' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'autoincrement' => true],
        ],
        'tstamp' => [
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
        ],
        'pid' => [
            'foreignKey' => 'tl_member.CONCAT(firstname," ",lastname)',
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'belongsTo', 'load' => 'lazy'],
        ],
        'email' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'email', 'tl_class' => 'w50', 'maxlength' => 255, 'mandatory' => true, 'feEditable' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'firstname' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'clr w50', 'maxlength' => 255, 'feEditable' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'lastname' => [
            'exclude' => true,
            'search' => true,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 255, 'feEditable' => true],
            'sql' => ['type' => 'string', 'length' => 255, 'default' => ''],
        ],
        'message' => [
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => ['tl_class' => 'clr', 'maxlength' => 2048, 'feEditable' => true],
            'sql' => ['type' => 'text', 'notnull' => false],
        ],
        'date_invited' => [
            'exclude' => true,
            'flag' => 8,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'integer', 'notnull' => false],
        ],
        'date_accepted' => [
            'exclude' => true,
            'flag' => 8,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'integer', 'notnull' => false],
        ],
        'date_expire' => [
            'exclude' => true,
            'flag' => 8,
            'sorting' => true,
            'inputType' => 'text',
            'eval' => ['rgxp' => 'datim', 'datepicker' => true, 'tl_class' => 'w50 wizard'],
            'sql' => ['type' => 'integer', 'notnull' => false],
        ],
        'uuid' => [
            'search' => true,
            'exclude' => true,
            'inputType' => 'text',
            'eval' => ['tl_class' => 'w50', 'maxlength' => 64, 'mandatory' => true],
            'sql' => ['type' => 'string', 'length' => 64, 'default' => ''],
        ],
        'status' => [
            'filter' => true,
            'exclude' => true,
            'inputType' => 'select',
            'options' => [
                MemberInviteModel::STATUS_INVITED,
                MemberInviteModel::STATUS_ACCEPTED,
                MemberInviteModel::STATUS_OTHER,
                MemberInviteModel::STATUS_EXPIRED,
                MemberInviteModel::STATUS_REQUESTED,
            ],
            'reference' => &$GLOBALS['TL_LANG']['tl_member_invite']['statuses'],
            'eval' => ['tl_class' => 'w50'],
            'sql' => ['type' => 'string', 'length' => 8, 'default' => MemberInviteModel::STATUS_INVITED],
        ],
        'member' => [
            'exclude' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_member.CONCAT(firstname," ",lastname)',
            'eval' => ['chosen' => true, 'doNotCopy' => true, 'includeBlankOption' => true, 'tl_class' => 'w50'],
            'sql' => ['type' => 'integer', 'unsigned' => true, 'default' => 0],
            'relation' => ['type' => 'hasOne', 'load' => 'lazy'],
        ],
    ],
    'palettes' => [
        'default' => '{invite_legend},sender,email,firstname,lastname,message,date_invited,date_accepted,date_expire,status,member',
    ],
];
