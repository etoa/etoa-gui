<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Building\BuildingListItemRepository;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Planet;
use EtoA\Ship\ShipRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Bundle\SecurityBundle\Security;

class UserSetupService
{
    private DefaultItemRepository $defaultItemRepository;
    private BuildingListItemRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipRepository $shipRepository;
    private DefenseRepository $defenseRepository;
    private PlanetService $planetService;
    private PlanetRepository $planetRepository;
    private UserService $userService;
    private EntityRepository $entityRepository;
    private Security $security;

    public function __construct(
        DefaultItemRepository      $defaultItemRepository,
        BuildingListItemRepository $buildingRepository,
        TechnologyRepository       $technologyRepository,
        ShipRepository             $shipRepository,
        DefenseRepository          $defenseRepository,
        PlanetService              $planetService,
        PlanetRepository           $planetRepository,
        UserService                $userService,
        EntityRepository           $entityRepository,
        Security                   $security,
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
                $this->buildingRepository->addBuilding($defaultItem->getObjectId(), $defaultItem->getCount(), $userId, $planetId);
            }
        }

        // Add technologies
        if (isset($defaultItems['t'])) {
            foreach ($defaultItems['t'] as $defaultItem) {
                $this->technologyRepository->addTechnology($defaultItem->getObjectId(), $defaultItem->getCount(), $userId, $planetId);
            }
        }

        // Add ships
        if (isset($defaultItems['s'])) {
            foreach ($defaultItems['s'] as $defaultItem) {
                $this->shipRepository->addShip($defaultItem->getObjectId(), $defaultItem->getCount(), $userId, $planetId);
            }
        }

        // Add defense
        if (isset($defaultItems['d'])) {
            foreach ($defaultItems['d'] as $defaultItem) {
                $this->defenseRepository->addDefense($defaultItem->getObjectId(), $defaultItem->getCount(), $userId, $planetId);
            }
        }
    }

    public function coloniseMainPlanet(Planet $planet):void
    {
        $cu = $this->security->getUser();

        $this->planetRepository->reset($planet);
        $this->planetRepository->assignToUser($planet, $cu->getId(), true);
        $this->planetService->setDefaultResources($planet);
        $this->userService->addToUserLog($cu->getId(), "planets", "{nick} wählt [b]" . $planet->getEntity()->toString() . "[/b] als Hauptplanet aus.");
    }
}
