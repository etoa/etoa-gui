<?php declare(strict_types=1);

namespace EtoA\Quest\Log;

use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Log\QuestLoggerInterface;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestGameLog implements QuestLoggerInterface
{
    public const TRANSITION_MAP = [
        QuestDefinitionInterface::TRANSITION_START => 0,
        QuestDefinitionInterface::TRANSITION_COMPLETE => 1,
        QuestDefinitionInterface::TRANSITION_COLLECT_REWARD => 2,
        QuestDefinitionInterface::TRANSITION_ABORT => 3,
        QuestDefinitionInterface::TRANSITION_REJECT => 4,
    ];

    public function log(QuestInterface $quest, string $previousState, string $transitionName): void
    {
        \GameLog::add(\GameLog::F_QUESTS, \GameLog::INFO, '', $quest->getUser(), 0, 0, $quest->getQuestId(), self::TRANSITION_MAP[$transitionName]);
    }
}
