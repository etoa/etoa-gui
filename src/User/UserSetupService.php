<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Building\BuildingRepository;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Bundle\SecurityBundle\Security;

class UserSetupService
{
    private DefaultItemRepository $defaultItemRepository;
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;
    private PlanetService $planetService;
    private PlanetRepository $planetRepository;
    private UserService $userService;
    private EntityRepository $entityRepository;
    private Security $security;

    public function __construct(
        DefaultItemRepository $defaultItemRepository,
        BuildingRepository $buildingRepository,
        TechnologyRepository $technologyRepository,
        ShipRepository $shipRepository,
        DefenseRepository $defenseRepository,
        PlanetService $planetService,
        PlanetRepository $planetRepository,
        UserService $userService,
        EntityRepository $entityRepository,
        Security $security,
    ) {
        $this->defaultItemRepository = $defaultItemRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipRepository = $shipRepository;
        $this->defenseRepository = $defenseRepository;
        $this->planetRepository = $planetRepository;
        $this->planetService = $planetService;
        $this->userService = $userService;
        $this->entityRepository = $entityRepository;
        $this->security = $security;
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

    public function coloniseMainPlanet(int $planetId):void
    {
        $cu = $this->security->getUser();

        $this->planetRepository->reset($planetId);
        $this->planetRepository->assignToUser($planetId, $cu->getId(), true);
        $this->planetService->setDefaultResources($planetId);

        $entity = $this->entityRepository->findIncludeCell($planetId);

        $this->userService->addToUserLog($cu->getId(), "planets", "{nick} wählt [b]" . $entity->toString() . "[/b] als Hauptplanet aus.");
    }
}
