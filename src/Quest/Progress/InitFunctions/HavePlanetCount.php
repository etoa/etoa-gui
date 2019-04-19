<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Planet\PlanetRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HavePlanetCount implements InitProgressHandlerFunctionInterface
{
    public const NAME = 'have-planet-count';

    /** @var PlanetRepository */
    private $planetRepository;

    public function __construct(PlanetRepository $planetRepository)
    {
        $this->planetRepository = $planetRepository;
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task): int
    {
        return $this->planetRepository->getPlanetCount($quest->getUser());
    }
}
