<?php

namespace EtoA\Controller\Game;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefenseController
{
    #[Route('/game/defense', name: 'game.defense')]
    public function list(Request $request): Response
    {
    }
}