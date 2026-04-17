<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingQueueItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Entity\Planet;
use EtoA\Fleet\FleetAction;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipQueueRepository;

class PlanetService
{
    public function __construct(
        private readonly PlanetRepository           $repository,
        private readonly BuildingListItemRepository $buildingRepository,
        private readonly DefenseRepository          $defenseRepository,
        private readonly ConfigurationService       $config,
        private readonly LogRepository              $logRepository,
        private readonly ShipListRepository $shipListRepository,
        private readonly ShipQueueRepository $shipQueueRepository,
        private readonly DefenseQueueRepository $defenseQueueRepository,
        private readonly BuildingQueueItemRepository $buildingQueueItemRepository
    ) {}

    /**
     * @return array<int,string>
     */
    public function getUserPlanetNames(int $userId): array
    {
        $data = array();
        foreach ($this->repository->getUserPlanets($userId) as $planet) {
            $data[$planet->getEntity()->getId()] = $planet->displayName();
        }

        return $data;
    }

    /**
     * Changes the owner of the planet.
     *
     * Existing buildings will be transferred to the new owner,
     * but ships and defense will be deleted.
     */
    public function changeOwner(Planet $planet): void
    {
        $this->repository->changeUser($planet, $planet->getUser(), 'Unbenannt');
        if ($planet->getUser()) {
            $this->buildingRepository->updateUserForEntity($planet->getUser(), $planet);
        } else {
            $this->buildingRepository->removeForEntity($planet);
        }
        $this->shipListRepository->removeForEntity($planet);
        $this->defenseRepository->removeForEntity($planet);
    }

    public function setDefaultResources(Planet $planet): void
    {
        $this->repository->setResources(
            $planet,
            $this->config->getInt('user_start_metal'),
            $this->config->getInt('user_start_crystal'),
            $this->config->getInt('user_start_plastic'),
            $this->config->getInt('user_start_fuel'),
            $this->config->getInt('user_start_food'),
            $this->config->getInt('user_start_people')
        );
    }

    public function reset(Planet $planet): void
    {
        $this->repository->reset($planet);
        $this->shipListRepository->removeForEntity($planet);
        $this->shipQueueRepository->removeForEntity($planet->getEntity());
        $this->defenseRepository->removeForEntity($planet);
        $this->defenseQueueRepository->removeForEntity($planet->getEntity());
        $this->buildingRepository->removeForEntity($planet);
        $this->buildingQueueItemRepository->removeForEntity($planet->getEntity());

        $this->logRepository->add(LogFacility::GALAXY, LogSeverity::INFO, "Der Planet mit der ID " . $planet->getEntity()->getId() . " wurde zurückgesetzt!");
    }

    public function getAllowedFleetActions(Planet $planet):array {
        $planetType = $planet->getPlanetType();

        $arr = array();
        if ($planet->getUser()) {
            $arr[] = FleetAction::TRANSPORT;
            $arr[] = FleetAction::FETCH;
            $arr[] = FleetAction::POSITION;
            $arr[] = FleetAction::ATTACK;
            $arr[] = FleetAction::SPY;
            $arr[] = FleetAction::INVADE;
            $arr[] = FleetAction::SPY_ATTACK;
            $arr[] = FleetAction::STEALTH_ATTACK;
            $arr[] = FleetAction::FAKE_ATTACK;
            $arr[] = FleetAction::BOMBARD;
            $arr[] = FleetAction::ANTRAX;
            $arr[] = FleetAction::GAS_ATTACK;
            $arr[] = FleetAction::CREATE_DEBRIS;
            $arr[] = FleetAction::ALLIANCE;
            $arr[] = FleetAction::SUPPORT;
            $arr[] = FleetAction::MARKET;
            $arr[] = FleetAction::EMP;
        }
        if ($planet->getUser() && $planetType->isHabitable())
            $arr[] = FleetAction::COLONIZE;
        if ($planet->getWfMetal() || $planet->getWfCrystal() || $planet->getWfPlastic())
            $arr[] = FleetAction::COLLECT_DEBRIS;
        if ($planetType->isCollectGas()) {
            $arr[] = FleetAction::COLLECT_FUEL;
            $arr[] = FleetAction::ANALYZE;
        }
        $arr[] = FleetAction::FLIGHT;
        return $arr;
    }

    public function getTotalPeopleWorking(Planet $planet):int
    {
        $people_working = 0;
        foreach ($this->buildingRepository->getPeopleWorking($planet) as $building) {
            $people_working =+ $building->getPeopleWorking();
        }

        return $people_working;
    }
}
