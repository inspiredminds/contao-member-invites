<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

array_insert($GLOBALS['TL_DCA']['tl_member']['list']['global_operations'], 0, [
    'invites' => [
        'href' => 'table=tl_member_invite',
        'icon' => 'bundles/contaomemberinvites/send.svg',
    ],
]);
