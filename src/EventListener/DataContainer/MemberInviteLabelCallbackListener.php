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
use Contao\Image;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @Callback(table="tl_member_invite", target="list.label.label")
 */
class MemberInviteLabelCallbackListener
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function __invoke(array $row, string $label, DataContainer $dc, array $labels): array
    {
        /** @var MemberInviteModel $invite */
        $invite = MemberInviteModel::findById($row['id']);
        $invite->preventSaving();
        $invite->expire();

        $attributes = ' style="float:left"';
        $attributes .= ' title="'.$this->translator->trans('tl_member_invite.statuses.'.$invite->status, [], 'contao_tl_member_invite').'"';

        $labels[0] = Image::getHtml($this->getIcon($invite), '', $attributes);

        return $labels;
    }

    private function getIcon(MemberInviteModel $invite): string
    {
        $icon = 'clock.svg';

        switch ($invite->status) {
            case MemberInviteModel::STATUS_ACCEPTED: $icon = 'user-check.svg'; break;
            case MemberInviteModel::STATUS_OTHER: $icon = 'check.svg'; break;
            case MemberInviteModel::STATUS_EXPIRED: $icon = 'x.svg'; break;
        }

        return 'bundles/contaomemberinvites/'.$icon; 
    }
}
