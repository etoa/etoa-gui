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
use EtoA\Technology\TechnologyRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use LittleCubicleGames\Quests\Progress\Functions\HandlerFunctionInterface;
use LittleCubicleGames\Quests\Progress\ProgressFunctionBuilderInterface;
use Pimple\Container;

class ContainerAwareFunctionBuilder implements ProgressFunctionBuilderInterface
{
    private BuildingRepository $buildingRepository;
    private TechnologyRepository $technologyRepository;
    private DefenseRepository $defenseRepository;
    private UserRepository $userRepository;
    private PlanetRepository $planetRepository;

    public function __construct(BuildingRepository $buildingRepository, TechnologyRepository $technologyRepository, DefenseRepository $defenseRepository, UserRepository $userRepository, PlanetRepository $planetRepository)
    {
        $this->buildingRepository = $buildingRepository;
        $this->technologyRepository = $technologyRepository;
        $this->defenseRepository = $defenseRepository;
        $this->userRepository = $userRepository;
        $this->planetRepository = $planetRepository;
    }

    /**
     * @param array<mixed> $attributes
     */
    public function build(string $taskName, array $attributes): ?HandlerFunctionInterface
    {
        switch ($taskName) {
            case HaveBuildingLevel::NAME:
                return new HaveBuildingLevel($attributes, $this->buildingRepository);
            case HaveTechnologyLevel::NAME:
                return new HaveTechnologyLevel($attributes, $this->technologyRepository);
            case HaveDefense::NAME:
                return new HaveDefense($attributes, $this->defenseRepository);
            case HaveGalaxyDiscovered::NAME:
                return new HaveGalaxyDiscovered($this->userRepository);
            case HavePoints::NAME:
                return new HavePoints($this->userRepository);
            case HavePlanetCount::NAME:
                return new HavePlanetCount($this->planetRepository);
            case HaveAlliance::NAME:
                return new HaveAlliance($this->userRepository);
            case HaveSpecialist::NAME:
                return new HaveSpecialist($this->userRepository);
            case HaveSpecialistType::NAME:
                return new HaveSpecialistType($attributes, $this->userRepository);
        }

        return null;
    }
}
