<?php

namespace EtoA\Controller\Game;

use EtoA\Fleet\FleetRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Ship\ShipSort;
use EtoA\Support\StringUtils;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FleetStatController extends AbstractGameController
{


    public function __construct(
        private readonly ShipListRepository  $shipListRepository,
        private readonly ShipQueueRepository $shipQueueRepository,
        private readonly FleetRepository     $fleetRepository,
        private readonly PlanetService       $planetService,
        private readonly ShipDataRepository $shipDataRepository
    )
    {
    }

    #[Route('/game/fleetstats', name: 'game.fleetstats')]
    public function list(): Response
    {
        $cu = $this->getUser()->getData();
        $shiplist = $this->shipListRepository->findForUser($cu);
        
        if (count($shiplist) === 0) {
            return $this->render('game/error.html.twig',[
                'msg' => 'Es sind noch keine Schiffe vorhanden!',
                'path' => $this->generateUrl('game.overview'),
                'headline' => 'Schiffsübersicht'
            ]);
        }

        // Speichert Planetnamen in ein Array
        $planetData = $this->planetService->getUserPlanetNames($cu);

        // Speichert alle Schiffe des Users, welche auf den Planeten stationiert sind
        $shiplistData = [];
        $shiplistBunkered = [];
        foreach ($shiplist as $item) {
            $shipId = $item->getShip()->getId();
            $entityId = $item->getEntity()->getEntity()->getId();
            $shiplistData[$shipId][$entityId] = $item->getCount();
            $shiplistBunkered[$shipId][$entityId] = $item->getBunkered();
        }

        // Speichert alle Schiffe des Users, die sich im Bau befinden
        $queueData = [];
        $queuelist = $this->shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->userId($cu->getId()));
        foreach ($queuelist as $item) {
            $shipId = $item->getShip()->getId();
            $entityId = $item->getEntity()->getEntity()->getId();
            $queueData[$shipId][$entityId] = $item->getCount();
        }

        // Speichert alle Schiffe des Users, die sich im All befinden
        $fleetData = $this->fleetRepository->getUserFleetShipCounts($cu);

        // Prepare ship statistics
        $shipStats = [];
        $ships = $this->shipDataRepository->searchShips(null, ShipSort::specialWithUserSort('name', 'ASC'));
        
        foreach ($ships as $ship) {
            $shipId = $ship->getId();
            
            // Calculate totals
            $orbitTotal = isset($shiplistData[$shipId]) ? array_sum($shiplistData[$shipId]) : 0;
            $bunkeredTotal = isset($shiplistBunkered[$shipId]) ? array_sum($shiplistBunkered[$shipId]) : 0;
            $queueTotal = isset($queueData[$shipId]) ? array_sum($queueData[$shipId]) : 0;
            $fleetTotal = $fleetData[$shipId] ?? 0;
            
            // Skip if no ships of this type exist
            if ($orbitTotal === 0 && $bunkeredTotal === 0 && $queueTotal === 0 && $fleetTotal === 0) {
                continue;
            }
            
            // Build planet breakdown for orbit ships
            $orbitDetails = '';
            if (isset($shiplistData[$shipId])) {
                foreach ($planetData as $planetId => $planetName) {
                    if (($shiplistData[$shipId][$planetId] ?? 0) > 0) {
                        $orbitDetails .= "<b>" . $planetName . "</b>: " . StringUtils::formatNumber( $shiplistData[$shipId][$planetId]) . "<br>";
                    }
                }
            }

            // Build planet breakdown for bunkered ships
            $bunkeredDetails = '';
            if (isset($shiplistBunkered[$shipId])) {
                foreach ($planetData as $planetId => $planetName) {
                    if (($shiplistBunkered[$shipId][$planetId] ?? 0) > 0) {
                        $bunkeredDetails .= "<b>" . $planetName . "</b>: " . StringUtils::formatNumber($shiplistBunkered[$shipId][$planetId]) . "<br>";
                    }
                }
            }
            
            // Build planet breakdown for queue ships
            $queueDetails = [];
            if (isset($queueData[$shipId])) {
                foreach ($planetData as $planetId => $planetName) {
                    if (($queueData[$shipId][$planetId] ?? 0) > 0) {
                        $queueDetails[] = [
                            'name' => $planetName,
                            'count' => $queueData[$shipId][$planetId]
                        ];
                    }
                }
            }
            
            $shipStats[] = [
                'ship' => $ship,
                'orbitTotal' => $orbitTotal,
                'orbitDetails' => $orbitDetails,
                'bunkeredTotal' => $bunkeredTotal,
                'bunkeredDetails' => $bunkeredDetails,
                'queueTotal' => $queueTotal,
                'queueDetails' => $queueDetails,
                'fleetTotal' => $fleetTotal
            ];
        }

        return $this->render('game/fleetstats/list.html.twig', [
            'shipStats' => $shipStats
        ]);
    }
}