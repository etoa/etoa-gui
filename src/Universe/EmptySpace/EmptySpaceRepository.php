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
        $data = $this->createQueryBuilder('q')
            ->select("id")
            ->from('space')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['id'], $data);
    }

    public function add(int $id, int $lastVisited = 0): void
    {
        $this->createQueryBuilder('q')
            ->insert('space')
            ->values([
                'id' => ':id',
                'lastvisited' => ':lastvisited',
            ])
            ->setParameters([
                'id' => $id,
                'lastvisited' => $lastVisited,
            ])
            ->executeQuery();
    }

    public function remove(int $id): void
    {
        $this->createQueryBuilder('q')
            ->delete('space')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }
}
