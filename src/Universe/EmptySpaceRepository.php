<?php

declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\Core\AbstractRepository;

class EmptySpaceRepository extends AbstractRepository
{
    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('space')
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('space')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? $data : null;
    }

    public function add(int $id, int $lastVisited = 0): void
    {
        $this->createQueryBuilder()
            ->insert('space')
            ->values([
                'id' => ':id',
                'lastvisited' => ':lastvisited',
            ])
            ->setParameters([
                'id' => $id,
                'lastvisited' => $lastVisited,
            ])
            ->execute();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('space')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->execute();
    }
}
