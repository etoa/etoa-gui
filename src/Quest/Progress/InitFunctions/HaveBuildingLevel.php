<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Building\BuildingRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveBuildingLevel implements InitProgressHandlerFunctionInterface
{
    public const NAME = 'have-building-level';

    private BuildingRepository $buildingRepository;
    private int $buildingId;

    public function __construct(array $attributes, BuildingRepository $buildingRepository)
    {
        $this->buildingRepository = $buildingRepository;
        $this->buildingId = $attributes['building_id'];
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task): int
    {
        return $this->buildingRepository->getBuildingLevel($quest->getUser(), $this->buildingId);
    }
}
