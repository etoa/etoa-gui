<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingService;
use EtoA\Entity\Building;
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
        if ($planet->getUser() !== $this->getUser()->getData()) {
            return $this->redirectToRoute('game.overview');
        }

        $buildingData = $this->buildingService->getBuildingsData();

        return $this->render('game/buildings/list.html.twig', [
            'planet' => $planet,
            'buildingData' => $buildingData,
        ]);
    }

    #[Route('/game/buildings/{id}', name: 'game.buildings.show')]
    public function show(?Building $building, Request $request): Response
    {
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));

        //check building and only allow own planets
        if (!$building || !$building->isShow() || $planet->getUser() !== $this->getUser()->getData()) {
            return $this->redirectToRoute('game.buildings');
        }

        $buildingData = $this->buildingService->getBuildingsData();

        return $this->render('game/buildings/show.html.twig', [
            'planet' => $planet,
            'building' => $building,
            'buildingData' => $buildingData,
        ]);
    }
}