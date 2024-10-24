<?php

declare(strict_types=1);

namespace EtoA\Universe\EmptySpace;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\EmptySpace;
use EtoA\Entity\User;

class EmptySpaceRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EmptySpace::class);
    }

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
