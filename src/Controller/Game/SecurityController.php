<?php declare(strict_types=1);

namespace EtoA\Controller\Game;

use EtoA\Controller\AbstractLegacyShowController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractLegacyShowController
{
    #[Route("/game/login", name: "game.login", methods: ['GET'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->handle(function () use ($authenticationUtils) {
            return $this->render('game/login/login.html.twig', [
                'error' => $authenticationUtils->getLastAuthenticationError(),
                'lastUsername' => $authenticationUtils->getLastUsername(),
            ]);
        });
    }

    #[Route("/game/login/check", name: "game.login.check", methods: ['POST'])]
    public function loginCheck(): void
    {
        // Dummy method. Request handled by symfony security
    }

    #[Route("/game/logout", name: "game.logout")]
    public function logout(): void
    {
        // Dummy method. Request handled by symfony security
    }
}
