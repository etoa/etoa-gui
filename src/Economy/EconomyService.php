<?php

namespace EtoA\Economy;

use EtoA\Building\BuildingDataRepository;
use EtoA\Building\BuildingId;
use EtoA\Building\BuildingListItemRepository;
use EtoA\Building\BuildingSearch;
use EtoA\Building\BuildingSort;
use EtoA\Building\BuildingTypeId;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Ship\ShipDataRepository;
use EtoA\Ship\ShipListRepository;
use EtoA\Ship\ShipSearch;
use EtoA\Technology\TechnologyId;
use EtoA\Technology\TechnologyListItemRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Star\StarRepository;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;

class EconomyService
{
    public function __construct(
        private readonly ConfigurationService         $configurationService,
        private readonly PlanetRepository             $planetRepository,
        private readonly RequestStack                 $requestStack,
        private readonly Security                     $security,
        private readonly BuildingListItemRepository   $buildingListItemRepository,
        private readonly BuildingDataRepository       $buildingDataRepository,
        private readonly ShipDataRepository           $shipDataRepository,
        private readonly ShipListRepository           $shipListRepository,
        private readonly TechnologyListItemRepository $technologyListItemRepository,
        private readonly ConfigurationService         $config,
        private readonly StarRepository               $starRepository
    )
    {
    }

    public function getPlanetResourcesData(): array
    {
        $user = $this->security->getUser()->getData();
        $planets = $user->getPlanets();

        $planetData = [];
        $totals = [
            'metal' => 0,
            'crystal' => 0,
            'plastic' => 0,
            'fuel' => 0,
            'food' => 0,
            'people' => 0
        ];
        $maxResources = [
            'metal' => 0,
            'crystal' => 0,
            'plastic' => 0,
            'fuel' => 0,
            'food' => 0,
            'people' => 0
        ];
        $maxProduction = [
            'metal' => 0,
            'crystal' => 0,
            'plastic' => 0,
            'fuel' => 0,
            'food' => 0,
            'people' => 0,
            'energy' => 0
        ];
        $minResources = [
            'metal' => PHP_INT_MAX,
            'crystal' => PHP_INT_MAX,
            'plastic' => PHP_INT_MAX,
            'fuel' => PHP_INT_MAX,
            'food' => PHP_INT_MAX,
            'people' => PHP_INT_MAX
        ];
        $minProduction = [
            'metal' => PHP_INT_MAX,
            'crystal' => PHP_INT_MAX,
            'plastic' => PHP_INT_MAX,
            'fuel' => PHP_INT_MAX,
            'food' => PHP_INT_MAX,
            'people' => PHP_INT_MAX,
            'energy' => PHP_INT_MAX
        ];

        $resourceKeys = ['metal', 'crystal', 'plastic', 'fuel', 'food', 'people'];

        foreach ($planets as $planet) {
            $entityId = $planet->getEntity()->getId();
            $resources = [
                'metal' => floor($planet->getResMetal()),
                'crystal' => floor($planet->getResCrystal()),
                'plastic' => floor($planet->getResPlastic()),
                'fuel' => floor($planet->getResFuel()),
                'food' => floor($planet->getResFood()),
                'people' => floor($planet->getPeople())
            ];

            $storage = [
                'metal' => floor($planet->getStoreMetal()),
                'crystal' => floor($planet->getStoreCrystal()),
                'plastic' => floor($planet->getStorePlastic()),
                'fuel' => floor($planet->getStoreFuel()),
                'food' => floor($planet->getStoreFood()),
                'people' => floor($planet->getPeoplePlace())
            ];

            $production = [
                'metal' => $planet->getProdMetal(),
                'crystal' => $planet->getProdCrystal(),
                'plastic' => $planet->getProdPlastic(),
                'fuel' => $planet->getProdFuel(),
                'food' => $planet->getProdFood(),
                'people' => $planet->getProdPeople(),
                'energy' => $planet->getProdPower()
            ];

            $timeToFull = [];
            foreach ($resourceKeys as $key) {
                if ($production[$key] > 0 && $storage[$key] > $resources[$key]) {
                    $timeToFull[$key] = ceil(($storage[$key] - $resources[$key]) / $production[$key] * 3600);
                } else {
                    $timeToFull[$key] = 0;
                }
            }

            // Calculate people growth for tooltip
            $capacity = $planet->getPeoplePlace();
            if ($capacity < 200) {
                $capacity = 200;
            }
            $star = $this->starRepository->findStarForCell($planet->getEntity()->getCell());
            $peopleGrowthPerHour = $planet->getPeople() * (
                    ($this->configurationService->getFloat('people_multiply') +
                        $planet->getPlanetType()->getPeople() +
                        $user->getRace()->getPopulation() +
                        $star->getSolarType()->getPeople() +
                        ($user->getSpecialist() ? $user->getSpecialist()->getProdPeople() : 1) - 4) *
                    (1 - ($planet->getPeople() / ($capacity + 1))) / 24
                );

            // Update totals and min/max
            foreach ($resourceKeys as $key) {
                $totals[$key] += $resources[$key];
                $maxResources[$key] = max($maxResources[$key], $resources[$key]);
                $minResources[$key] = min($minResources[$key], $resources[$key]);
                $maxProduction[$key] = max($maxProduction[$key], $production[$key]);
                $minProduction[$key] = min($minProduction[$key], $production[$key]);
            }

            $maxProduction['energy'] = max($maxProduction['energy'], $production['energy']);
            $minProduction['energy'] = min($minProduction['energy'], $production['energy']);

            $planetData[] = [
                'id' => $planet->getEntity()->getId(),
                'entityId' => $entityId,
                'name' => $planet->displayName(),
                'resources' => $resources,
                'storage' => $storage,
                'production' => $production,
                'timeToFull' => $timeToFull,
                'peopleGrowthPerHour' => round($peopleGrowthPerHour)
            ];
        }

        $planetCount = count($planets);
        $averages = [];
        foreach ($resourceKeys as $key) {
            $averages[$key] = $planetCount > 0 ? $totals[$key] / $planetCount : 0;
        }

        return [
            'planets' => $planetData,
            'totals' => $totals,
            'averages' => $averages,
            'maxResources' => $maxResources,
            'minResources' => $minResources,
            'maxProduction' => $maxProduction,
            'minProduction' => $minProduction,
            'resourceKeys' => $resourceKeys
        ];
    }

