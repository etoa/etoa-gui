<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseRepository;
use EtoA\Fleet\FleetAction;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipRepository;

class PlanetService
{
    public function __construct(
        private readonly PlanetRepository $repository,
        private readonly BuildingRepository $buildingRepository,
        private readonly ShipRepository $shipRepository,
        private readonly DefenseRepository $defenseRepository,
        private readonly ConfigurationService $config,
        private readonly LogRepository $logRepository,
        private readonly PlanetTypeRepository $planetTypeRepository
    ) {}

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

    public function getAllowedFleetActions(Planet $planet):array {
        $planetType = $this->planetTypeRepository->get($planet->typeId);

        $arr = array();
        if ($planet->getUserId() > 0) {
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
        if ($planet->getUserId() == 0 && $planetType->habitable)
            $arr[] = FleetAction::COLONIZE;
        if ($planet->getWfMetal() || $planet->getWfCrystal() || $planet->getWfPlastic())
            $arr[] = FleetAction::COLLECT_DEBRIS;
        if ($planetType->collectGas) {
            $arr[] = FleetAction::COLLECT_FUEL;
            $arr[] = FleetAction::ANALYZE;
        }
        $arr[] = FleetAction::FLIGHT;
        return $arr;
    }
}
