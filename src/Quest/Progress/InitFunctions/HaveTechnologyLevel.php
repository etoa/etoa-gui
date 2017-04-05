<?php

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Technology\TechListRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveTechnologyLevel implements InitProgressHandlerFunctionInterface
{
    const NAME = 'have-technology-level';

    /** @var TechListRepository */
    private $techListRepository;
    /** @var int */
    private $buildingId;

    public function __construct(array $attributes, TechListRepository $techListRepository)
    {
        $this->techListRepository = $techListRepository;
        $this->buildingId = $attributes['technology_id'];
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task)
    {
        return $this->techListRepository->getTechnologyLevel($quest->getUser(), $this->buildingId);
    }
}
