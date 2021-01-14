<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoMemberInvites\EventListener\DataContainer;

use Contao\CoreBundle\ServiceAnnotation\Callback;
use Contao\DataContainer;
use Doctrine\DBAL\Connection;

/**
 * @Callback(table="tl_member", target="config.ondelete")
 */
class DeleteInviteCallbackListener
{
    private $db;

    public function __construct(Connection $db)
    {
        $this->db = $db;
    }

    public function __invoke(DataContainer $dc, int $undoId): void
    {
        if (!$dc->id) {
            return;
        }

        $this->db->delete('tl_member_invite', ['pid' => (int) $dc->id]);
        $this->db->delete('tl_member_invite', ['member' => (int) $dc->id]);
    }
}
