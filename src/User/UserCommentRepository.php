<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class UserCommentRepository extends AbstractRepository
{
    /**
     * @return array{count: int, latest: int}
     */
    public function getCommentInformation(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('COUNT(comment_id) count, MAX(comment_timestamp) latest')
            ->from('user_comments')
            ->where('comment_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute()
            ->fetchAssociative();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return UserComment[]
     */
    public function getComments(int $userId): array
    {
        $data = $this->createQueryBuilder()
            ->select('c.*')
            ->addSelect('a.user_nick')
            ->from('user_comments', 'c')
            ->leftJoin('c', 'admin_users', 'a', 'c.comment_admin_id = a.user_id')
            ->where('comment_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('comment_timestamp', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserComment($row), $data);
    }

    public function addComment(int $userId, int $adminUserId, string $text): void
    {
        $this->createQueryBuilder()
            ->insert('user_comments')
            ->values([
                'comment_timestamp' => ':now',
                'comment_user_id' => ':userId',
                'comment_admin_id' => ':adminUserId',
                'comment_text' => ':text',
            ])
            ->setParameters([
                'now' => time(),
                'userId' => $userId,
                'adminUserId' => $adminUserId,
                'text' => $text,
            ])
            ->execute();
    }

    public function deleteComment(int $commentId): void
    {
        $this->createQueryBuilder()
            ->delete('user_comments')
            ->where('comment_id = :id')
            ->setParameter('id', $commentId)
            ->execute();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function getOrphanedCount(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->select('count(comment_id)')
            ->from('user_comments')
            ->where($qb->expr()->notIn('comment_user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute()
            ->fetchOne();
    }

    /**
     * @param int[] $availableUserIds
     */
    public function deleteOrphaned(array $availableUserIds): int
    {
        $qb = $this->createQueryBuilder();

        return (int) $qb
            ->delete('user_comments')
            ->where($qb->expr()->notIn('comment_user_id', ':userIds'))
            ->setParameter('userIds', $availableUserIds, Connection::PARAM_INT_ARRAY)
            ->execute();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder()
            ->delete('user_comments')
            ->where('comment_user_id = :userId')
            ->setParameter('userId', $userId)
            ->execute();
    }
}
