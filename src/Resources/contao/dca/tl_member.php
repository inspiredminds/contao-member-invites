<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

$GLOBALS['TL_DCA']['tl_member']['config']['ctable'][] = 'tl_member_invite';

$GLOBALS['TL_DCA']['tl_member']['list']['operations'] = \array_slice($GLOBALS['TL_DCA']['tl_member']['list']['operations'], 0, 4, true) + [
    'invites' => [
        'href' => 'table=tl_member_invite',
        'icon' => 'bundles/contaomemberinvites/send.svg',
    ],
] + \array_slice($GLOBALS['TL_DCA']['tl_member']['list']['operations'], 4, \count($GLOBALS['TL_DCA']['tl_member']['list']['operations']) - 1, true);
