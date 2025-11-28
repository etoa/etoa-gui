<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\Routing\Annotation\Route;

class BuildingsController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingService $buildingService,
        private readonly PlanetRepository $planetRepository
    )
    {
    }

    #[Route('/game/buildings', name: 'game.buildings')]
    public function list(Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        //only allow own planets
        if($planet->getUser() !== $this->getUser()->getData()) {
            return $this->redirectToRoute('game.overview');
        }
        return $this->render('game/buildings/list.html.twig', [
            'planet' => $planet,
            'render' => $this->buildingService->renderBuilding()
        ]);
    }
}