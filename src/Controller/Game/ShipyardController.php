<?php

namespace EtoA\Controller\Game;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipyardController
{
    #[Route('/game/shipyard', name: 'game.shipyard')]
    public function list(Request $request): Response
    {
    }
}