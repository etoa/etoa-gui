<?php

declare(strict_types=1);

namespace EtoA\Universe\EmptySpace;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\EmptySpace;
use EtoA\Entity\Entity;
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
        return $this->createQueryBuilder('q')
            ->select("q.id")
            ->getQuery()
            ->getSingleColumnResult();
    }

    public function add(Entity $entity, int $lastVisited = 0): void
    {
        $space = new EmptySpace();
        $space->setLastVisited($lastVisited);

        $entity->setEmptySpace($space);

        $this->save();
    }
}
