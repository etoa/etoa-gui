<?php

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Building\BuildListRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveBuildingLevel implements InitProgressHandlerFunctionInterface
{
    const NAME = 'have-building-level';

    /** @var BuildListRepository */
    private $buildListRepository;
    /** @var int */
    private $buildingId;

    public function __construct(array $attributes, BuildListRepository $buildListRepository)
    {
        $this->buildListRepository = $buildListRepository;
        $this->buildingId = $attributes['building_id'];
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task)
    {
        return $this->buildListRepository->getBuildingLevel($quest->getUser(), $this->buildingId);
    }
}
