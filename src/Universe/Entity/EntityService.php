<?php

declare(strict_types=1);

namespace EtoA\Universe\Entity;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserRepository;

class EntityService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;
    private PlanetRepository $planetRepository;
    private StarRepository $starRepository;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepository,
        PlanetRepository $planetRepository,
        StarRepository $starRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
        $this->planetRepository = $planetRepository;
        $this->starRepository = $starRepository;
    }

    public function formattedString(Entity $entity): string
    {
        $str = $entity->toString();

        $extra = [];

        if ($entity->code == EntityType::PLANET) {
            $planet = $this->planetRepository->find($entity->id);
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

        if ($entity->code == EntityType::STAR) {
            $star = $this->starRepository->find($entity->id);
            if (filled($star->name)) {
                $extra[] = $star->name;
            }
        }

        if (count($extra) > 0) {
            $str .= ' (' . implode(', ', $extra) . ')';
        }

        return $str;
    }

    public function distance(Entity $start, Entity $end): float
    {
        return $this->distanceByCoords($start->getCoordinates(), $end->getCoordinates());
    }

    public function distanceByCoords(EntityCoordinates $start, EntityCoordinates $end): float
    {
        // Länge vom Solsys in AE
        $cellLengthAE = $this->config->get('cell_length');
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
}
