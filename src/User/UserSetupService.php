<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Building\BuildingListItemRepository;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Planet;
use EtoA\Entity\User;
use EtoA\Ship\ShipListRepository;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Bundle\SecurityBundle\Security;

class UserSetupService
{
    private DefaultItemRepository $defaultItemRepository;
    private BuildingListItemRepository $buildingRepository;
    private TechnologyListItemRepository $technologyRepository;
    private ShipListRepository $shipListRepository;
    private DefenseRepository $defenseRepository;
    private PlanetService $planetService;
    private PlanetRepository $planetRepository;
    private UserService $userService;
    private Security $security;

    public function __construct(
        DefaultItemRepository        $defaultItemRepository,
        BuildingListItemRepository   $buildingRepository,
        TechnologyListItemRepository $technologyRepository,
        DefenseRepository            $defenseRepository,
        PlanetService                $planetService,
        PlanetRepository             $planetRepository,
        UserService                  $userService,
        EntityRepository             $entityRepository,
        Security                     $security,
        ShipListRepository           $shipListRepository
    ) {
        $this->defaultItemRepository = $defaultItemRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipListRepository = $shipListRepository;
        $this->defenseRepository = $defenseRepository;
        $this->planetRepository = $planetRepository;
        $this->planetService = $planetService;
        $this->userService = $userService;
        $this->security = $security;
    }

    /**
     * Add an item setlist to a given planet
     */
    public function addItemSetListToPlanet(Planet $planet, User $user, int $setId): void
    {
        $defaultItems = $this->defaultItemRepository->getItemsGroupedByCategory($setId);

        // Add buildings
        if (isset($defaultItems['b'])) {
            foreach ($defaultItems['b'] as $defaultItem) {
                $this->buildingRepository->addBuilding($defaultItem->getObjectId(), $defaultItem->getCount(), $user, $planet);
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
                $this->shipListRepository->addShip($defaultItem->getObjectId(), $defaultItem->getCount(), $userId, $planetId);
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
