<?php

namespace EtoA\Controller\External;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class LoginController extends AbstractController
{
    /**
     * The login itself is handled by the game firewall, this route only exists
     * because it is still linked from several places.
     */
    #[Route('/login', name: 'external.login')]
    public function index(): Response
    {
        return $this->redirectToRoute('game.login');
    }
}