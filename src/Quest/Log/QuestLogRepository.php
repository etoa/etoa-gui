<?php

namespace EtoA\Quest\Log;

use EtoA\Core\AbstractRepository;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Log\QuestLoggerInterface;

class QuestLogRepository extends AbstractRepository implements QuestLoggerInterface
{
    public function log(QuestInterface $quest, $previousState, $transitionName)
    {
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
