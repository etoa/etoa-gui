<?php

namespace EtoA\Quest\Progress;

use EtoA\Quest\Progress\InitFunctions\HaveBuildingLevel;
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
        }
    }
}
