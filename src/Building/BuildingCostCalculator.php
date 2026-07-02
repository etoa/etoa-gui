<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Building;
use EtoA\Universe\Resources\PreciseResources;

class BuildingCostCalculator
{
    public function __construct(
        private readonly ConfigurationService $config,
    ) {
    }

    public function calculate(Building $building, int $level, BuildingCostContext $context): PreciseResources
    {
        $costs = PreciseResources::createFromBase($building->getCosts())
            ->multiply($building->getBuildCostsFactor() ** ($level - 1));

        if ($context->specialist !== null) {
            $costs = $costs->multiply($context->specialist->getCostsBuildings());
        }

        $costs->time = $this->calculateBuildTime($costs, $context);
        $costs->food = $costs->food + $context->peopleWorking * $this->config->getInt('people_food_require');

        return $costs;
    }

    public function calculateBuildTime(PreciseResources $costs, BuildingCostContext $context): int
    {
        $time = $costs->getSum() / $this->config->getInt('global_time') * $this->config->getFloat('build_build_time');

        $factor = 1;
        if ($context->race !== null) {
            $factor += $context->race->getBuildTime() - 1;
        }

        if ($context->specialist !== null) {
            $factor += $context->specialist->getTimeBuildings() - 1;
        }

        if ($context->planetType !== null) {
            $factor += $context->planetType->getBuildTime() - 1;
        }

        if ($context->solarType !== null) {
            $factor += $context->solarType->getBuildTime() - 1;
        }

        $buildTime = ($time * $factor);

        $timeMin = $buildTime * (0.1 - ($context->gentech / 100));
        $buildTime = $buildTime - $context->peopleWorking * $this->config->getInt('people_work_done');

        return (int) max($timeMin,$buildTime);
    }
}
