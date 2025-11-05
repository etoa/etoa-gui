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
            ->select("q.id")
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function getOneId(): ?int
    {
        $id = $this->createQueryBuilder('q')
            ->select("id")
            ->from('wormholes')
            ->fetchOne();

        return $id !== false ? (int) $id : null;
    }

    /**
     * @return array<Wormhole>
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('wormholes')
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Wormhole($row), $data);
    }

    /**
     * @return array<Wormhole>
     */
    public function findNonPersistentInRandomOrder(int $changedBefore, ?int $limit = null): array
    {
        $data = $this->createQueryBuilder('q')
            ->select("*")
            ->from('wormholes')
            ->where('persistent = 0')
            ->andWhere('target_id > 0')
            ->andWhere('changed < :changed')
            ->orderBy('RAND()')
            ->setMaxResults($limit)
            ->setParameters([
                'changed' => $changedBefore,
            ])
            ->fetchAllAssociative();

        return array_map(fn ($row) => new Wormhole($row), $data);
    }

    public function add(Entity $entity, bool $persistent, Entity $target = null): void
    {
        $wormhole = new Wormhole();
        $wormhole->setChanged(time());
        $wormhole->setPersistent($persistent);
        $wormhole->setTarget($target);

        $entity->setWormhole($wormhole);

        $this->save();
    }

    public function updateTarget(Wormhole $wormhole, Entity $target): void
    {
        $wormhole->setTarget($target);

        $this->save();
    }

    public function setPersistent(int $id, bool $persistent): void
    {
        $this->createQueryBuilder('q')
            ->update('wormholes')
            ->set('persistent', ':persistent')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'persistent' => (int) $persistent,
            ])
            ->executeQuery();
    }
}
