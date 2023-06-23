<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Building\BuildingRepository;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyRepository;

class UserSetupService
{
    private DefaultItemRepository $defaultItemRepository;
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;

    public function __construct(
        DefaultItemRepository $defaultItemRepository,
        BuildingRepository $buildingRepository,
        TechnologyRepository $technologyRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository
    ) {
        $this->defaultItemRepository = $defaultItemRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
    }

    /**
     * Add an item setlist to a given planet
     */
    public function addItemSetListToPlanet(int $planetId, int $userId, int $setId): void
    {
        $defaultItems = $this->defaultItemRepository->getItemsGroupedByCategory($setId);

        // Add buildings
        if (isset($defaultItems['b'])) {
            foreach ($defaultItems['b'] as $defaultItem) {
                $this->buildingRepository->addBuilding($defaultItem->objectId, $defaultItem->count, $userId, $planetId);
            }
        }

        // Add technologies
        if (isset($defaultItems['t'])) {
            foreach ($defaultItems['t'] as $defaultItem) {
                $this->technologyRepository->addTechnology($defaultItem->objectId, $defaultItem->count, $userId, $planetId);
            }
        }

        // Add ships
        if (isset($defaultItems['s'])) {
            foreach ($defaultItems['s'] as $defaultItem) {
                $this->shipRepository->addShip($defaultItem->objectId, $defaultItem->count, $userId, $planetId);
            }
        }

        // Add defense
        if (isset($defaultItems['d'])) {
            foreach ($defaultItems['d'] as $defaultItem) {
                $this->defenseRepository->addDefense($defaultItem->objectId, $defaultItem->count, $userId, $planetId);
            }
        }
    }
}
