<?php

declare(strict_types=1);

namespace EtoA\Bookmark;

use EtoA\Core\AbstractRepository;

class BookmarkRepository extends AbstractRepository
{
    /**
     * @return array<Bookmark>
     */
    public function findForUser(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('bookmarks')
            ->where('user_id = :userId')
            ->setParameters([
                'userId' => $userId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn ($arr) => new Bookmark($arr), $data);
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
            ->update('comment = :comment')
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
