<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\Building\BuildingCostContext;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Entity\Technology;
use EtoA\Universe\Resources\PreciseResources;

class TechnologyCostCalculator
{
    public function __construct(
        private readonly ConfigurationService $config,
    ) {
    }

    public function calculate(Technology $technology, int $level, BuildingCostContext $context): PreciseResources
    {
        $costs = PreciseResources::createFromBase($technology->getCosts())
            ->multiply($technology->getBuildCostsFactor() ** ($level - 1));

        if ($context->specialist !== null) {
            $costs = $costs->multiply($context->specialist->getCostsTechnologies());
        }

        $costs->time = $this->calculateBuildTime($costs, $context);
        $costs->food = $costs->food + $context->peopleWorking * $this->config->getInt('people_food_require');

        return $costs;
    }

    public function calculateBuildTime(PreciseResources $costs, BuildingCostContext $context): int
    {
        $time = $costs->getSum() / $this->config->getInt('global_time') * $this->config->getFloat('res_build_time');

        $factor = 1;
        if ($context->race !== null) {
            $factor += $context->race->getResearchTime() - 1;
        }

        if ($context->specialist !== null) {
            $factor += $context->specialist->getTimeTechnologies() - 1;
        }

        if ($context->planetType !== null) {
            $factor += $context->planetType->getResearchTime() - 1;
        }

        if ($context->solarType !== null) {
            $factor += $context->solarType->getResearchTime() - 1;
        }

        $researchTime = ($time * $factor);

        $timeMin = $researchTime * (0.1 - ($context->gentech / 100));
        $researchTime = $researchTime - $context->peopleWorking * $this->config->getInt('people_work_done');

        return (int) max($timeMin,$researchTime);
     }
}
