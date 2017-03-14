<?php

namespace EtoA\Quest;

use EtoA\Core\AbstractRepository;
use EtoA\Quest\Entity\Quest;
use EtoA\Quest\Entity\Task;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Storage\QuestStorageInterface;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestRepository extends AbstractRepository implements QuestStorageInterface
{
    /**
     * @param int $userId
     * @return QuestInterface[]
     */
    public function getActiveQuests($userId)
    {
        $qb = $this->createQueryBuilder();

        $quests = [];
        $result = $qb
            ->select('q.id')
            ->addSelect('q.*')
            ->addSelect('t.*')
            ->from('quests', 'q')
            ->leftJoin('q', 'quest_tasks', 't', 't.quest_id = q.id')
            ->where('q.user_id = :userId')
            ->andWhere($qb->expr()->notIn('q.state', ["'" . QuestDefinitionInterface::STATE_FINISHED . "'", "'" . QuestDefinitionInterface::STATE_REJECTED . "'"]))
            ->setParameters([
                'userId' => $userId,
            ])->execute()->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);

        foreach ($result as $questId => $questData) {
            $tasks = [];
            if (null !== $questData[0]['task_id']) {
                foreach ($questData as $row) {
                    $tasks[$row['task_id']] = new Task($row['id'], $row['task_id'], $row['progress']);
                }
            }

            $quests[] = new Quest($questId, $questData[0]['quest_data_id'], $questData[0]['user_id'], $questData[0]['slot_id'], $questData[0]['state'], $tasks);
        }

        return $quests;
    }

    public function save(QuestInterface $quest)
    {
        if ($quest->getId()) {
            $this->createQueryBuilder()
                ->update('quests')
                ->set('state', ':state')
                ->where('id = :id')
                ->setParameters([
                    'id' => $quest->getId(),
                    'state' => $quest->getState(),
                ])->execute();

            foreach ($quest->getTasks() as $task) {
                $this->createQueryBuilder()
                    ->update('quest_tasks')
                    ->set('progress', $task->getProgress())
                    ->where('id = :id')
                    ->setParameters([
                        'id' => $task->getId(),
                    ])->execute();
            }
        } else {
            $qb = $this->createQueryBuilder();
            $qb
                ->insert('quests')
                ->values([
                    'user_id' => ':userId',
                    'state' => ':state',
                    'slot_id' => ':slotId',
                    'quest_data_id' => ':questId',
                ])->setParameters([
                    'userId' => $quest->getUser(),
                    'state' => $quest->getState(),
                    'slotId' => $quest->getSlotId(),
                    'questId' => $quest->getQuestId(),
                ])->execute();

            $questId = $qb->getConnection()->lastInsertId();
            $quest->setId($questId);

            foreach ($quest->getTasks() as $task) {
                $qb = $this->createQueryBuilder();
                $qb
                    ->insert('quest_tasks')
                    ->values([
                        'task_id' => ':taskId',
                        'quest_id' => ':questId',
                        'progress' => ':progress',
                    ])->setParameters([
                        'taskId' => $task->getTaskId(),
                        'questId' => $questId,
                        'progress' => $task->getProgress(),
                    ])->execute();

                $task->setId($qb->getConnection()->lastInsertId());
            }
        }
    }
}
