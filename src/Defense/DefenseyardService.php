<?php

namespace EtoA\Defense;

use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\BuildingListItem;
use EtoA\Entity\Defense;
use EtoA\Entity\Planet;
use EtoA\Entity\Specialist;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Resources\PreciseResources;
use Symfony\Component\HttpFoundation\RequestStack;

class DefenseyardService
{
    public function __construct(
        private readonly PlanetRepository             $planetRepository,
        private readonly RequestStack                 $requestStack,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly ConfigurationService         $config,
        private readonly DefenseCategoryRepository    $defenseCategoryRepository,
        private readonly DefenseDataRepository        $defenseDataRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly DefenseRepository            $defenseListRepository,
        private readonly DefenseQueueRepository       $defenseQueueRepository,
        private readonly string                       $projectDir
    )
    {
    }

    public function getDefenseyardData(): array
    {
        $cp = $this->getCurrentPlanet();
        $defenseyard = $this->getDefenseyard();
        $defenseCategories = $this->defenseCategoryRepository->getAllCategories();
        $defenseByCategory = [];
        $properties = $cp->getUser()->getUserProperties();
        $specialist = $cp->getUser()->getSpecialist();
        $gen_tech_level = $this->technologyListItemRepository->findOneBy(['technology' => TechnologyId::GEN, 'user' => $cp->getUser()])?->getCurrentLevel() ?? 0;
        $time_boni_factor = $this->getTimeBoniFactor();

        $defenseCosts = [];
        foreach ($this->getDefense() as $defense) {
            $defenseByCategory[$defense->getCat()->getId()][] = $defense;
            $defenseCosts[$defense->getId()] = PreciseResources::createFromBase($defense->getCosts())->multiply($specialist ? $specialist->getCostsDefense() : 1);
        }

        $defenseQueueItems = $this->getDefenseQueueItems();
        $queueData = $this->buildQueueData($defenseQueueItems);

        $categoriesData = $this->buildCategoriesData(
            $defenseCategories,
            $defenseByCategory,
            $defenseCosts,
            $cp,
            $defenseyard,
            $time_boni_factor,
            $gen_tech_level,
            $specialist,
            count($defenseQueueItems) > 0
        );

        return [
            'queue' => $queueData,
            'categories' => $categoriesData,
            'compactView' => $properties->getItemShow() !== 'full',
            'hasDefenses' => count($categoriesData) > 0,
        ];
    }

    private function buildQueueData(array $defenseQueueItems): array
    {
        if (count($defenseQueueItems) === 0) {
            return [];
        }

        $queueData = [];
        $first = true;
        $absolute_starttime = 0;
        $currentItem = null;
        $time = time();

        foreach ($defenseQueueItems as $data) {
            if ($first) {
                $obj_t_remaining = ((($data->getEndTime() - $time) / $data->getObjectTime()) - floor(($data->getEndTime() - $time) / $data->getObjectTime())) * $data->getObjectTime();
                if ($obj_t_remaining == 0) {
                    $obj_t_remaining = $data->getObjectTime();
                }

                $absolute_starttime = $data->getStartTime();
                $obj_t_passed = $data->getObjectTime() - $obj_t_remaining;

                $currentItem = [
                    'name' => $data->getDefense()->getName(),
                    'startTime' => $time - $obj_t_passed,
                    'endTime' => $time + $obj_t_remaining,
                    'remaining' => $obj_t_remaining,
                ];

                $first = false;
            }

            $queueData[] = [
                'id' => $data->getId(),
                'count' => $data->getCount(),
                'name' => $data->getDefense()->getName(),
                'startTime' => $absolute_starttime,
                'endTime' => $absolute_starttime + $data->getEndTime() - $data->getStartTime(),
                'remaining' => $data->getEndTime() - $time,
                'cancelable' => $this->getCancelResFactor() > 0
            ];

            $absolute_starttime = $data->getEndTime();
        }

        return [
            'current' => $currentItem,
            'items' => $queueData,
        ];
    }

