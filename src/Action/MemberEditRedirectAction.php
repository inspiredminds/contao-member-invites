<?php

declare(strict_types=1);

/*
 * This file is part of the Contao Member Invites extension.
 *
 * (c) inspiredminds
 *
 * @license LGPL-3.0-or-later
 */

namespace InspiredMinds\ContaoMemberInvites\Action;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

/**
 * @Route("/contao/_edit-member/{id}", name=MemberEditRedirectAction::class, defaults={"_scope": "backend"})
 */
class MemberEditRedirectAction
{
    private $router;
    private $tokenManager;
    private $tokenName;

    public function __construct(RouterInterface $router, CsrfTokenManagerInterface $tokenManager, string $tokenName)
    {
        $this->router = $router;
        $this->tokenManager = $tokenManager;
        $this->tokenName = $tokenName;
    }

    public function __invoke(int $id): Response
    {
        $redirect = $this->router->generate('contao_backend', [
            'do' => 'member',
            'act' => 'edit',
            'id' => (int) $id,
            'rt' => $this->tokenManager->getToken($this->tokenName)->getValue(),
        ]);

        return new RedirectResponse($redirect, Response::HTTP_SEE_OTHER);
    }
}
