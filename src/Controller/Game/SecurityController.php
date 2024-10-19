<?php declare(strict_types=1);

namespace EtoA\Controller\Game;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\Design;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public function __construct(
        private readonly ConfigurationService $config
    )
    {
    }

    #[Route("/game/login", name: "game.login", methods: ['GET'])]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('game/login/login.html.twig', [
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'lastUsername' => $authenticationUtils->getLastUsername(),
            'templateDir' => '/' . Design::DIRECTORY . '/official/' . $this->config->get('default_css_style'),
            'roundName' => $this->config->get('roundname'),
        ]);
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
