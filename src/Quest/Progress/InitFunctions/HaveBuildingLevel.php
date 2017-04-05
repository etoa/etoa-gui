<?php

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Building\BuildingRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveBuildingLevel implements InitProgressHandlerFunctionInterface
{
    const NAME = 'have-building-level';

    /** @var BuildingRepository */
    private $buildingRepository;
    /** @var int */
    private $buildingId;

    public function __construct(array $attributes, BuildingRepository $buildingRepository)
    {
        $this->buildingRepository = $buildingRepository;
        $this->buildingId = $attributes['building_id'];
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task)
    {
        return $this->buildingRepository->getBuildingLevel($quest->getUser(), $this->buildingId);
    }
}
