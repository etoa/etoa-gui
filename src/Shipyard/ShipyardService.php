<?php

namespace EtoA\Shipyard;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Building;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Planet;
use EtoA\Entity\Ship;
use EtoA\Entity\Specialist;
use EtoA\Fleet\FleetShipRepository;
use EtoA\Ship\ShipCategoryRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipQueueRepository;
use EtoA\Ship\ShipQueueSearch;
use EtoA\Ship\ShipSearch;
use EtoA\Ship\ShipSort;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\PreciseResources;
use Symfony\Component\HttpFoundation\RequestStack;

class ShipyardService
{
    public function __construct(
        private readonly ShipQueueRepository          $shipQueueRepository,
        private readonly PlanetRepository             $planetRepository,
        private readonly RequestStack                 $requestStack,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly ConfigurationService         $config,
        private readonly ShipCategoryRepository       $shipCategoryRepository,
        private readonly ShipDataRepository           $shipDataRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly ShipListRepository           $shipListRepository,
        private readonly FleetShipRepository          $fleetShipRepository
    )
    {
    }

    public function getShipyardData(): array
    {
        $cp = $this->getCurrentPlanet();
        $shipyard = $this->getShipyard();
        $shipCategories = $this->shipCategoryRepository->getAllCategories();
        $shipsByCategory = [];
        $properties = $cp->getUser()->getUserProperties();
        $specialist = $cp->getUser()->getSpecialist();
        $gen_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::GEN, 'user' => $cp->getUser()])?->getCurrentLevel() ?? 0;
        $time_boni_factor = $this->getTimeBoniFactor();

        $shipCosts = [];
        foreach ($this->getShips() as $ship) {
            $shipsByCategory[$ship->getCat()->getId()][] = $ship;
            $shipCosts[$ship->getId()] = PreciseResources::createFromBase($ship->getCosts())->multiply($specialist ? $specialist->getCostsShips() : 1);
        }

        $shipQueueItems = $this->getShipQueueItems();
        $queueData = $this->buildQueueData($shipQueueItems);

        $categoriesData = $this->buildCategoriesData(
            $shipCategories,
            $shipsByCategory,
            $shipCosts,
            $cp,
            $shipyard,
            $time_boni_factor,
            $gen_tech_level,
            $specialist,
            count($shipQueueItems) > 0
        );

        return [
            'queue' => $queueData,
            'categories' => $categoriesData,
            'compactView' => $properties->getItemShow() !== 'full',
            'hasShips' => count($categoriesData) > 0,
        ];
    }

    private function buildQueueData(array $shipQueueItems): array
    {
        if (count($shipQueueItems) === 0) {
            return [];
        }

        $queueData = [];
        $first = true;
        $absolute_starttime = 0;
        $currentItem = null;
        $time = time();

        foreach ($shipQueueItems as $data) {
            if ($first) {
                $obj_t_remaining = ((($data->getEndTime() - $time) / $data->getObjectTime()) - floor(($data->getEndTime() - $time) / $data->getObjectTime())) * $data->getObjectTime();
                if ($obj_t_remaining == 0) {
                    $obj_t_remaining = $data->getObjectTime();
                }

                $absolute_starttime = $data->getStartTime();
                $obj_t_passed = $data->getObjectTime() - $obj_t_remaining;

                $currentItem = [
                    'name' => $data->getShip()->getName(),
                    'startTime' => $time - $obj_t_passed,
                    'endTime' => $time + $obj_t_remaining,
                    'remaining' => $obj_t_remaining,
                ];

                $first = false;
            }

            $queueData[] = [
                'id' => $data->getId(),
                'count' => $data->getCount(),
                'name' => $data->getShip()->getName(),
                'startTime' => $absolute_starttime,
                'endTime' => $absolute_starttime + $data->getEndTime() - $data->getStartTime(),
                'remaining' => $data->getEndTime() - $time,
                'cancelable' => $this->getCancelResFactor() > 0,
            ];

            $absolute_starttime = $data->getEndTime();
        }

        return [
            'current' => $currentItem,
            'items' => $queueData,
        ];
    }

    private function buildCategoriesData(
        array $shipCategories,
        array $shipsByCategory,
        array $shipCosts,
              $cp,
              $shipyard,
        float $time_boni_factor,
        int   $gen_tech_level,
              $specialist,
        bool  $hasQueue
    ): array
    {
        $categoriesData = [];

        foreach ($shipCategories as $category) {
            if (!isset($shipsByCategory[$category->getId()])) {
                continue;
            }

            $categoryShips = [];

            foreach ($shipsByCategory[$category->getId()] as $shipData) {
                $shipInfo = $this->buildShipData(
                    $shipData,
                    $shipCosts[$shipData->getId()],
                    $cp,
                    $shipyard,
                    $time_boni_factor,
                    $gen_tech_level,
                    $specialist,
                    $hasQueue
                );

                if ($shipInfo !== null) {
                    $categoryShips[] = $shipInfo;
                }
            }

            if (count($categoryShips) > 0) {
                $categoriesData[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'ships' => $categoryShips,
                ];
            }
        }

        return $categoriesData;
    }

    private function buildShipData(
        Ship             $shipData,
        PreciseResources $costs,
        Planet           $cp,
        BuildingListItem $shipyard,
        float            $time_boni_factor,
        int              $gen_tech_level,
        ?Specialist      $specialist,
        bool             $hasQueue
    ): ?array
    {
        if (!$this->requirementsPassed($shipData))
            return null;

        $btime = ($costs->getSum()) / $this->config->getInt('global_time') * $this->config->getFloat('ship_build_time') * $time_boni_factor * ($specialist ? $specialist->getTimeShips() : 1);
        $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
        $peopleOptimized = ceil(($btime - $btime_min) / $this->config->getInt('people_work_done'));

        if ($btime_min < $this->config->getInt('shipyard_min_build_time')) {
            $btime_min = $this->config->getInt('shipyard_min_build_time');
        }

        $btime = ceil($btime - $shipyard->getPeopleWorking() * $this->config->getInt('people_work_done'));
        if ($btime < $btime_min) {
            $btime = $btime_min;
        }

        $food_costs = $shipyard->getPeopleWorking() * $this->config->getInt('people_food_require') + $costs->food;

        $build_cnt_metal = $costs->metal > 0 ? floor($cp->getResMetal() / $costs->metal) : PHP_INT_MAX;
        $build_cnt_crystal = $costs->crystal > 0 ? floor($cp->getResCrystal() / $costs->crystal) : PHP_INT_MAX;
        $build_cnt_plastic = $costs->plastic > 0 ? floor($cp->getResPlastic() / $costs->plastic) : PHP_INT_MAX;
        $build_cnt_fuel = $costs->fuel > 0 ? floor($cp->getResFuel() / $costs->fuel) : PHP_INT_MAX;
        $build_cnt_food = $food_costs > 0 ? floor($cp->getResFood() / $food_costs) : PHP_INT_MAX;

        $ship_count = $this->getAllShipCount($shipData);
        $max_cnt = $shipData->getMaxCount() !== 0 ? $shipData->getMaxCount() - $ship_count : PHP_INT_MAX;

        $ship_max_build = min($build_cnt_metal, $build_cnt_crystal, $build_cnt_plastic, $build_cnt_fuel, $build_cnt_food, $max_cnt);

        $waitTimes = $this->calculateWaitTimes($costs, $food_costs, $cp);

        return [
            'id' => $shipData->getId(),
            'name' => $shipData->getName(),
            'image' => $shipData->getImagePath('small'),
            'imageMedium' => $shipData->getImagePath('medium'),
            'shortComment' => $shipData->getShortComment(),
            'special' => $shipData->isSpecial(),
            'buildTime' => $btime,
            'peopleOptimized' => $peopleOptimized,
            'costs' => [
                'metal' => $costs->metal,
                'crystal' => $costs->crystal,
                'plastic' => $costs->plastic,
                'fuel' => $costs->fuel,
                'food' => $food_costs,
            ],
            'available' => [
                'metal' => $cp->getResMetal(),
                'crystal' => $cp->getResCrystal(),
                'plastic' => $cp->getResPlastic(),
                'fuel' => $cp->getResFuel(),
                'food' => $cp->getResFood(),
            ],
            'maxBuildable' => $ship_max_build,
            'shipCount' => $ship_count,
            'maxCount' => $shipData->getMaxCount(),
            'waitTimes' => $waitTimes,
            'hasQueue' => $hasQueue,
        ];
    }

    private function calculateWaitTimes(PreciseResources $costs, float $food_costs, $cp): array
    {
        $waitTimes = [];

        if ($cp->getProdMetal() > 0 && $costs->metal > $cp->getResMetal()) {
            $waitTimes['metal'] = ceil(($costs->metal - $cp->getResMetal()) / $cp->getProdMetal() * 3600);
        }

        if ($cp->getProdCrystal() > 0 && $costs->crystal > $cp->getResCrystal()) {
            $waitTimes['crystal'] = ceil(($costs->crystal - $cp->getResCrystal()) / $cp->getProdCrystal() * 3600);
        }

        if ($cp->getProdPlastic() > 0 && $costs->plastic > $cp->getResPlastic()) {
            $waitTimes['plastic'] = ceil(($costs->plastic - $cp->getResPlastic()) / $cp->getProdPlastic() * 3600);
        }

        if ($cp->getProdFuel() > 0 && $costs->fuel > $cp->getResFuel()) {
            $waitTimes['fuel'] = ceil(($costs->fuel - $cp->getResFuel()) / $cp->getProdFuel() * 3600);
        }

        if ($cp->getProdFood() > 0 && $food_costs > $cp->getResFood()) {
            $waitTimes['food'] = ceil(($food_costs - $cp->getResFood()) / $cp->getProdFood() * 3600);
        }

        return $waitTimes;
    }

    private function requirementsPassed(Ship $ship): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        foreach ($ship as $requirement) {
            if ($requirement->getBuilding() && $requirement->getLevel() > $this->buildingListItemRepository->findOneBy(['building' => $requirement->getBuilding(), 'entity' => $cp])?->getCurrentLevel()) {
                return false;
            }

            if ($requirement->getTech() && $requirement->getLevel() > $this->technologyListItemRepository->findOneBy(['user' => $cp->getUser(), 'technology' => $requirement->getTech()])?->getCurrentLevel()) {
                return false;
            }
        }

        if ($ship->isSpecial() && !$cp->isMainPlanet()) {
            return false;
        }

        return true;
    }

    private function getShips(): ?array
    {
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $properties = $cp->getUser()->getUserproperties();
        $shipSearch = ShipSearch::create()->buildable()->raceOrNull($cp->getUser()->getRace());
        $shipOrder = ShipSort::specialWithUserSort($properties->getItemOrderShip(), $properties->getItemOrderWay());

        return $this->shipDataRepository->searchShips($shipSearch, $shipOrder);
    }

    public function getAllShipCount(Ship|int $ship):int
    {
        // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        $ship_count = 0;
        // ... auf den Planeten
        $shiplist = $this->shipListRepository->findOneBy(['entity' => $cp, 'ship' => $ship]);
        if ($shiplist) {
            $ship_count += $shiplist->getCount();
        }
        $queuelist = $this->shipQueueRepository->findBy(['entity' => $cp, 'ship' => $ship]);
        // ... in der Bauliste
        foreach ($queuelist as $item) {
            $ship_count += $item->getCount();
        }

        $fleetlist = $this->fleetShipRepository->getByUserAndShip($cp->getUser(), $ship);
        // ... in der Luft
        foreach ($fleetlist as $item) {
            $ship_count += $item->getCount();
        }

        return $ship_count;
    }

    private function getShipyard():?BuildingListItem
    {
        return $this->buildingListItemRepository->findOneBy(['building' => BuildingId::SHIPYARD->value, 'entity' => $this->getCurrentPlanet()]);
    }

    public function getCurrentPlanet():Planet
    {
        $request = $this->requestStack->getCurrentRequest();
        return $this->planetRepository->find($request->getSession()->get('cpid'));
    }

    private function getShipQueueItems():array
    {
        return $this->shipQueueRepository->searchQueueItems(ShipQueueSearch::create()->entityId($this->getCurrentPlanet())->endAfter(time()));
    }

    private function getTimeBoniFactor(): float|int
    {
        $need_bonus_level = $this->getShipyard()?->getCurrentLevel() - $this->config->param1Int('build_time_boni_schiffswerft');

        if ($need_bonus_level <= 0) {
            return 1;
        } else {
            return 1 - ($need_bonus_level * ($this->config->getInt('build_time_boni_schiffswerft') / 100));
        }
    }

    // Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
    public function getCancelResFactor():float|int
    {
        $shipyard = $this->getShipyard();
        $minLevel = $this->config->getInt('shipqueue_cancel_min_level');
        $cancelEnd = $this->config->getFloat('shipqueue_cancel_end');
        $cancelStart = $this->config->getFloat('shipqueue_cancel_start');
        $cancelFactor = $this->config->getFloat('shipqueue_cancel_factor');

        if ($shipyard->getCurrentLevel() >= $minLevel) {
            return min($cancelEnd, $cancelStart + (($shipyard->getCurrentLevel() - $minLevel) * $cancelFactor));
        } else {
            return 0;
        }
    }
}
