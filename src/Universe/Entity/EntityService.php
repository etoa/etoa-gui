<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Entity;
use EtoA\Universe\Asteroid\AsteroidRepository;
use EtoA\Universe\EmptySpace\EmptySpaceRepository;
use EtoA\Universe\Nebula\NebulaRepository;
use EtoA\Universe\Others\AllianceMarket;
use EtoA\Universe\Others\Market;
use EtoA\Universe\Others\UnknownEntity;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Star\StarRepository;
use EtoA\Universe\Wormhole\WormholeRepository;
use EtoA\User\UserRepository;

class EntityService
{
    public function __construct(
        private readonly ConfigurationService $config,
        private readonly UserRepository $userRepository,
        private readonly PlanetRepository $planetRepository,
        private readonly StarRepository $starRepository,
        private readonly AsteroidRepository $asteroidRepository,
        private readonly NebulaRepository $nebulaRepository,
        private readonly WormholeRepository $wormholeRepository,
        private readonly EmptySpaceRepository $emptySpaceRepository,
        private readonly AllianceRepository $allianceRepository,
        private readonly PlanetService $planetService
    ){}

    public function formattedString(Entity $entity): string
    {
        $str = $entity->toString();

        $extra = [];

        if ($entity->getCode() == EntityType::PLANET) {
            $planet = $this->planetRepository->find($entity->getId());
            if (filled($planet->name)) {
                $extra[] = $planet->name;
            }
            if ($planet->userId > 0) {
                $nick = $this->userRepository->getNick($planet->userId);
                if (filled($nick)) {
                    $extra[] = $nick;
                }
            }
        }

        if ($entity->getCode() == EntityType::STAR) {
            $star = $this->starRepository->find($entity->getId());
            if (filled($star->name)) {
                $extra[] = $star->name;
            }
        }

        if (count($extra) > 0) {
            $str .= ' (' . implode(', ', $extra) . ')';
        }

        return $str;
    }

    public function distance(?Entity $start, ?Entity $end): float
    {
        $startCoordinates = $start !== null ? $start->getCoordinates() : new EntityCoordinates(0, 0, 0, 0, 0);
        $endCoordinates = $end !== null ? $end->getCoordinates() : new EntityCoordinates(0, 0, 0, 0, 0);

        return $this->distanceByCoords($startCoordinates, $endCoordinates);
    }

    public function distanceByCoords(EntityCoordinates $start, EntityCoordinates $end): float
    {
        // Länge vom Solsys in AE
        $cellLengthAE = $this->config->getInt('cell_length');
        // Max. Planeten im Solsys
        $maxNumEntitiesPerSystem = $this->config->param2Int('num_planets');

        // Number of cells per sector
        $cellsPerSectorX = $this->config->param1Int('num_of_cells');
        $cellsPerSectorY = $this->config->param2Int('num_of_cells');

        // Absolute coordinates of current cell
        $cAbsX = (($start->sx - 1) * $cellsPerSectorX) + $start->cx;
        $cAbsY = (($start->sy - 1) * $cellsPerSectorY) + $start->cy;

        // Absolute coordinates of target cell
        $tAbsX = (($end->sx - 1) * $cellsPerSectorX) + $end->cx;
        $tAbsY = (($end->sy - 1) * $cellsPerSectorY) + $end->cy;

        // Entity position in cell (Planet position in sol system)
        $p1 = $start->pos;
        $p2 = $end->pos;

        // Get difference on x axis in absolute coordinates
        $dx = abs($tAbsX - $cAbsX);
        // Get difference on y axis in absolute coordinates
        $dy = abs($tAbsY - $cAbsY);
        // Use Pythagorean theorem to get the absolute length
        $hypotenuse = sqrt(($dx ** 2) + ($dy ** 2));
        // Multiply with AE units per cell
        $cellDistanceAE = $hypotenuse * $cellLengthAE;

        // The distance between the innermost and outermost possible entity in the system
        // The outermost entity lies at half distance to the cell edge
        $distanceInnerOuterEntity = $cellLengthAE / 4 / $maxNumEntitiesPerSystem;

        // Planetendistanz wenn sie im selben Solsys sind
        if ($cellDistanceAE == 0) {
            $finalDistance = abs($p2 - $p1) * $distanceInnerOuterEntity;
        }
        // Planetendistanz wenn sie nicht im selben Solsys sind
        else {
            $finalDistance = $cellDistanceAE + $cellLengthAE - ($distanceInnerOuterEntity * ($p1 + $p2));
        }

        return round($finalDistance);
    }

    public function getEntity(Entity $entity)
    {
        switch ($entity->getCode())
        {
            case EntityType::STAR:
                return $this->starRepository->find($entity->getId());
            case EntityType::PLANET:
                $planet = $this->planetRepository->find($entity->getId());
                $planet->setAllowedFleetActions($this->planetService->getAllowedFleetActions($planet));
                return $planet;
            case EntityType::ASTEROID:
                return $this->asteroidRepository->find($entity->getId());
            case EntityType::NEBULA:
                return $this->nebulaRepository->find($entity->getId());
            case EntityType::WORMHOLE:
                return $this->wormholeRepository->find($entity->getId());
            case EntityType::EMPTY_SPACE:
                return $this->emptySpaceRepository->find($entity->getId());
            case EntityType::MARKET:
                return new Market();
            case EntityType::ALLIANCE_MARKET:
                return new AllianceMarket();
            default:
                return new UnknownEntity();
        }
    }
}
