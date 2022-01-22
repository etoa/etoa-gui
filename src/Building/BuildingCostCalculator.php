<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Resources\PreciseResources;

class BuildingCostCalculator
{
    public function __construct(
        private ConfigurationService $config,
    ) {
    }

    public function calculate(Building $building, int $level, BuildingCostContext $context): PreciseResources
    {
        $costs = PreciseResources::createFromBase($building->getCosts())
            ->multiply($building->buildCostsFactor ** $level);

        if ($context->specialist !== null) {
            $costs = $costs->multiply($context->specialist->costsBuildings);
        }

        $costs->time = $this->calculateBuildTime($costs, $context);

        return $costs;
    }

    public function calculateBuildTime(PreciseResources $costs, BuildingCostContext $context): int
    {
        $time = $costs->getSum() / $this->config->getInt('global_time') * $this->config->getFloat('build_build_time');

        $factor = 1;
        if ($context->race !== null) {
            $factor += $context->race->buildTime - 1;
        }

        if ($context->specialist !== null) {
            $factor += $context->specialist->timeBuildings - 1;
        }

        if ($context->planetType !== null) {
            $factor += $context->planetType->buildTime - 1;
        }

        if ($context->solarType !== null) {
            $factor += $context->solarType->buildTime - 1;
        }

        return (int) ($time * $factor);
    }
}
