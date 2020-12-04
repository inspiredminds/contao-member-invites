<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoMemberInvites\EventListener;

use Contao\CoreBundle\ServiceAnnotation\Hook;
use InspiredMinds\ContaoMemberInvites\Action\MemberEditRedirectAction;
use NotificationCenter\Model\Message;
use NotificationCenter\Model\Notification;
use Symfony\Component\Routing\RouterInterface;

/**
 * Provides the ##backend_link## token for the registration notification.
 *
 * @Hook("sendNotificationMessage")
 */
class NotificationBackendLinkTokenListener
{
    private $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function __invoke(Message $message, array &$tokens): bool
    {
        if ('member_registration' !== Notification::findByPk($message->pid)->type) {
            return true;
        }

        $memberId = (int) $tokens['member_id'];
        $link = $this->router->generate(MemberEditRedirectAction::class, ['id' => $memberId], RouterInterface::ABSOLUTE_URL);
        $tokens['backend_link'] = $link;

        return true;
    }
}