    private function buildCategoriesData(
        array $defenseCategories,
        array $defenseByCategory,
        array $defenseCosts,
              $cp,
              $defenseyard,
        float $time_boni_factor,
        int   $gen_tech_level,
              $specialist,
        bool  $hasQueue
    ): array
    {
        $categoriesData = [];

        foreach ($defenseCategories as $category) {
            if (!isset($defenseByCategory[$category->getId()])) {
                continue;
            }

            $categoryDefense = [];

            foreach ($defenseByCategory[$category->getId()] as $defenseData) {

                $defenseInfo = $this->buildDefenseData(
                    $defenseData,
                    $defenseCosts[$defenseData->getId()],
                    $cp,
                    $defenseyard,
                    $time_boni_factor,
                    $gen_tech_level,
                    $specialist,
                    $hasQueue
                );

                if ($defenseInfo !== null) {
                    $categoryDefense[] = $defenseInfo;
                }
            }

            if (count($categoryDefense) > 0) {
                $categoriesData[] = [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                    'defenses' => $categoryDefense,
                ];
            }
        }

        return $categoriesData;
    }

    private function buildDefenseData(
        Defense          $defenseData,
        PreciseResources $costs,
        Planet           $cp,
        BuildingListItem $defenseyard,
        float            $time_boni_factor,
        int              $gen_tech_level,
        ?Specialist      $specialist,
        bool             $hasQueue
    ): ?array
    {
        if (!$this->requirementsPassed($defenseData))
            return null;

        $btime = ($costs->getSum()) / $this->config->getInt('global_time') * $this->config->getFloat('def_build_time') * $time_boni_factor * ($specialist ? $specialist->getTimeDefense() : 1);
        $btime_min = $btime * (0.1 - ($gen_tech_level / 100));
        $peopleOptimized = ceil(($btime - $btime_min) / $this->config->getInt('people_work_done'));

        if ($btime_min < $this->config->getInt('shipyard_min_build_time')) {
            $btime_min = $this->config->getInt('shipyard_min_build_time');
        }

        $btime = ceil($btime - $defenseyard->getPeopleWorking() * $this->config->getInt('people_work_done'));
        if ($btime < $btime_min) {
            $btime = $btime_min;
        }

        $food_costs = $defenseyard->getPeopleWorking() * $this->config->getInt('people_food_require') + $costs->food;

        $build_cnt_metal = $costs->metal > 0 ? floor($cp->getResMetal() / $costs->metal) : PHP_INT_MAX;
        $build_cnt_crystal = $costs->crystal > 0 ? floor($cp->getResCrystal() / $costs->crystal) : PHP_INT_MAX;
        $build_cnt_plastic = $costs->plastic > 0 ? floor($cp->getResPlastic() / $costs->plastic) : PHP_INT_MAX;
        $build_cnt_fuel = $costs->fuel > 0 ? floor($cp->getResFuel() / $costs->fuel) : PHP_INT_MAX;
        $build_cnt_food = $food_costs > 0 ? floor($cp->getResFood() / $food_costs) : PHP_INT_MAX;

        $defense_count = $this->getAllDefenseCount($defenseData);
        $max_cnt = $defenseData->getMaxCount() !== 0 ? $defenseData->getMaxCount() - $defense_count : PHP_INT_MAX;

        $defense_max_build = min($build_cnt_metal, $build_cnt_crystal, $build_cnt_plastic, $build_cnt_fuel, $build_cnt_food, $max_cnt);

        $waitTimes = $this->calculateWaitTimes($costs, $food_costs, $cp);

        return [
            'id' => $defenseData->getId(),
            'name' => $defenseData->getName(),
            'image' => $defenseData->getImagePath(),
            'imageMedium' => $defenseData->getImagePath('medium'),
            'shortComment' => $defenseData->getShortComment(),
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
            'maxBuildable' => $defense_max_build,
            'defenseCount' => $defense_count,
            'maxCount' => $defenseData->getMaxCount(),
            'waitTimes' => $waitTimes,
            'hasQueue' => $hasQueue,
            'model' => file_exists($this->projectDir . '/public/build/models/defense/def' . $defenseData->getId() . '.glb') ? '/build/models/defense/def' . $defenseData->getId() . '.glb' : false
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

    private function requirementsPassed(Defense $defense): bool
    {
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        foreach ($defense as $requirement) {
            if ($requirement->getBuilding() && $requirement->getLevel() > $this->buildingListItemRepository->findOneBy(['building' => $requirement->getBuilding(), 'entity' => $cp])?->getCurrentLevel()) {
                return false;
            }

            if ($requirement->getTech() && $requirement->getLevel() > $this->technologyListItemRepository->findOneBy(['user' => $cp->getUser(), 'technology' => $requirement->getTech()])?->getCurrentLevel()) {
                return false;
            }
        }

        return true;
    }

    private function getDefense(): ?array
    {
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));
        $properties = $cp->getUser()->getUserproperties();
        $defenseSearch = DefenseSearch::create()->showOrBuildable()->raceOrNull($cp->getUser()->getRace());
        $defenseOrder = DefenseSort::specialWithUserSort($properties->getItemOrderDef(), $properties->getItemOrderWay());

        return $this->defenseDataRepository->searchDefense($defenseSearch, $defenseOrder);
    }

