<?php declare(strict_types=1);

namespace EtoA\Defense;

class DefenseRepository extends \EtoA\Core\AbstractRepository
{
    public function addDefense(int $defenseId, int $amount, int $userId, int $entityId): void
    {
        $this->getConnection()->executeQuery('INSERT INTO deflist (
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
        ', [
            'userId' => $userId,
            'amount' => max(0, $amount),
            'entityId' => $entityId,
            'defenseId' => $defenseId,
        ]);
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
}
