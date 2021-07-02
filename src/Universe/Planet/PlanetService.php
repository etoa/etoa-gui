<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipRepository;

class PlanetService
{
    private PlanetRepository $repository;
    private BuildingRepository $buildingRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;

    public function __construct(
        PlanetRepository $repository,
        BuildingRepository $buildingRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository
    ) {
        $this->repository = $repository;
        $this->buildingRepository = $buildingRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
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

    /**
     * Changes the owner of the planet.
     *
     * Existing buildings will be transferred to the new owner,
     * but ships and defense will be deleted.
     */
    public function changeOwner(int $id, int $newUserId): void
    {
        $this->repository->changeUser($id, $newUserId, 'Unbenannt');
        if ($newUserId > 0) {
            $this->buildingRepository->updateUserForEntity($newUserId, $id);
        } else {
            $this->buildingRepository->removeForEntity($id);
        }
        $this->shipRepository->removeForEntity($id);
        $this->defenseRepository->removeForEntity($id);
    }
}
