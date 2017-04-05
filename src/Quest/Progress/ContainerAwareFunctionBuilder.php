<?php

namespace EtoA\Quest\Progress;

use EtoA\Quest\Progress\InitFunctions\HaveBuildingLevel;
use EtoA\Quest\Progress\InitFunctions\HaveDefense;
use EtoA\Quest\Progress\InitFunctions\HaveTechnologyLevel;
use LittleCubicleGames\Quests\Progress\ProgressFunctionBuilderInterface;
use Pimple\Container;

class ContainerAwareFunctionBuilder implements ProgressFunctionBuilderInterface
{
    /** @var Container */
    private $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function build($taskName, array $attributes)
    {
        switch ($taskName) {
            case HaveBuildingLevel::NAME:
                return new HaveBuildingLevel($attributes, $this->container['etoa.building.repository']);
            case HaveTechnologyLevel::NAME:
                return new HaveTechnologyLevel($attributes, $this->container['etoa.technology.repository']);
            case HaveDefense::NAME:
                return new HaveDefense($attributes, $this->container['etoa.defense.repository']);
        }
    }
}
