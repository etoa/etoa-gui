<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Technology\TechnologyRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveTechnologyLevel implements InitProgressHandlerFunctionInterface
{
    public const NAME = 'have-technology-level';

    private TechnologyRepository $technologyRepository;
    private int $buildingId;

    public function __construct(array $attributes, TechnologyRepository $technologyRepository)
    {
        $this->technologyRepository = $technologyRepository;
        $this->buildingId = $attributes['technology_id'];
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task): int
    {
        return $this->technologyRepository->getTechnologyLevel($quest->getUser(), $this->buildingId);
    }
}
