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
use Contao\ModuleModel;
use InspiredMinds\ContaoMemberInvites\Controller\FrontendModule\MemberInviteAcceptController;
use InspiredMinds\ContaoMemberInvites\Controller\FrontendModule\MemberInviteFormController;

/**
 * @Callback(table="tl_module", target="config.onload")
 */
class MakeRedirectPageMandatoryListener
{
    public function __invoke(DataContainer $dc): void
    {
        $model = ModuleModel::findById($dc->id);

        if (null === $model || !\in_array($model->type, [MemberInviteFormController::TYPE, MemberInviteAcceptController::TYPE], true)) {
            return;
        }

        $GLOBALS['TL_DCA']['tl_module']['fields']['jumpTo']['eval']['mandatory'] = true;
    }
}
