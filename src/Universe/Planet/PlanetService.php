<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipRepository;
use Log;

class PlanetService
{
    private PlanetRepository $repository;
    private BuildingRepository $buildingRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;
    private ConfigurationService $config;

    public function __construct(
        PlanetRepository $repository,
        BuildingRepository $buildingRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository,
        ConfigurationService $config
    ) {
        $this->repository = $repository;
        $this->buildingRepository = $buildingRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
        $this->config = $config;
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

    public function imagePath(Planet $planet, string $opt = ""): string
    {
        defineImagePaths();
        if ($opt == "b") {
            return IMAGE_PATH . "/planets/planet" . $planet->image . "." . IMAGE_EXT;
        }
        if ($opt == "m") {
            return IMAGE_PATH . "/planets/planet" . $planet->image . "_middle." . IMAGE_EXT;
        }

        return IMAGE_PATH . "/planets/planet" . $planet->image . "_small." . IMAGE_EXT;
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

        Log::add(6, Log::INFO, "Der Planet mit der ID " . $id . " wurde zurückgesetzt!");
    }
}
