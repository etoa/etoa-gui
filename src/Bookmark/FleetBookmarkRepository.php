<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\FleetBookmark;
use EtoA\Entity\User;

class FleetBookmarkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, FleetBookmark::class);
    }

    /**
     * @return FleetBookmark[]
     */
    public function findForUser(User $user): array
    {
        return $this->findBy(['user' => $user], ['name' => 'ASC']);
    }

    public function findOneForUser(int $id, User $user): ?FleetBookmark
    {
        return $this->findOneBy(['id' => $id, 'user' => $user]);
    }

    public function removeForUser(User $user): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
