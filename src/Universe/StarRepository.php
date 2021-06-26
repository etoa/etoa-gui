<?php

declare(strict_types=1);

namespace EtoA\Universe;

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

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('stars')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
