<?php declare(strict_types=1);

namespace EtoA\Quest\Log;

use EtoA\Core\AbstractRepository;
use EtoA\Quest\Entity\Quest;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Log\QuestLoggerInterface;

class QuestLogRepository extends AbstractRepository implements QuestLoggerInterface
{
    public function log(QuestInterface $quest, string $previousState, string $transitionName): void
    {
        if (!$quest instanceof Quest) {
            throw new \InvalidArgumentException('$quest must be a instance of Quest');
        }

        $this->createQueryBuilder()
            ->insert('quest_log')
            ->values([
                'user_id' => ':userId',
                'quest_id' => ':questId',
                'quest_data_id' => ':questDataId',
                'slot_id' => ':slotId',
                'previous_state' => ':previousState',
                'transition' => ':transition',
                'date' => ':date',
            ])->setParameters([
                'userId' => $quest->getUser(),
                'questId' => $quest->getId(),
                'questDataId' => $quest->getQuestId(),
                'slotId' => $quest->getSlotId(),
                'previousState' => $previousState,
                'transition' => $transitionName,
                'date' => time(),
            ])->execute();
    }
}
