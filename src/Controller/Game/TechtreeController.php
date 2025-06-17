<?php

namespace EtoA\Controller\Game;

use EtoA\Controller\Game\AbstractGameController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechtreeController extends AbstractGameController
{
    #[Route('/game/techtree', name: 'game.techtree')]
    public function overview(): Response {
        return $this->render('game/techtree/techtree.html.twig');
    }
}