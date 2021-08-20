<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\Core\AbstractRepository;

class ShipTransformRepository extends AbstractRepository
{
    public function hasUserTransformableObjects(int $userId, int $entityId): bool
    {
        $defense = (bool) $this->defenseQueryBuilder($userId, $entityId)
            ->select('1')
            ->execute()
            ->fetchOne();

        if ($defense) {
            return true;
        }

        return (bool) $this->shipQueryBuilder($userId, $entityId)
            ->select('1')
            ->execute()
            ->fetchOne();
    }

    /**
     * @return ShipTransform[]
     */
    public function getShips(int $userId, int $entityId): array
    {
        $data = $this->shipQueryBuilder($userId, $entityId)
            ->select('ship_id, def_id, num_def')
            ->addSelect('l.shiplist_count as count')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => ShipTransform::createFromShip($row), $data);
    }

    /**
     * @return ShipTransform[]
     */
    public function getDefenses(int $userId, int $entityId): array
    {
        $data = $this->defenseQueryBuilder($userId, $entityId)
            ->select('ship_id, def_id, num_def')
            ->addSelect('l.deflist_count as count')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => ShipTransform::createFromDefense($row), $data);
    }

    public function getShip(int $userId, int $entityId, int $shipId): ?ShipTransform
    {
        $data = $this->shipQueryBuilder($userId, $entityId)
            ->select('ship_id, def_id, num_def')
            ->addSelect('l.shiplist_count as count')
            ->andWhere('l.shiplist_ship_id = :shipId')
            ->setParameter('shipId', $shipId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? ShipTransform::createFromShip($data) : null;
    }

    public function getDefense(int $userId, int $entityId, int $defenseId): ?ShipTransform
    {
        $data = $this->defenseQueryBuilder($userId, $entityId)
            ->select('ship_id, def_id, num_def')
            ->addSelect('l.deflist_count as count')
            ->andWhere('l.deflist_def_id = :defenseId')
            ->setParameter('defenseId', $defenseId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? ShipTransform::createFromDefense($data) : null;
    }

    private function shipQueryBuilder(int $userId, int $entityId): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->from('obj_transforms')
            ->innerJoin('obj_transforms', 'shiplist', 'l', 'l.shiplist_ship_id = ship_id')
            ->where('l.shiplist_user_id = :userId')
            ->andWhere('l.shiplist_entity_id = :entityId')
            ->andWhere('l.shiplist_count > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ]);
    }

    private function defenseQueryBuilder(int $userId, int $entityId): QueryBuilder
    {
        return $this->createQueryBuilder()
            ->from('obj_transforms')
            ->innerJoin('obj_transforms', 'deflist', 'l', 'l.deflist_def_id = def_id')
            ->where('l.deflist_user_id = :userId')
            ->andWhere('l.deflist_entity_id = :entityId')
            ->andWhere('l.deflist_count > 0')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
            ]);
    }
}
