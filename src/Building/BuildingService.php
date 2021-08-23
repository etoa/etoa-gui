<?php

declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Race\RaceDataRepository;
use EtoA\Specialist\SpecialistService;
use EtoA\Universe\Planet\Planet;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Resources\BuildCosts;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\User\User;

class BuildingService
{
    private ConfigurationService $config;
    private SpecialistService $specialistService;
    private RaceDataRepository $raceRepository;
    private PlanetTypeRepository $planetTypeRepository;
    private SolarTypeRepository $starTypeRepository;

    public function __construct(
        ConfigurationService $config,
        SpecialistService $specialistService,
        RaceDataRepository $raceRepository,
        PlanetTypeRepository $planetTypeRepository,
        SolarTypeRepository $starTypeRepository
    ) {
        $this->config = $config;
        $this->specialistService = $specialistService;
        $this->raceRepository = $raceRepository;
        $this->planetTypeRepository = $planetTypeRepository;
        $this->starTypeRepository = $starTypeRepository;
    }

    public function calculateCosts(Building $building, int $level, ?User $user = null): BuildCosts
    {
        $costs = BuildCosts::create(
            $building->costsMetal,
            $building->costsCrystal,
            $building->costsPlastic,
            $building->costsFuel,
            $building->costsFood,
            $building->costsPower
        )->multiply($building->buildCostsFactor ** $level);

        if ($user !== null) {
            $specialist = $this->specialistService->getSpecialistOfUser($user->id);
            if ($specialist !== null) {
                $costs->multiply($specialist->costsBuildings);
            }
        }

        return $costs;
    }

    public function calculateBuildTime(BuildCosts $costs, ?User $user = null, ?Planet $planet = null): int
    {
        $time = $costs->total() / $this->config->getInt('global_time') * $this->config->getFloat('build_build_time');

        $factor = 1;

        if ($user !== null) {
            $race = $this->raceRepository->getRace($user->raceId);
            if ($race !== null) {
                $factor = $race->buildTime - 1;
            }

            $specialist = $this->specialistService->getSpecialistOfUser($user->id);
            if ($specialist !== null) {
                $factor += $specialist->timeBuildings - 1;
            }
        }

        if ($planet !== null) {
            $planetType = $this->planetTypeRepository->find($planet->typeId);
            if ($planetType !== null) {
                $factor += $planetType->buildTime - 1;
            }

            $starType = $this->starTypeRepository->getSolarTypeForEntity($planet->id);
            if ($starType !== null) {
                $factor += $starType->buildTime - 1;
            }
        }

        return (int) ($time * $factor);
    }
}
