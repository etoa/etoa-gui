<?php

namespace EtoA\Controller\Game;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GameTestController extends AbstractGameController
{
    #[Route('/game/test', name: 'game.test')]
    public function __invoke(Request $request): Response
    {
        return $this->render('game/test.html.twig');
    }
}