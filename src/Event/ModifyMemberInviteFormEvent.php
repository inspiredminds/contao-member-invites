<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoMemberInvites\Event;

use Codefog\HasteBundle\Form\Form;
use Contao\ModuleModel;
use Symfony\Contracts\EventDispatcher\Event;

class ModifyMemberInviteFormEvent extends Event
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var ModuleModel
     */
    private $moduleModel;

    public function __construct(Form $form, ModuleModel $moduleModel)
    {
        $this->form = $form;
        $this->moduleModel = $moduleModel;
    }

    public function getForm(): Form
    {
        return $this->form;
    }

    public function getModuleModel(): ModuleModel
    {
        return $this->moduleModel;
    }
}
