<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Core\AbstractRepository;
use EtoA\Quest\Entity\Quest;
use EtoA\Quest\Entity\Task;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Storage\QuestNotFoundException;
use LittleCubicleGames\Quests\Storage\QuestStorageInterface;
use LittleCubicleGames\Quests\Workflow\QuestDefinition;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestRepository extends AbstractRepository implements QuestStorageInterface
{
    public function getUserQuest(int $userId, int $questId): QuestInterface
    {
        $result = $this->createQueryBuilder()
            ->select('q.id AS qid')
            ->addSelect('q.*')
            ->addSelect('t.*')
            ->from('quests', 'q')
            ->leftJoin('q', 'quest_tasks', 't', 't.quest_id = q.id')
            ->where('q.user_id = :userId')
            ->andWhere('q.id = :questId')
            ->setParameters([
                'userId' => $userId,
                'questId' => $questId,
            ])->execute()->fetchAll(\PDO::FETCH_ASSOC);

        if (count($result) === 0) {
            throw new QuestNotFoundException();
        }

        return $this->buildQuest((int)$result[0]['qid'], $result);
    }

    /**
     * @return QuestInterface[]
     */
    public function getActiveQuests(int $userId): array
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
            ])
            ->orderBy('q.id')
            ->execute()->fetchAll(\PDO::FETCH_ASSOC | \PDO::FETCH_GROUP);

        foreach ($result as $questId => $questData) {
            $quests[] = $this->buildQuest($questId, $questData);
        }

        return $quests;
    }

    private function buildQuest(int $questId, array $questData): Quest
    {
        $tasks = [];
        if (null !== $questData[0]['task_id']) {
            foreach ($questData as $row) {
                $tasks[$row['task_id']] = new Task((int)$row['id'], (int)$row['task_id'], (int)$row['progress']);
            }
        }

        return new Quest($questId, (int)$questData[0]['quest_data_id'], (int)$questData[0]['user_id'], $questData[0]['slot_id'], $questData[0]['state'], $tasks);
    }

    public function save(QuestInterface $quest): QuestInterface
    {
        if (!$quest instanceof Quest) {
            throw new \InvalidArgumentException('$quest must be a instance of Quest');
        }

        try {
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
                    ->set('progress', (string)$task->getProgress())
                    ->where('id = :id')
                    ->setParameters([
                        'id' => $task->getId(),
                    ])->execute();
            }
        } catch (\RuntimeException $e) {
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

            $questId = (int)$qb->getConnection()->lastInsertId();
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

                $task->setId((int)$qb->getConnection()->lastInsertId());
            }
        }

        return $quest;
    }

    public function searchQuests(?int $questId, ?int $userId, ?string $questState, ?string $userNick): array
    {
        $qb = $this->createQueryBuilder()
            ->addSelect('q.*')
            ->addSelect('t.*')
            ->addSelect('u.user_nick, u.user_points')
            ->from('quests', 'q')
            ->leftJoin('q', 'quest_tasks', 't', 't.quest_id = q.id')
            ->innerJoin('q', 'users', 'u', 'u.user_id=q.user_id');

        $parameters = [];
        if ($questId !== null) {
            $qb
                ->andWhere('q.quest_data_id = :questId');
            $parameters['questId'] = $questId;
        }

        if ($userId !== null) {
            $qb
                ->andWhere('q.user_id = :userId');
            $parameters['userId'] = $userId;
        }

        if ($userNick !== null) {
            $qb
                ->andWhere('u.user_nick LIKE :userNick');
            $parameters['userNick'] = $userNick;
        }

        if ($questState !== null && in_array($questState, QuestDefinition::STATES, true)) {
            $qb
                ->andWhere('q.state = :state');
            $parameters['state'] = $questState;
        }

        return $qb
            ->setParameters($parameters)
            ->orderBy('q.id')
            ->execute()->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getQuest(int $questId): ?array
    {
        $result = $this->createQueryBuilder()
            ->addSelect('q.*')
            ->addSelect('t.*')
            ->addSelect('u.user_nick, u.user_points')
            ->from('quests', 'q')
            ->leftJoin('q', 'quest_tasks', 't', 't.quest_id = q.id')
            ->innerJoin('q', 'users', 'u', 'u.user_id=q.user_id')
            ->where('q.id = :questId')
            ->setParameter('questId', $questId)
            ->execute()->fetchAll(\PDO::FETCH_ASSOC);

        if (count($result) === 0) {
            return null;
        }

        return $result[0];
    }

    public function updateQuest(int $questId, string $questState): void
    {
        if (!in_array($questState, QuestDefinition::STATES, true)) {
            throw new \InvalidArgumentException('Invalid quest state: ' .$questState);
        }

        $this->createQueryBuilder()
            ->update('quests')
            ->set('state', ':state')
            ->where('id = :questId')
            ->setParameters([
                'questId' => $questId,
                'state' => $questState,
            ])
            ->execute();
    }

    public function deleteQuest(int $questId): void
    {
        $this->createQueryBuilder()
            ->delete('quest_tasks')
            ->where('quest_id = :questId')
            ->setParameter('questId', $questId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('quests')
            ->where('id = :questId')
            ->setParameter('questId', $questId)
            ->execute();
    }
}
