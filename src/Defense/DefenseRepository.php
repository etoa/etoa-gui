<?php

declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\AbstractRepository;

class DefenseRepository extends AbstractRepository
{
    public function addDefense(int $defenseId, int $amount, int $userId, int $entityId): void
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot add negative defense count');
        }


        $this->changeDefenseCount($defenseId, $amount, $userId, $entityId);
    }

    public function removeDefense(int $defenseId, int $amount, int $userId, int $entityId): int
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException('Cannot remove negative defense count');
        }

        $available = (int) $this->createQueryBuilder()
            ->select('deflist_count')
            ->from('deflist')
            ->where('deflist_def_id = :defenseId')
            ->andWhere('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'defenseId' => $defenseId,
            ])->execute()->fetchOne();

        $amount = min($available, $amount);

        $this->changeDefenseCount($defenseId, -$amount, $userId, $entityId);

        return $amount;
    }

    private function changeDefenseCount(int $defenseId, int $amount, int $userId, int $entityId): void
    {
        $this->getConnection()
            ->executeQuery(
                'INSERT INTO deflist (
                    deflist_user_id,
                    deflist_entity_id,
                    deflist_def_id,
                    deflist_count
                ) VALUES (
                    :userId,
                    :entityId,
                    :defenseId,
                    :amount
                ) ON DUPLICATE KEY
                UPDATE deflist_count = deflist_count + VALUES(deflist_count);
            ',
                [
                    'userId' => $userId,
                    'amount' => max(0, $amount),
                    'entityId' => $entityId,
                    'defenseId' => $defenseId,
                ]
            );
    }


    /**
     * @return array<int, int>
     */
    public function getEntityDefenseCounts(int $userId, int $entityId): array
    {
        $data = $this->createQueryBuilder()
            ->select('deflist_def_id, deflist_count')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->andWhere('deflist_count > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchAllKeyValue();

        return array_map(fn ($value) => (int) $value, $data);
    }

    public function getDefenseCount(int $userId, int $defenseId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('SUM(deflist_count)')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_def_id = :defenseId')
            ->setParameters([
                'userId' => $userId,
                'defenseId' => $defenseId,
            ])->execute()
            ->fetchOne();
    }

    public function countJammingDevicesOnEntity(int $entityId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('dl.deflist_count')
            ->from('deflist', 'dl')
            ->where('dl.deflist_entity_id = :entityId')
            ->andWhere('dl.deflist_count > 0')
            ->innerJoin('dl', 'defense', 'd', 'dl.deflist_def_id = d.def_id AND def_jam = 1')
            ->setParameters([
                'entityId' => $entityId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function removeForEntity(int $entityId): void
    {
        $this->createQueryBuilder()
            ->delete('def_queue')
            ->where('queue_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute();

        $this->createQueryBuilder()
            ->delete('deflist')
            ->where('deflist_entity_id = :entityId')
            ->setParameter('entityId', $entityId)
            ->execute();
    }

    /**
     * @return DefenseQueueItem[]
     */
    public function findQueueItemsForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('def_queue')
            ->where('queue_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('queue_starttime', 'ASC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($row) => new DefenseQueueItem($row), $data);
    }

    public function saveQueueItem(DefenseQueueItem $item): void
    {
        $this->createQueryBuilder()
            ->update('def_queue')
            ->set('queue_user_id', 'userId')
            ->set('queue_def_id', 'defenseId')
            ->set('queue_entity_id', 'entityId')
            ->set('queue_cnt', 'count')
            ->set('queue_starttime', 'startTime')
            ->set('queue_endtime', 'endTime')
            ->set('queue_objtime', 'objectTime')
            ->set('queue_build_type', 'buildType')
            ->set('queue_user_click_time', 'userClickTime')
            ->where('id = :id')
            ->setParameters([
                'id' => $item->id,
                'userId' => $item->userId,
                'defenseId' => $item->defenseId,
                'entityId' => $item->entityId,
                'count' => $item->count,
                'startTime' => $item->startTime,
                'endTime' => $item->endTime,
                'objectTime' => $item->objectTime,
                'buildType' => $item->buildType,
                'userClickTime' => $item->userClickTime,
            ])
            ->execute();
    }
}
