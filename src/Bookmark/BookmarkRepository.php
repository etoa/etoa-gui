<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Core\AbstractRepository;

class BookmarkRepository extends AbstractRepository
{
    /**
     * @return array<Bookmark>
     */
    public function findForUser(int $userId, BookmarkOrder $order = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('bookmarks.*')
            ->addSelect('entities.code as entityCode')
            ->from('bookmarks')
            ->innerJoin('bookmarks', 'entities', 'entities', 'bookmarks.entity_id = entities.id')
            ->where('bookmarks.user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ]);

        if ($order !== null) {
            if ($order->order === BookmarkOrder::ORDER_OWNER) {
                $qb
                    ->leftJoin('bookmarks', 'planets', 'planets', 'bookmarks.entity_id = planets.id')
                    ->leftJoin('planets', 'users', 'users', 'planets.planet_user_id = users.user_id');
            }

            $qb
                ->orderBy($order->order, $order->direction);
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new Bookmark($arr), $data);
    }

    /**
     * @return BookmarkEntity[]
     */
    public function getBookmarkedEntities(int $userId): array
    {
        $data = $this->createQueryBuilder()
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
                'stars.name as star_name',
                'comment'
            )
            ->from('bookmarks')
            ->innerJoin('bookmarks', 'entities', 'e', 'e.id = bookmarks.entity_id')
            ->leftJoin('e', 'planets', 'planets', 'e.id = planets.id')
            ->leftJoin('e', 'stars', 'stars', 'e.id = stars.id')
            ->innerJoin('e', 'cells', 'c', 'e.cell_id = c.id')
            ->where('bookmarks.user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('bookmarks.comment')
            ->addOrderBy('bookmarks.entity_id')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new BookmarkEntity($row), $data);
    }

    public function getBookmark(int $id, int $userId): ?Bookmark
    {
        $data = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Bookmark($data) : null;
    }

    public function hasEntityBookmark(int $userId, int $entity): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('bookmarks')
            ->where('entity_id = :entityId')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'entityId' => $entity,
            ])
            ->execute()
            ->fetchOne();
    }

    public function add(int $userId, int $entityId, string $comment): int
    {
        $this->createQueryBuilder()
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
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function updateComment(int $id, int $userId, string $comment): bool
    {
        return (bool) $this->createQueryBuilder()
            ->update('bookmarks')
            ->set('comment', ':comment')
            ->where('id = :id')
            ->andWhere('user_id = :userId')
            ->setParameters([
                'id' => $id,
                'userId' => $userId,
                'comment' => $comment,
            ])
            ->execute();
    }

    public function remove(int $id, int $userId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->delete('bookmarks')
            ->where('user_id = :userId')
            ->andWhere('id = :id')
            ->setParameters([
                'userId' => $userId,
                'id' => $id,
            ])
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('bookmarks')
            ->where('user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
