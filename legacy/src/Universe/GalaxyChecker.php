<?php declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\Cell\CellRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Planet\Planet;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetSearch;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use EtoA\User\UserRepository;

class GalaxyChecker
{
    public function __construct(
        private UserRepository $userRepository,
        private PlanetRepository $planetRepository,
        private EntityRepository $entityRepository,
        private StarRepository $starRepository,
        private WormholeRepository $wormholeRepository,
        private AsteroidRepository $asteroidRepository,
        private NebulaRepository $nebulaRepository,
        private EmptySpaceRepository $emptySpaceRepository,
        private CellRepository $cellRepository,
    ) {
    }

    /**
     * @return Planet[]
     */
    public function planetsWithInvalidUserId(): array
    {
        $user = $this->userRepository->searchUserNicknames();
        $planets = $this->planetRepository->search(PlanetSearch::create()->assignedToUser());

        $invalidPlanets = [];
        foreach ($planets as $planet) {
            if (!isset($user[$planet->userId])) {
                $invalidPlanets[] = $planet;
            }
        }

        return $invalidPlanets;
    }

    /**
     * @return Planet[]
     */
    public function mainPlanetsWithoutUsers(): array
    {
        return $this->planetRepository->search(PlanetSearch::create()->mainPlanet()->withoutUser());
    }

    /**
     * @return array{users: string[], multiple: array<int, int>, none: int[]}
     */
    public function usersWithInvalidNumberOfMainPlanets(): array
    {
        $user = $this->userRepository->searchUserNicknames();
        $planets = $this->planetRepository->search(PlanetSearch::create()->assignedToUser());

        $userPlanetCounts = [];
        $userHasMainPlanet = [];
        foreach ($planets as $planet) {
            if ($planet->mainPlanet) {
                $userPlanetCounts[$planet->userId] = isset($userPlanetCounts[$planet->userId]) ? $userPlanetCounts[$planet->userId] + 1 : 1;
                $userHasMainPlanet[$planet->userId] = true;
            }
        }

        $usersWithMultiplePlanets = array_filter($userPlanetCounts, fn (int $count) => $count > 1);
        $userWithoutMainPlanet = array_keys(array_diff_key($userPlanetCounts, $userHasMainPlanet));

        return [
            'users' => $user,
            'multiple' => $usersWithMultiplePlanets,
            'none' => $userWithoutMainPlanet,
        ];
    }

    /**
     * @return string[]
     */
    public function invalidEntities(): array
    {
        $entityCodes = $this->entityRepository->getEntityCodes();
        $starIds = $this->starRepository->getAllIds();
        $wormholeIds = $this->wormholeRepository->getAllIds();
        $asteroidIds = $this->asteroidRepository->getAllIds();
        $nebulaIds = $this->nebulaRepository->getAllIds();
        $emptySpaceIds = $this->emptySpaceRepository->getAllIds();
        $planetIds = $this->planetRepository->getAllIds();
        $cellIds = $this->cellRepository->getAllIds();

        $invalidEntities = [];
        $allianceMarketFound = false;
        $marketFound = false;
        foreach ($entityCodes as $entityId => $entityCode) {
            switch ($entityCode) {
                case EntityType::STAR:
                    if (!in_array($entityId, $starIds, true)) {
                        $invalidEntities[] = "Fehlender Detaildatensatz bei Entität " . $entityId . " (Stern)";
                    }

                    break;
                case EntityType::PLANET:
                    if (!in_array($entityId, $planetIds, true)) {
                        $invalidEntities[] = "Fehlender Detaildatensatz bei Entität " . $entityId . " (Planet)";
                    }

                    break;
                case EntityType::ASTEROID:
                    if (!in_array($entityId, $asteroidIds, true)) {
                        $invalidEntities[] = "Fehlender Detaildatensatz bei Entität " . $entityId . " (Asteroidenfeld)";
                    }

                    break;
                case EntityType::NEBULA:
                    if (!in_array($entityId, $nebulaIds, true)) {
                        $invalidEntities[] = "Fehlender Detaildatensatz bei Entität " . $entityId . " (Nebel)";
                    }

                    break;
                case EntityType::WORMHOLE:
                    if (!in_array($entityId, $wormholeIds, true)) {
                        $invalidEntities[] = "Fehlender Detaildatensatz bei Entität " . $entityId . " (Wurmloch)";
                    }

                    break;
                case EntityType::EMPTY_SPACE:
                    if (!in_array($entityId, $emptySpaceIds, true)) {
                        $invalidEntities[] = "Fehlender Detaildatensatz bei Entität " . $entityId . " (Leerer Raum)";
                    }

                    break;
                case EntityType::ALLIANCE_MARKET:
                    if ($allianceMarketFound) {
                        $invalidEntities[] = "Achtung! Entität " . $entityId . " definiert eine Allianzbasis. Allianzbasis wurde schon definiert.";
                    }
                    $allianceMarketFound = true;

                    break;
                case EntityType::MARKET:
                    if ($marketFound) {
                        $invalidEntities[] = "Achtung! Entität " . $entityId . " definiert einen Marktplatz. Marktplatz wurde schon definiert.";
                    }
                    $marketFound = true;

                    break;
                default:
                    $invalidEntities[] = "Achtung! Entität" . $entityId . " hat einen unbekannten Code (" . $entityCode . ")";
            }
        }


        if (count($starIds) > 0) {
            foreach ($starIds as $starId) {
                if (!isset($entityCodes[$starId])) {
                    $invalidEntities[] = "Fehlender Entitätsdatemsatz bei Stern " . $starId;
                } elseif ($entityCodes[$starId] !== EntityType::STAR) {
                    $invalidEntities[] = "Falscher Code (" . $entityCodes[$starId] . ") bei Stern " . $starId . "";
                }
            }
        } else {
            $invalidEntities[] = "Keine Sterne vorhanden!";
        }

        if (count($wormholeIds) > 0) {
            foreach ($wormholeIds as $wormholeId) {
                if (!isset($entityCodes[$wormholeId])) {
                    $invalidEntities[] = "Fehlender Entitätsdatemsatz bei Wurmloch " . $wormholeId;
                } elseif ($entityCodes[$wormholeId] !== EntityType::WORMHOLE) {
                    $invalidEntities[] = "Falscher Code (" . $entityCodes[$wormholeId] . ") bei Wurmloch " . $wormholeId;
                }
            }
        } else {
            $invalidEntities[] = "Keine Wurmlöcher vorhanden!";
        }

        if (count($emptySpaceIds) > 0) {
            foreach ($emptySpaceIds as $emptySpaceId) {
                if (!isset($entityCodes[$emptySpaceId])) {
                    $invalidEntities[] = "Fehlender Entitätsdatemsatz bei leerem Raum " . $emptySpaceId;
                } elseif ($entityCodes[$emptySpaceId] !== EntityType::EMPTY_SPACE) {
                    $invalidEntities[] = "Falscher Code (" . $entityCodes[$emptySpaceId] . ") bei leerem Raum " . $emptySpaceId;
                }
            }
        } else {
            $invalidEntities[] = "Keine leeren Räume vorhanden!";
        }

        foreach ($cellIds as $cellId) {
            if (!isset($entityCodes[$cellId])) {
                $invalidEntities[] = "Fehlende Entität " . $cellId . " bei Zelle " . $cellId;
            }
        }

        return $invalidEntities;
    }
}
