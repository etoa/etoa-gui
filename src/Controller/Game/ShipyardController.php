<?php

namespace EtoA\Controller\Game;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Shipyard\ShipyardService;
use EtoA\Universe\Planet\PlanetRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShipyardController extends AbstractGameController
{
    public function __construct(
        private readonly BuildingListItemRepository $buildingListItemRepository,
        private readonly PlanetRepository           $planetRepository,
        private readonly ShipyardService            $shipyardService
    )
    {
    }

    #[Route('/game/shipyard', name: 'game.shipyard')]
    public function list(Request $request): Response
    {
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $yard = $this->buildingListItemRepository->findOneBy(['building' => BuildingId::SHIPYARD->value, 'entity' => $cp]);

        if (!$yard->isDeactivated()) {
            return $this->render('game/shipyard.list.html.twig', [
                'data' => $this->shipyardService->renderOverview()
            ]);
        }


        return $this->render('game/error.html.twig', [
            'msg' => 'Diese Schiffswerft ist bis ' . date("d.m.Y H:i", $yard->getDeactivated()) . ' deaktiviert.',
            'path' => $this->generateUrl('game.overview'),
            'headline' => 'Gebäude nicht bereit'
        ]);
    }
}