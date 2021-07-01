<?php declare(strict_types=1);

namespace EtoA\Quest\Progress;

use EtoA\Building\BuildingRepository;
use EtoA\Defense\DefenseRepository;
use EtoA\Quest\Progress\InitFunctions\HaveAlliance;
use EtoA\Quest\Progress\InitFunctions\HaveBuildingLevel;
use EtoA\Quest\Progress\InitFunctions\HaveDefense;
use EtoA\Quest\Progress\InitFunctions\HaveGalaxyDiscovered;
use EtoA\Quest\Progress\InitFunctions\HavePlanetCount;
use EtoA\Quest\Progress\InitFunctions\HavePoints;
use EtoA\Quest\Progress\InitFunctions\HaveSpecialist;
use EtoA\Quest\Progress\InitFunctions\HaveSpecialistType;
use EtoA\Quest\Progress\InitFunctions\HaveTechnologyLevel;
use EtoA\Universe\PlanetRepository;
use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;
use LittleCubicleGames\Quests\Progress\ProgressFunctionBuilderInterface;
use Pimple\Container;

class ContainerAwareFunctionBuilder implements ProgressFunctionBuilderInterface
{
    private Container $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function build(string $taskName, array $attributes): ?HandlerFunctionInterface
    {
        switch ($taskName) {
            case HaveBuildingLevel::NAME:
                return new HaveBuildingLevel($attributes, $this->container[BuildingRepository::class]);
            case HaveTechnologyLevel::NAME:
                return new HaveTechnologyLevel($attributes, $this->container['etoa.technology.repository']);
            case HaveDefense::NAME:
                return new HaveDefense($attributes, $this->container[DefenseRepository::class]);
            case HaveGalaxyDiscovered::NAME:
                return new HaveGalaxyDiscovered($this->container[UserRepository::class]);
            case HavePoints::NAME:
                return new HavePoints($this->container[UserRepository::class]);
            case HavePlanetCount::NAME:
                return new HavePlanetCount($this->container[PlanetRepository::class]);
            case HaveAlliance::NAME:
                return new HaveAlliance($this->container[UserRepository::class]);
            case HaveSpecialist::NAME:
                return new HaveSpecialist($this->container[UserRepository::class]);
            case HaveSpecialistType::NAME:
                return new HaveSpecialistType($attributes, $this->container[UserRepository::class]);
        }

        return null;
    }
}
