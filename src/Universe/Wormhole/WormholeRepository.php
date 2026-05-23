<?php

declare(strict_types=1);

namespace EtoA\Universe\Wormhole;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Entity;
use EtoA\Entity\Wormhole;

class WormholeRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Wormhole::class);
    }

    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        return $this->createQueryBuilder('q')
            ->select("IDENTITY(q.entity)")
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getOne(): ?Wormhole
    {
        return $this->createQueryBuilder('q')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * @return array<Wormhole>
     */
    public function findNonPersistentInRandomOrder(int $changedBefore, ?int $limit = null): array
    {
        return $this->createQueryBuilder('q')
            ->where('q.persistent = 0')
            ->andWhere('IDENTITY(q.target) > 0')
            ->andWhere('q.changed < :changed')
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->setParameters([
                'changed' => $changedBefore,
            ])
            ->getQuery()
            ->execute();
    }

    public function add(Entity $entity, bool $persistent, Wormhole $target = null, bool $flush = true): void
    {
        $wormhole = new Wormhole();
        $wormhole->setChanged(time());
        $wormhole->setPersistent($persistent);
        $wormhole->setTarget($target);

        $entity->setWormhole($wormhole);

        if ($flush) {
            $this->save();
        }
    }

    public function updateTarget(Wormhole $wormhole, Wormhole $target): void
    {
        $wormhole->setTarget($target);

        $this->save();
    }

    public function setPersistent(Wormhole $wormhole, bool $persistent): void
    {
        $wormhole->setPersistent($persistent);

        $this->save();
    }
}
