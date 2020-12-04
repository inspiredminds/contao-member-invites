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
use Contao\Image;
use Contao\StringUtil;
use InspiredMinds\ContaoMemberInvites\Model\MemberInviteModel;

/**
 * @Callback(table="tl_member_invite", target="list.sorting.child_record")
 */
class MemberInviteChildRecordCallbackListener
{
    public function __invoke(array $row): string
    {
        $record = $row['id'];

        $list = &$GLOBALS['TL_DCA']['tl_member_invite']['list']['label'] ?? null;

        if (null === $list) {
            return $this->wrapRecord($row, $record);
        }

        if (empty($list['fields']) || empty($list['format'])) {
            return $this->wrapRecord($row, implode(' ', array_filter([$row['firstname'], $row['lastname']])).' <'.$row['email'].'>');
        }

        $fieldData = [];

        foreach ($list['fields'] as $labelField) {
            if (isset($row[$labelField])) {
                $fieldData[] = $row[$labelField];
            }
        }

        $record = sprintf($list['format'], ...$fieldData);

        return $this->wrapRecord($row, $record);
    }

    private function wrapRecord(array $row, string $record): string
    {
        $icon = 'clock.svg';

        switch ($row['status']) {
            case MemberInviteModel::STATUS_ACCEPTED: $icon = 'user-check.svg'; break;
            case MemberInviteModel::STATUS_OTHER: $icon = 'check.svg'; break;
            case MemberInviteModel::STATUS_EXPIRED: $icon = 'x.svg'; break;
        }

        if (!\in_array($row['status'], [MemberInviteModel::STATUS_ACCEPTED, MemberInviteModel::STATUS_OTHER], true) && time() >= $row['date_expire']) {
            $icon = 'x.svg';
        }

        $icon = 'bundles/contaomemberinvites/'.$icon;

        $record = Image::getHtml($icon, '', ' style="float:left; margin-right:0.3em;"').' '.StringUtil::specialchars($record);

        return '<div class="tl_content_left">'.$record.'</div>';
    }
}
