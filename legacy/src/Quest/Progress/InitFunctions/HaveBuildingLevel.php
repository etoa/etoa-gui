<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Building\BuildingListItemRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveBuildingLevel implements InitProgressHandlerFunctionInterface
{
    public const NAME = 'have-building-level';

    private BuildingListItemRepository $buildingRepository;
    private int $buildingId;

    /**
     * @param array<string, int> $attributes
     */
    public function __construct(array $attributes, BuildingListItemRepository $buildingRepository)
    {
        $this->buildingRepository = $buildingRepository;
        $this->buildingId = $attributes['building_id'];
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task): int
    {
        return $this->buildingRepository->getHighestBuildingLevel($quest->getUser(), $this->buildingId);
    }
}
