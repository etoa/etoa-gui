<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityRepository;

class PlanetService
{
    private PlanetRepository $repository;
    private EntityRepository $entityRepository;

    public function __construct(
        PlanetRepository $repository,
        EntityRepository $entityRepository
    ) {
        $this->repository = $repository;
        $this->entityRepository = $entityRepository;
    }

    /**
     * @return array<int,string>
     */
    public function getUserPlanetNames(int $userId): array
    {
        $data = array();
        foreach ($this->repository->getUserPlanets($userId) as $planet) {
            $data[$planet->id] = filled($planet->name) ? $planet->name : 'Unbenannt';
        }
        return $data;
    }
}
