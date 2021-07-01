<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use EtoA\Core\AbstractRepository;

class StarRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('stars')
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('stars')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? $data : null;
    }

    public function add(int $id, int $typeId): void
    {
        $this->createQueryBuilder()
            ->insert('stars')
            ->values([
                'id' => ':id',
                'type_id' => ':type_id',
            ])
            ->setParameters([
                'id' => $id,
                'type_id' => $typeId,
            ])
            ->execute();
    }

    public function update(int $id, int $typeId, string $name): bool
    {
        $affected = (int) $this->createQueryBuilder()
            ->update('stars')
            ->set('type_id', ':type_id')
            ->set('name', ':name')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'type_id' => $typeId,
                'name' => $name,
            ])
            ->execute();

        return $affected > 0;
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('stars')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