    public function getAllDefenseCount(Defense|int $defense): int
    {
        // Zählt die Anzahl Verteidigungen dieses Typs im ganzen Account...
        $request = $this->requestStack->getCurrentRequest();
        $cp = $this->planetRepository->find($request->getSession()->get('cpid'));

        $defense_count = 0;
        // ... auf den Planeten
        $defenselist = $this->defenseListRepository->findOneBy(['entity' => $cp, 'defense' => $defense]);
        if ($defenselist) {
            $defense_count += $defenselist->getCount();
        }
        $queuelist = $this->defenseQueueRepository->findBy(['entity' => $cp, 'defense' => $defense]);
        // ... in der Bauliste
        foreach ($queuelist as $item) {
            $defense_count += $item->getCount();
        }

        return $defense_count;
    }

    private function getDefenseyard(): ?BuildingListItem
    {
        return $this->buildingListItemRepository->findOneBy(['building' => BuildingId::DEFENSE->value, 'entity' => $this->getCurrentPlanet()]);
    }

    public function getCurrentPlanet(): Planet
    {
        $request = $this->requestStack->getCurrentRequest();
        return $this->planetRepository->find($request->getSession()->get('cpid'));
    }

    private function getDefenseQueueItems(): array
    {
        return $this->defenseQueueRepository->searchQueueItems(DefenseQueueSearch::create()->entity($this->getCurrentPlanet())->endAfter(time()));
    }

    private function getTimeBoniFactor(): float|int
    {
        $need_bonus_level = $this->getDefenseyard()?->getCurrentLevel() - $this->config->param1Int('build_time_boni_waffenfabrik');

        if ($need_bonus_level <= 0) {
            return 1;
        } else {
            return 1 - ($need_bonus_level * ($this->config->getInt('build_time_boni_waffenfabrik') / 100));
        }
    }

    // Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
    public function getCancelResFactor(): float|int
    {
        $defenseyard = $this->getDefenseyard();
        $minLevel = $this->config->getInt('defqueue_cancel_min_level');
        $cancelEnd = $this->config->getFloat('defqueue_cancel_end');
        $cancelStart = $this->config->getFloat('defqueue_cancel_start');
        $cancelFactor = $this->config->getFloat('defqueue_cancel_factor');

        if ($defenseyard->getCurrentLevel() >= $minLevel) {
            return min($cancelEnd, $cancelStart + (($defenseyard->getCurrentLevel() - $minLevel) * $cancelFactor));
        } else {
            return 0;
        }
    }
}
