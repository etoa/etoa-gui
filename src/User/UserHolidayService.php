<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Backend\BackendMessageService;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Entity\User;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Fleet\FleetStatus;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;

class UserHolidayService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;
    private BuildingListItemRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipQueueRepository $shipQueueRepository;
    private DefenseQueueRepository $defenseQueueRepository;
    private PlanetRepository $planetRepository;
    private FleetRepository $fleetRepository;
    private BackendMessageService $backendMessageService;

    public function __construct(ConfigurationService $config, UserRepository $userRepository, BuildingListItemRepository $buildingRepository, TechnologyRepository $technologyRepository, ShipQueueRepository $shipQueueRepository, DefenseQueueRepository $defenseQueueRepository, PlanetRepository $planetRepository, FleetRepository $fleetRepository, BackendMessageService $backendMessageService)
    {
        $this->config = $config;
        $this->userRepository = $userRepository;
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->shipQueueRepository = $shipQueueRepository;
        $this->defenseQueueRepository = $defenseQueueRepository;
        $this->planetRepository = $planetRepository;
        $this->fleetRepository = $fleetRepository;
        $this->backendMessageService = $backendMessageService;
    }

    public function activateHolidayMode(User $user, bool $force = false): bool
    {
        $userId = $user->getId();
        // Prevent umode if user has any fleet in the air
        if ($this->fleetRepository->exists(FleetSearch::create()->user($userId)) && !$force) {
            return false;
        }

        // Prevent umode if foreign fleets are approaching one of the users planets
        $foreignFleetSearch = FleetSearch::create()
            ->notUser($userId)
            ->planetUser($userId)
            ->status(FleetStatus::DEPARTURE)
            ->actionNotIn([FleetAction::COLLECT_DEBRIS, FleetAction::EXPLORE, FleetAction::FLIGHT, FleetAction::CREATE_DEBRIS]);
        if ($this->fleetRepository->exists($foreignFleetSearch) && !$force) {
            return false;
        }

        $holidayFrom = time();
        $holidayTo = $holidayFrom + ($this->config->getInt('hmode_days') * 24 * 3600);

        $this->shipQueueRepository->freezeConstruction($userId);
        $this->defenseQueueRepository->freezeConstruction($userId);
        $this->buildingRepository->freezeConstruction($userId);
        $this->technologyRepository->freezeConstruction($userId);
        $this->planetRepository->freezeProduction($userId);

        $user->setHmodFrom($holidayFrom);
        $user->setHmodTo($holidayTo);
        $user->setLogoutTime($holidayFrom);

        $this->userRepository->save($user);

        return true;
    }

    public function deactivateHolidayMode(User $user, bool $force = false): bool
    {
        $now = time();
        if ($user->getHmodFrom() === 0 || (($user->getHmodFrom() > $now || $user->getHmodTo() > $now) && !$force)) {
            return false;
        }

        $holidayTime = $now - $user->getHmodFrom();

        $this->shipQueueRepository->unfreezeConstruction($user->getId(), $holidayTime);
        $this->defenseQueueRepository->unfreezeConstruction($user->getId(), $holidayTime);
        $this->buildingRepository->unfreezeConstruction($user->getId(), $holidayTime);
        $this->technologyRepository->unfreezeConstruction($user->getId(), $holidayTime);
        $this->planetRepository->freezeProduction($user->getId());

        $user->setSpecialistTime($user->getSpecialistTime()+$holidayTime);
        $user->setHmodFrom(0);
        $user->setHmodTo(0);
        $user->setLogoutTime(time());

        $this->userRepository->save($user);

        $userPlanets = $this->planetRepository->getUserPlanets($user->getId());
        foreach ($userPlanets as $planet) {
            $this->planetRepository->setLastUpdated($planet->getId(), time());
            $this->backendMessageService->updatePlanet($planet->getId());
        }

        return true;
    }

    public function setUmodeToInactive(): int
    {
        $threshold = time() - ($this->config->param1Int('hmode_days') * 86400);
        $users = $this->userRepository->findInactiveInHolidayMode($threshold);
        foreach ($users as $user) {
            $this->deactivateHolidayMode($user);
        }

        return count($users);
    }
}
