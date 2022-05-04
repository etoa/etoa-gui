<?php

declare(strict_types=1);

namespace EtoA\Universe\Wormhole;

use EtoA\Core\AbstractRepository;

class WormholeRepository extends AbstractRepository
{
    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select("id")
            ->from('wormholes')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('wormholes')
            ->fetchOne();
    }

    public function getOneId(): ?int
    {
        $id = $this->createQueryBuilder()
            ->select("id")
            ->from('wormholes')
            ->fetchOne();

        return $id !== false ? (int) $id : null;
    }

    public function find(int $id): ?Wormhole
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('wormholes')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->fetchAssociative();

        return $data !== false ? new Wormhole($data) : null;
    }

    /**
     * @return array<Wormhole>
     */
    public function findAll(): array
    {
        $data = $this->createQueryBuilder()
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
        $data = $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
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
        $this->createQueryBuilder()
            ->delete('wormholes')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}
