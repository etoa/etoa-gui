<?php

declare(strict_types=1);

namespace EtoA\Universe\EmptySpace;

use EtoA\Core\AbstractRepository;

class EmptySpaceRepository extends AbstractRepository
{
    /**
     * @return int[]
     */
    public function getAllIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select("id")
            ->from('space')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function count(): int
    {
        return (int) $this->createQueryBuilder()
            ->select("COUNT(id)")
            ->from('space')
            ->execute()
            ->fetchOne();
    }

    public function find(int $id): ?EmptySpace
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

        return $data !== false ? new EmptySpace($data) : null;
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
