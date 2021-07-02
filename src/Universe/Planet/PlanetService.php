<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

class PlanetService
{
    private PlanetRepository $repository;

    public function __construct(
        PlanetRepository $repository
    ) {
        $this->repository = $repository;
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
