<?php

namespace EtoA\Quest\Progress;

use EtoA\Quest\Progress\InitFunctions\HaveBuildingLevel;
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
                return new HaveBuildingLevel($attributes, $this->container['etoa.building.buildlistrepository']);
            case HaveTechnologyLevel::NAME:
                return new HaveTechnologyLevel($attributes, $this->container['etoa.technology.techlistrepository']);
        }
    }
}
