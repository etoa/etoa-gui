<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Backend\BackendMessageService;
use EtoA\Building\BuildingRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseQueueRepository;
use EtoA\Defense\DefenseQueueSearch;
use EtoA\Fleet\FleetAction;
use EtoA\Fleet\FleetRepository;
use EtoA\Fleet\FleetSearch;
use EtoA\Fleet\FleetStatus;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;

class UserHolidayService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private ShipQueueRepository $shipQueueRepository;
    private DefenseQueueRepository $defenseQueueRepository;
    private PlanetRepository $planetRepository;
    private FleetRepository $fleetRepository;
    private BackendMessageService $backendMessageService;

    public function __construct(ConfigurationService $config, UserRepository $userRepository, BuildingRepository $buildingRepository, TechnologyRepository $technologyRepository, ShipQueueRepository $shipQueueRepository, DefenseQueueRepository $defenseQueueRepository, PlanetRepository $planetRepository, FleetRepository $fleetRepository, BackendMessageService $backendMessageService)
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

    public function activateHolidayMode(int $userId, bool $force = false): bool
    {
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

        $this->userRepository->activateHolidayMode($userId, $holidayFrom, $holidayTo);

        return true;
    }

    public function setUmodeToInactive(): int
    {
        $threshold = time() - ($this->config->param1Int('hmode_days') * 86400);
        $users = $this->userRepository->findInactiveInHolidayMode($threshold);
        foreach ($users as $user) {
            $hmodTime = time() - $user->hmodFrom;

            $this->userRepository->disableHolidayMode($user->id);

            $newLogoutTime = time() - ($this->config->param2Int('user_inactive_days') * 86400);
            $this->userRepository->setLogoutTime($user->id, $newLogoutTime);

            $buildingItems = $this->buildingRepository->findForUser($user->id);
            foreach ($buildingItems as $item) {
                if ($item->startTime == 0 || $item->endTime == 0) {
                    continue;
                }
                $item->buildType = 3;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->buildingRepository->save($item);
            }

            $technologyItems = $this->technologyRepository->findForUser($user->id);
            foreach ($technologyItems as $item) {
                if ($item->startTime == 0 || $item->endTime == 0) {
                    continue;
                }
                $item->buildType = 3;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->technologyRepository->save($item);
            }

            $shipQueueItems = $this->shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->userId($user->id));
            foreach ($shipQueueItems as $item) {
                $item->buildType = 0;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->shipQueueRepository->saveQueueItem($item);
            }

            $defQueueItems = $this->defenseQueueRepository->searchQueueItems(DefenseQueueSearch::create()->userId($user->id));
            foreach ($defQueueItems as $item) {
                $item->buildType = 0;
                $item->startTime += $hmodTime;
                $item->endTime += $hmodTime;
                $this->defenseQueueRepository->saveQueueItem($item);
            }

            $this->userRepository->addSpecialistTime($user->id, $hmodTime);

            $userPlanets = $this->planetRepository->getUserPlanets($user->id);
            foreach ($userPlanets as $planet) {
                $this->planetRepository->setLastUpdated($planet->id, time());
                $this->backendMessageService->updatePlanet($planet->id);
            }
        }

        return count($users);
    }
}
