<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Missile;

class MissileDataRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Missile::class);
    }

    /**
     * @return array<int, Missile>
     */
    public function getMissileNames(bool $showAll = false, bool $orderById = false): array
    {
        $qb = $this->createQueryBuilder('q');

        if (!$showAll) {
            $qb->where('q.show = 1');
        }

        return $qb
            ->orderBy($orderById ? 'q.id' : 'q.name')
            ->getQuery()
            ->execute();
    }

    public function getMissile(int $missileId): ?Missile
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('missiles')
            ->where('missile_show=1')
            ->andWhere('missile_id = :missileId')
            ->setParameter('missileId', $missileId)
            ->fetchAssociative();

        return $data !== false ? new Missile($data) : null;
    }

    /**
     * @return Missile[]
     */
    public function getMissiles(): array
    {
        return $this->findBy(['show'=>1]);
    }
}
