<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoMemberInvites\Model;

use Contao\Model;

/**
 * @property int      $id
 * @property int      $pid
 * @property int      $tstamp
 * @property string   $email
 * @property string   $firstname
 * @property string   $lastname
 * @property string   $message
 * @property int|null $date_invited
 * @property int|null $date_accepted
 * @property int|null $date_expire
 * @property string   $uuid
 * @property string   $status
 * @property int      $member
 *
 * @method static MemberInviteModel|null findOneByUuid($id, array $opt=array())
 */
class MemberInviteModel extends Model
{
    public const STATUS_INVITED = 'invited';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_OTHER = 'other';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_REQUESTED = 'requested';

    protected static $strTable = 'tl_member_invite';

    public function canResend(): bool
    {
        if (\in_array($this->status, [self::STATUS_ACCEPTED, self::STATUS_OTHER], true)) {
            return false;
        }

        return true;
    }
}