    public function getPlanetEconomyData(): array
    {
        $request = $this->requestStack->getCurrentRequest();
        $planet = $this->planetRepository->find($request->getSession()->get('cpid'));
        $cu = $planet->getUser();
        $race = $cu->getRace();
        $specialist = $cu->getSpecialist();
        $star = $this->starRepository->findStarForCell($planet->getEntity()->getCell());

        $resourceKeys = ['metal', 'crystal', 'plastic', 'fuel', 'food'];

        $cnt = array_fill_keys($resourceKeys, 0);
        $baseResourceProd = array_fill_keys($resourceKeys, 0);

        $producingBuildingsData = [];
        $pwrcnt = 0;

        $producingBuildings = $this->buildingDataRepository->searchBuildings(BuildingSearch::create()->withProductionOrPowerUse(),BuildingSort::type());

        $fuelBonus = $planet->fuelProductionBonus();
        foreach ($producingBuildings as $building) {
            $buildlist = $this->buildingListItemRepository->findOneBy(['entity'=>$planet,'building'=>$building]);

            if($buildlist) {
                $bareBuildingProduction = [];
                $prodIncludingBoni = [];

                foreach ($resourceKeys as $resourceKey) {
                    $methodName = 'getProd' . ucfirst($resourceKey);
                    $bareBuildingProduction[$resourceKey] = $building->$methodName() * pow($building->getProductionFactor(), $buildlist->getCurrentLevel() - 1);
                    $prodIncludingBoni[$resourceKey] = $bareBuildingProduction[$resourceKey];
                    $baseResourceProd[$resourceKey] += $bareBuildingProduction[$resourceKey] * $buildlist->getProdPercent();
                }

                $bonusFactors = [
                    'metal' => $planet->getPlanetType()->getMetal() + $race->getMetal() + $star->getSolarType()->getMetal() + ($specialist ? $specialist->getProdMetal() : 1) - 4,
                    'crystal' => $planet->getPlanetType()->getCrystal() + $race->getCrystal() + $star->getSolarType()->getCrystal() + ($specialist ? $specialist->getProdCrystal() : 1) - 4,
                    'plastic' => $planet->getPlanetType()->getPlastic() + $race->getPlastic() + $star->getSolarType()->getPlastic() + ($specialist ? $specialist->getProdPlastic() : 1) - 4,
                    'fuel' => $planet->getPlanetType()->getFuel() + $race->getFuel() + $star->getSolarType()->getFuel() + ($specialist ? $specialist->getProdFuel() : 1) - 4 + $planet->getFuelProductionBonusFactor() * -1,
                    'food' => $planet->getPlanetType()->getFood() + $race->getFood() + $star->getSolarType()->getFood() + ($specialist ? $specialist->getProdFood() : 1) - 4,
                ];

                foreach ($resourceKeys as $resourceKey) {
                    if ($bareBuildingProduction[$resourceKey] != 0) {
                        $prodIncludingBoni[$resourceKey] += $bareBuildingProduction[$resourceKey] * $bonusFactors[$resourceKey];
                    }
                    $cnt[$resourceKey] += floor($prodIncludingBoni[$resourceKey]);
                }

                $building_power_use = floor($building->getPowerUse() * pow($building->getProductionFactor(), $buildlist->getCurrentLevel() - 1));
                $pwrcnt += $building_power_use * $buildlist->getProdPercent();

                $productionPercentOptions = [];
                if ($buildlist->getBuilding()->getType()->getId() === BuildingTypeId::RES) {
                    for ($x = 0; $x < 1; $x += 0.1) {
                        $vx = ($x > 0.9) ? 0 : 1 - $x;
                        $productionPercentOptions[] = [
                            'value' => $vx,
                            'label' => ($vx * 100) . ' %',
                            'selected' => doubleval($vx) >= doubleval($buildlist->getProdPercent())
                        ];

                    }
                } elseif ($building->getId() === BuildingId::MISSILE->value || $building->getId() === BuildingId::CRYPTO->value) {
                    $productionPercentOptions = [
                        ['value' => 1, 'label' => '100 %', 'selected' => $buildlist->getProdPercent() == 1],
                        ['value' => 0, 'label' => '0 %', 'selected' => $buildlist->getProdPercent() == 0]
                    ];
                }

                $producingBuildingsData[] = [
                    'id' => $building->getId(),
                    'name' => $building->getName(),
                    'level' => $buildlist->getCurrentLevel(),
                    'prodPercent' => $buildlist->getProdPercent(),
                    'bareProd' => $bareBuildingProduction,
                    'prodWithBoni' => $prodIncludingBoni,
                    'powerUse' => ceil($building_power_use * $buildlist->getProdPercent()),
                    'productionPercentOptions' => $productionPercentOptions,
                    'buildType' => $buildlist->getBuildType()
                ];
            }
        }

        $pwrcnt = floor($pwrcnt);

        $boostData = null;
        if ($this->config->getBoolean('boost_system_enable')) {
            $bonusProd = [];
            foreach ($resourceKeys as $resourceKey) {
                $bonusProd[$resourceKey] = $baseResourceProd[$resourceKey] * $cu->getBoostBonusProduction();
                $cnt[$resourceKey] += $bonusProd[$resourceKey];
            }
            $boostData = [
                'factor' => $cu->getBoostBonusProduction(),
                'production' => $bonusProd,
                'baseProduction' => $baseResourceProd
            ];
        }

        $powerUsed = $pwrcnt;
        $insufficientPower = false;
        $powerEfficiency = 1;

        if ($pwrcnt > $planet->getProdPower()) {
            $insufficientPower = true;
            $powerEfficiency = $planet->getProdPower() / $pwrcnt;
            foreach ($resourceKeys as $resourceKey) {
                $cnt[$resourceKey] = floor($cnt[$resourceKey] * $powerEfficiency);
            }
        }

        $bunkerData = null;
        $blvl = $this->buildingListItemRepository->getBuildingLevel(BuildingId::RES_BUNKER->value, $planet);
        if ($blvl > 0) {
            $bunkerBuilding = $this->buildingDataRepository->getBuilding(BuildingId::RES_BUNKER->value);
            $bunkerData = [
                'name' => $bunkerBuilding->getName(),
                'level' => $blvl,
                'capacity' => $bunkerBuilding->calculateBunkerResources($blvl)
            ];
        }

        $energyTechLevel = $this->technologyListItemRepository->getTechnologyLevel($cu, TechnologyId::ENERGY);
        $energyTechPowerBonusRequiredLevel = $this->config->getInt('energy_tech_power_bonus_required_level');
        $energyTechPowerBonusFactor = 1;

        if ($energyTechLevel > $energyTechPowerBonusRequiredLevel) {
            $percentPerLevel = $this->config->getInt('energy_tech_power_bonus_percent_per_level');
            $percent = $percentPerLevel * ($energyTechLevel - $energyTechPowerBonusRequiredLevel);
            $energyTechPowerBonusFactor = (100 + $percent) / 100;
        }

        $bonusFactor = 1 + ($planet->getPlanetType()->getPower() + $race->getPower() + $star->getSolarType()->getPower() + ($specialist ? $specialist->getProdPower() : 1) + $energyTechPowerBonusFactor - 5);

        $powerProducingBuildings = [];
        $producingPowerBuildings = $this->buildingDataRepository->searchBuildings(BuildingSearch::create()->withPowerProduction());
        $totalPowerProduced = 0;

        foreach ($producingPowerBuildings as $building) {
            $buildlist = $this->buildingListItemRepository->findOneBy(['entity'=>$planet,'building'=>$building]);
            if($buildlist) {
                $powerProd = round($building->getProdPower() * pow($building->getProductionFactor(), $buildlist->getCurrentLevel() - 1));
                $powerProd *= $bonusFactor;
                $totalPowerProduced += $powerProd;

                $powerProducingBuildings[] = [
                    'name' => $building->getName(),
                    'level' => $buildlist->getCurrentLevel(),
                    'production' => floor($powerProd)
                ];
            }
        }

        $powerProducingShips = [];
        $ships = $this->shipDataRepository->searchShips(ShipSearch::create()->producesPower());

        if (count($ships) > 0) {
            $solarProdBonus = $planet->solarPowerBonus();
            foreach ($ships as $ship) {
                $shiplist = $this->shipListRepository->findOneBy(['ship'=>$ship,'entity'=>$planet]);
                if($shiplist) {
                    $pwr = ($ship->getPowerProduction() + $solarProdBonus);
                    $pwr *= $bonusFactor;
                    $pwrt = $pwr * $shiplist->getCount();
                    $totalPowerProduced += $pwrt;

                    $powerProducingShips[] = [
                        'name' => $ship->getName(),
                        'count' => $shiplist->getCount(),
                        'production' => $pwrt,
                        'basePower' => $ship->getPowerProduction(),
                        'solarBonus' => $solarProdBonus,
                        'bonusFactor' => $bonusFactor,
                        'perShip' => $pwr
                    ];
                }
            }
        }

        $powerFree = $totalPowerProduced - $powerUsed;
        $powerUsedPercent = $totalPowerProduced > 0 ? round($powerUsed / $totalPowerProduced * 100, 2) : 0;
        $powerFreePercent = $totalPowerProduced > 0 ? round($powerFree / $totalPowerProduced * 100, 2) : 0;

        $storageBuildings = $this->buildingDataRepository->searchBuildings(BuildingSearch::create()->storage());
        $storageBuildingsData = [];
        $storetotal = array_fill(0, 5, $this->config->getInt('def_store_capacity'));

        foreach ($storageBuildings as $building) {
            $buildlist = $this->buildingListItemRepository->findOneBy(['entity'=>$planet,'building'=>$building]);

            if($buildlist) {
                $level = $buildlist->getCurrentLevel() - 1;

                $store = [
                    round($building->getStoreMetal() * pow($building->getStoreFactor(), $level)),
                    round($building->getStoreCrystal() * pow($building->getStoreFactor(), $level)),
                    round($building->getStorePlastic() * pow($building->getStoreFactor(), $level)),
                    round($building->getStoreFuel() * pow($building->getStoreFactor(), $level)),
                    round($building->getStoreFood() * pow($building->getStoreFactor(), $level))
                ];

                foreach ($store as $id => $sd) {
                    $storetotal[$id] += $sd;
                }

                $storageBuildingsData[] = [
                    'name' => $building->getName(),
                    'level' => $buildlist->getCurrentLevel(),
                    'storage' => $store
                ];
            }
        }

        $storageUsagePercent = [
            'metal' => $planet->getStoreMetal() > 0 ? round($planet->getResMetal() / $planet->getStoreMetal() * 100) : 0,
            'crystal' => $planet->getStoreCrystal() > 0 ? round($planet->getResCrystal() / $planet->getStoreCrystal() * 100) : 0,
            'plastic' => $planet->getStorePlastic() > 0 ? round($planet->getResPlastic() / $planet->getStorePlastic() * 100) : 0,
            'fuel' => $planet->getStoreFuel() > 0 ? round($planet->getResFuel() / $planet->getStoreFuel() * 100) : 0,
            'food' => $planet->getStoreFood() > 0 ? round($planet->getResFood() / $planet->getStoreFood() * 100) : 0
        ];

        $boniData = [
            'planet' => [
                'name' => $planet->getPlanetType()->getName(),
                'metal' => $planet->getPlanetType()->getMetal(),
                'crystal' => $planet->getPlanetType()->getCrystal(),
                'plastic' => $planet->getPlanetType()->getPlastic(),
                'fuel' => $planet->getPlanetType()->getFuel(),
                'food' => $planet->getPlanetType()->getFood(),
                'power' => $planet->getPlanetType()->getPower(),
                'population' => $planet->getPlanetType()->getPeople(),
                'researchtime' => $planet->getPlanetType()->getResearchtime(),
                'buildtime' => $planet->getPlanetType()->getBuildtime()
            ],
            'race' => [
                'name' => $race->getName(),
                'metal' => $race->getMetal(),
                'crystal' => $race->getCrystal(),
                'plastic' => $race->getPlastic(),
                'fuel' => $race->getFuel(),
                'food' => $race->getFood(),
                'power' => $race->getPower(),
                'population' => $race->getPopulation(),
                'researchTime' => $race->getResearchTime(),
                'buildTime' => $race->getBuildTime(),
                'fleetTime' => $race->getFleetTime()
            ],
            'star' => [
                'name' => $star->getSolarType()->getName(),
                'metal' => $star->getSolarType()->getMetal(),
                'crystal' => $star->getSolarType()->getCrystal(),
                'plastic' => $star->getSolarType()->getPlastic(),
                'fuel' => $star->getSolarType()->getFuel(),
                'food' => $star->getSolarType()->getFood(),
                'power' => $star->getSolarType()->getPower(),
                'population' => $star->getSolarType()->getPeople(),
                'researchtime' => $star->getSolarType()->getResearchtime(),
                'buildtime' => $star->getSolarType()->getBuildtime()
            ],
            'specialist' => $specialist ? [
                'name' => $specialist->getName(),
                'prodMetal' => $specialist->getProdMetal(),
                'prodCrystal' => $specialist->getProdCrystal(),
                'prodPlastic' => $specialist->getProdPlastic(),
                'prodFuel' => $specialist->getProdFuel(),
                'prodFood' => $specialist->getProdFood(),
                'prodPower' => $specialist->getProdPower(),
                'prodPeople' => $specialist->getProdPeople(),
                'timeTechnologies' => $specialist->getTimeTechnologies(),
                'timeBuildings' => $specialist->getTimeBuildings(),
                'timeShips' => $specialist->getTimeShips(),
                'timeDefense' => $specialist->getTimeDefense(),
                'fleetSpeed' => $specialist->getFleetSpeed()
            ] : null,
            'energyTech' => $energyTechPowerBonusFactor
        ];

        return [
            'planet' => $planet,
            'resourceKeys' => $resourceKeys,
            'producingBuildings' => $producingBuildingsData,
            'totalProduction' => $cnt,
            'totalProductionDay' => array_map(fn($v) => $v * 24, $cnt),
            'totalProductionWeek' => array_map(fn($v) => $v * 168, $cnt),
            'powerUsed' => $powerUsed,
            'powerUsedFormatted' => floor($powerUsed),
            'boost' => $boostData,
            'insufficientPower' => $insufficientPower,
            'powerEfficiency' => $powerEfficiency,
            'bunker' => $bunkerData,
            'powerProducingBuildings' => $powerProducingBuildings,
            'powerProducingShips' => $powerProducingShips,
            'powerProduced' => $totalPowerProduced,
            'powerFree' => $powerFree,
            'powerUsedPercent' => $powerUsedPercent,
            'powerFreePercent' => $powerFreePercent,
            'storageBuildings' => $storageBuildingsData,
            'storageTotal' => $storetotal,
            'storageUsagePercent' => $storageUsagePercent,
            'baseStorageCapacity' => $this->config->getInt('def_store_capacity'),
            'boni' => $boniData,
            'fuelBonus' => $fuelBonus
        ];
    }
}