<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipRepository;

class PlanetService
{
    private PlanetRepository $repository;
    private BuildingRepository $buildingRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;
    private ConfigurationService $config;
    private LogRepository $logRepository;

    public function __construct(
        PlanetRepository $repository,
        BuildingRepository $buildingRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository,
        ConfigurationService $config,
        LogRepository $logRepository
    ) {
        $this->repository = $repository;
        $this->buildingRepository = $buildingRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
        $this->config = $config;
        $this->logRepository = $logRepository;
    }

    /**
     * @return array<int,string>
     */
    public function getUserPlanetNames(int $userId): array
    {
        $data = array();
        foreach ($this->repository->getUserPlanets($userId) as $planet) {
            $data[$planet->id] = $planet->displayName();
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

    public function setDefaultResources(int $id): void
    {
        $this->repository->setResources(
            $id,
            $this->config->getInt('user_start_metal'),
            $this->config->getInt('user_start_crystal'),
            $this->config->getInt('user_start_plastic'),
            $this->config->getInt('user_start_fuel'),
            $this->config->getInt('user_start_food'),
            $this->config->getInt('user_start_people')
        );
    }

    public function reset(int $id): void
    {
        if ($id == 0) {
            return;
        }

        $this->repository->reset($id);
        $this->shipRepository->removeForEntity($id);
        $this->defenseRepository->removeForEntity($id);
        $this->buildingRepository->removeForEntity($id);

        $this->logRepository->add(LogFacility::GALAXY, LogSeverity::INFO, "Der Planet mit der ID " . $id . " wurde zurückgesetzt!");
    }
}
