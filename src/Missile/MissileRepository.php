<?php declare(strict_types=1);

namespace EtoA\Missile;

class MissileRepository extends \EtoA\Core\AbstractRepository
{
    public function addMissile(int $missileId, int $amount, int $userId, int $entityId): void
    {
        $hasMissiles = $this->createQueryBuilder()
            ->select('missilelist_id')
            ->from('missilelist')
            ->where('missilelist_user_id = :userId')
            ->andWhere('missilelist_entity_id = :entityId')
            ->andWhere('missilelist_missile_id = :missileId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'missileId' => $missileId,
            ])->execute()->fetchColumn();

        if ($hasMissiles) {
            $this->createQueryBuilder()
                ->update('missilelist')
                ->set('missilelist_count', 'missilelist_count + :amount')
                ->where('missilelist_missile_id = :missileId')
                ->andWhere('missilelist_entity_id = :entityId')
                ->andWhere('missilelist_user_id = :userId')
                ->setParameters([
                    'amount' => $amount,
                    'missileId' => $missileId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->execute();
        } else {
            $this->createQueryBuilder()
                ->insert('missilelist')
                ->values([
                    'missilelist_count' => ':amount',
                    'missilelist_missile_id' => ':missileId',
                    'missilelist_entity_id' => ':entityId',
                    'missilelist_user_id' => ':userId',
                ])
                ->setParameters([
                    'amount' => $amount,
                    'missileId' => $missileId,
                    'userId' => $userId,
                    'entityId' => $entityId,
                ])->execute();
        }
    }
}
