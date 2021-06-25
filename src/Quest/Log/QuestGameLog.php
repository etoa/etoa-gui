<?php declare(strict_types=1);

namespace EtoA\Quest\Log;

use EtoA\Core\Logging\GameLog;
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

    private GameLog $gameLog;

    public function __construct(
        GameLog $gameLog
    ) {
        $this->gameLog = $gameLog;
    }

    public function log(QuestInterface $quest, string $previousState, string $transitionName): void
    {
        $this->gameLog->add(GameLog::F_QUESTS, GameLog::INFO, '', $quest->getUser(), 0, 0, $quest->getQuestId(), self::TRANSITION_MAP[$transitionName]);
    }
}
