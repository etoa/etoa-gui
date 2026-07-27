<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Bookmark;
use EtoA\Entity\Entity;
use EtoA\Entity\User;

class BookmarkRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Bookmark::class);
    }

    /**
     * @return array<Bookmark>
     */
    public function findForUser(User $user, BookmarkOrder $order = null): array
    {
        $qb = $this->createQueryBuilder('bookmarks')
            ->innerJoin('bookmarks.entity', 'entities')
            ->where('bookmarks.user = :user')
            ->setParameter('user', $user);

        if ($order !== null) {
            if ($order->requiresOwnerJoin()) {
                $qb
                    ->leftJoin('entities.planet', 'planets')
                    ->leftJoin('planets.user', 'users');
            }

            $qb->orderBy($order->order, $order->direction);
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    public function findOneForUser(int $id, User $user): ?Bookmark
    {
        return $this->findOneBy(['id' => $id, 'user' => $user]);
    }

    public function hasBookmark(User $user, Entity $entity): bool
    {
        return $this->findOneBy(['user' => $user, 'entity' => $entity]) !== null;
    }

    /**
     * @return BookmarkEntity[]
     */
    public function getBookmarkedEntities(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'e.id',
                'c.id as cid',
                'code',
                'pos',
                'sx',
                'sy',
                'cx',
                'cy',
                'planet_name',
                'planets.planet_user_main',
                'planets.planet_type_id as planet_type',
                'stars.name as star_name',
                'stars.type_id as star_type',
                'comment',
                'users.user_nick, users.user_id'
            )
            ->from('bookmarks')
            ->innerJoin('bookmarks', 'entities', 'e', 'e.id = bookmarks.entity_id')
            ->leftJoin('e', 'planets', 'planets', 'e.id = planets.id')
            ->leftJoin('e', 'stars', 'stars', 'e.id = stars.id')
            ->leftJoin('planets', 'users', 'users', 'users.user_id = planets.planet_user_id')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->where('bookmarks.user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('bookmarks.comment')
            ->addOrderBy('bookmarks.entity_id')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new BookmarkEntity($row), $data);
    }

    public function getBookmark(int $id, int $userId): ?Bookmark
    {
        $data = $this->createQueryBuilder('q')
            ->select('b.*')
            ->addSelect('e.code as entityCode')
            ->from('bookmarks', 'b')
            ->innerJoin('b', 'entities', 'e', 'b.entity_id=e.id')
            ->where('b.user_id = :userId')
            ->where('b.id = :id')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
            ])
            ->fetchAssociative();

        return $data !== false ? new Bookmark($data) : null;
    }

    public function hasEntityBookmark(int $userId, int $entity): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->select('1')
            ->from('bookmarks')
            ->where('entity_id = :entityId')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entity,
            ])
            ->fetchOne();
    }

    public function add(int $userId, int $entityId, string $comment): int
    {
        $this->createQueryBuilder('q')
            ->insert('bookmarks')
            ->values([
                'user_id' => ':userId',
                'entity_id' => ':entityId',
                'comment' => ':comment',
            ])
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entityId,
                'comment' => $comment,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updateComment(int $id, int $userId, string $comment): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->update('bookmarks')
            ->set('comment', ':comment')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'comment' => $comment,
            ])
            ->executeQuery()
            ->rowCount();
    }

    public function removeForUser(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }
}
