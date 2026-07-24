<?php declare(strict_types=1);

namespace EtoA\Ship;

use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Planet;
use EtoA\Entity\ShipTransform;
use EtoA\Entity\User;

class ShipTransformRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShipTransform::class);
    }

    public function hasUserTransformableObjects(int|User $user, Planet|int $entity): bool
    {
        $defense = (bool) $this->defenseQueryBuilder($user, $entity)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        if ($defense) {
            return true;
        }

        return (bool) $this->shipQueryBuilder($user, $entity)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return ShipTransform[]
     */
    public function getShips(int $userId, int $entityId): array
    {
        $data = $this->shipQueryBuilder($userId, $entityId)
            ->select('ship_id, def_id, num_def')
            ->addSelect('l.shiplist_count as count')
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
            ->fetchAssociative();

        return $data !== false ? ShipTransform::createFromDefense($data) : null;
    }

    private function shipQueryBuilder(int|User $user, Planet|int $entity): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:ShipListItem', 'l', 'WITH', 'l.ship = q.ship')
            ->where('l.user = :user')
            ->andWhere('l.entity = :entity')
            ->andWhere('l.count > 0')
            ->setParameters([
                'user' => $user,
                'entity' => $entity,
            ]);
    }

    private function defenseQueryBuilder(int|User $user, Planet|int $entity): QueryBuilder
    {
        return $this->createQueryBuilder('q')
            ->innerJoin('App:DefenseListItem', 'l', 'WITH', 'l.defense = q.defense')
            ->where('l.user = :user')
            ->andWhere('l.entity = :entity')
            ->andWhere('l.count > 0')
            ->setParameters([
                'user' => $user,
                'entity' => $entity,
            ]);
    }
}
