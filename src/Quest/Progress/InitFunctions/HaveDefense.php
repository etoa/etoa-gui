<?php declare(strict_types=1);

namespace EtoA\Quest\Progress\InitFunctions;

use EtoA\Defense\DefenseRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;
use LittleCubicleGames\Quests\Progress\Functions\InitProgressHandlerFunctionInterface;

class HaveDefense implements InitProgressHandlerFunctionInterface
{
    public const NAME = 'have-defense';

    /** @var DefenseRepository */
    private $defenseRepository;
    /** @var int */
    private $defenseId;

    public function __construct(array $attributes, DefenseRepository $defenseRepository)
    {
        $this->defenseRepository = $defenseRepository;
        $this->defenseId = $attributes['defense_id'];
    }

    public function initProgress(QuestInterface $quest, TaskInterface $task): int
    {
        return $this->defenseRepository->getDefenseCount($quest->getUser(), $this->defenseId);
    }
}
