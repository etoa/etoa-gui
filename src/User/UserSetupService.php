<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Building\BuildingListItemRepository;
use EtoA\DefaultItem\DefaultItemObjectResolver;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Building;
use EtoA\Entity\DefaultItemSet;
use EtoA\Entity\Defense;
use EtoA\Entity\Missile;
use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
use EtoA\Entity\Technology;
use EtoA\Entity\User;
use EtoA\Missile\MissileRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use Symfony\Bundle\SecurityBundle\Security;

class UserSetupService
{
    private DefaultItemRepository $defaultItemRepository;
    private DefaultItemObjectResolver $objectResolver;
    private BuildingListItemRepository $buildingRepository;
    private TechnologyListItemRepository $technologyRepository;
    private ShipListRepository $shipListRepository;
    private DefenseRepository $defenseRepository;
    private MissileRepository $missileRepository;
    private PlanetService $planetService;
    private PlanetRepository $planetRepository;
    private UserService $userService;
    private Security $security;

    public function __construct(
        DefaultItemRepository        $defaultItemRepository,
        DefaultItemObjectResolver    $objectResolver,
        BuildingListItemRepository   $buildingRepository,
        TechnologyListItemRepository $technologyRepository,
        DefenseRepository            $defenseRepository,
        MissileRepository            $missileRepository,
        PlanetService                $planetService,
        PlanetRepository             $planetRepository,
        UserService                  $userService,
        Security                     $security,
        ShipListRepository           $shipListRepository
    ) {
        $this->defaultItemRepository = $defaultItemRepository;
        $this->objectResolver = $objectResolver;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipListRepository = $shipListRepository;
        $this->defenseRepository = $defenseRepository;
        $this->missileRepository = $missileRepository;
        $this->planetRepository = $planetRepository;
        $this->planetService = $planetService;
        $this->userService = $userService;
        $this->security = $security;
    }

    /**
     * Add an item setlist to a given planet
     */
    public function addItemSetListToPlanet(Planet $planet, User $user, DefaultItemSet $set): void
    {
        $defaultItems = $this->defaultItemRepository->getItemsGroupedByCategory($set);



        // Add buildings
        foreach ($defaultItems['b'] ?? [] as $defaultItem) {
            $building = $this->objectResolver->resolve($defaultItem);
            if ($building instanceof Building) {
                $this->buildingRepository->addBuilding($building, $defaultItem->getCount(), $user, $planet);
            }
        }

        // Add technologies
        foreach ($defaultItems['t'] ?? [] as $defaultItem) {
            $technology = $this->objectResolver->resolve($defaultItem);
            if ($technology instanceof Technology) {
                $this->technologyRepository->addTechnology($technology, $defaultItem->getCount(), $user, $planet->getEntity());
            }
        }

        // Add ships
        foreach ($defaultItems['s'] ?? [] as $defaultItem) {
            $ship = $this->objectResolver->resolve($defaultItem);
            if ($ship instanceof Ship) {
                $this->shipListRepository->addShip($ship, $defaultItem->getCount(), $user, $planet);
            }
        }

        // Add defense
        foreach ($defaultItems['d'] ?? [] as $defaultItem) {
            $defense = $this->objectResolver->resolve($defaultItem);
            if ($defense instanceof Defense) {
                $this->defenseRepository->addDefense($defense, $defaultItem->getCount(), $user, $planet);
            }
        }

        // Add missiles
        foreach ($defaultItems['m'] ?? [] as $defaultItem) {
            $missile = $this->objectResolver->resolve($defaultItem);
            if ($missile instanceof Missile) {
                $this->missileRepository->addMissile($missile, $defaultItem->getCount(), $user, $planet);
            }
        }
    }

    public function coloniseMainPlanet(Planet $planet):void
    {
        $cu = $this->security->getUser();

        $this->planetRepository->reset($planet);
        $this->planetRepository->assignToUser($planet, $cu->getData(), true);
        $this->planetService->setDefaultResources($planet);
        $this->userService->addToUserLog($cu->getData(), "planets", "{nick} wählt [b]" . $planet->getEntity()->toString() . "[/b] als Hauptplanet aus.");
    }
}
