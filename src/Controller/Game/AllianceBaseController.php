<?php

namespace EtoA\Controller\Game;

use EtoA\Controller\Game\AbstractGameController;
use EtoA\UI\ResourceBoxDrawer;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AllianceBaseController extends AbstractGameController
{
    public function __construct(
    )
    {
    }

    #[Route('/game/alliance/base/buildings', name: 'game.alliance.base.buildings')]
    public function buildings(Request $request): Response {
        return $this->render('game/alliance/base/alliance_base.html.twig', [

        ]);
    }

    #[Route('/game/alliance/base/research', name: 'game.alliance.base.research')]
    public function research(Request $request): Response {
        return $this->render('game/alliance/base/alliance_base.html.twig');
    }

    #[Route('/game/alliance/base/storage', name: 'game.alliance.base.storage')]
    public function storage(Request $request): Response {
        return $this->render('game/alliance/base/alliance_base.html.twig');
    }

    #[Route('/game/alliance/base/shipyard', name: 'game.alliance.base.shipyard')]
    public function shipyard(Request $request): Response {
        return $this->render('game/alliance/base/alliance_base.html.twig');
    }
}