<?php

declare(strict_types=1);

namespace EtoA\Universe\Wormhole;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
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
        $data = $this->createQueryBuilder('q')
            ->select("id")
            ->from('wormholes')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
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

    public function add(int $id, bool $persistent, int $targetId = 0): void
    {
        $this->createQueryBuilder('q')
            ->insert('wormholes')
            ->values([
                'id' => ':id',
                'changed' => ':changed',
                'persistent' => ':persistent',
                'target_id' => ':targetId',
            ])
            ->setParameters([
                'id' => $id,
                'changed' => time(),
                'persistent' => (int) $persistent,
                'targetId' => $targetId,
            ])
            ->executeQuery();
    }

    public function updateTarget(int $id, int $targetId): void
    {
        $this->createQueryBuilder('q')
            ->update('wormholes')
            ->set('target_id', ':target_id')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'target_id' => $targetId,
            ])
            ->executeQuery();
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

    public function remove(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('wormholes')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}
