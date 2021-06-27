<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class WormholeRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('wormholes')
            ->execute()
            ->fetchOne();
    }

    public function getOneId(): ?int
    {
        $id = $this->createQueryBuilder()
            ->select("id")
            ->from('wormholes')
            ->execute()
            ->fetchOne();
        return $id !== false ? (int) $id : null;
    }

    public function find(int $id): ?array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('wormholes')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();
        return $data !== false ? $data : null;
    }

    public function findAll(): array
    {
        return $this->createQueryBuilder()
            ->select("*")
            ->from('wormholes')
            ->execute()
            ->fetchAllAssociative();
    }

    public function add(int $id, bool $persistent): void
    {
        $this->createQueryBuilder()
            ->insert('wormholes')
            ->values([
                'id' => ':id',
                'changed' => ':changed',
                'persistent' => ':persistent',
            ])
            ->setParameters([
                'id' => $id,
                'changed' => time(),
                'persistent' => $persistent,
            ])
            ->execute();
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
            ->execute();
    }

    public function setPersistent(int $id, bool $persistent): void
    {
        $this->createQueryBuilder()
            ->update('wormholes')
            ->set('persistent', ':persistent')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'persistent' => $persistent,
            ])
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('wormholes')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
