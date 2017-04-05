<?php

namespace EtoA\Defense;

class DefenseRepository extends \EtoA\Core\AbstractRepository
{
    public function addDefense($defenseId, $amount, $userId, $entityId)
    {
        $hasShips = $this->createQueryBuilder()
            ->select('deflist_id')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_entity_id = :entityId')
            ->andWhere('deflist_def_id = :defenseId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'defenseId' => $defenseId,
            ])->execute()->fetchColumn();

        if ($hasShips) {
            $this->createQueryBuilder()
                ->update('deflist')
                ->set('deflist_count', 'deflist_count + :amount')
                ->where('deflist_def_id = :defenseId')
                ->andWhere('deflist_entity_id = :entityId')
                ->andWhere('deflist_user_id = :userId')
                ->setParameters([
                    'amount' => $amount,
                    'defenseId' => $defenseId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->execute();
        } else {
            $this->createQueryBuilder()
                ->insert('deflist')
                ->values([
                    'deflist_count' => ':amount',
                    'deflist_def_id' => ':defenseId',
                    'deflist_entity_id' => ':entityId',
                    'deflist_user_id' => ':userId',
                ])
                ->setParameters([
                    'amount' => $amount,
                    'defenseId' => $defenseId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->execute();
        }
    }

    public function getDefenseCount($userId, $defenseId)
    {
        return (int)$this->createQueryBuilder()
            ->select('SUM(deflist_count)')
            ->from('deflist')
            ->where('deflist_user_id = :userId')
            ->andWhere('deflist_def_id = :defenseId')
            ->setParameters([
                'userId' => $userId,
                'defenseId' => $defenseId,
            ])->execute()
            ->fetchColumn();
    }
}
