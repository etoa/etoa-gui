<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\UserComment;

class UserCommentRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserComment::class);
    }

    /**
     * @return array{count: int, latest: int}
     */
    public function getCommentInformation(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('COUNT(comment_id) count, MAX(comment_timestamp) latest')
            ->from('user_comments')
            ->where('comment_user_id = :userId')
            ->setParameter('userId', $userId)
            ->fetchAssociative();

        return array_map(fn ($value) => (int) $value, $data);
    }

    /**
     * @return UserComment[]
     */
    public function getComments(int $userId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('c.*')
            ->addSelect('a.user_nick')
            ->from('user_comments', 'c')
            ->leftJoin('c', 'admin_users', 'a', 'c.comment_admin_id = a.user_id')
            ->where('comment_user_id = :userId')
            ->setParameter('userId', $userId)
            ->orderBy('comment_timestamp', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new UserComment($row), $data);
    }

    public function addComment(int $userId, int $adminUserId, string $text): void
    {
        $this->createQueryBuilder('q')
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
            ->executeQuery();
    }

    public function deleteComment(int $commentId): void
    {
        $this->createQueryBuilder('q')
            ->delete('user_comments')
            ->where('comment_id = :id')
            ->setParameter('id', $commentId)
            ->executeQuery();
    }

    public function removeForUser(int $userId) : void
    {
        $this->createQueryBuilder('q')
            ->delete('user_comments')
            ->where('comment_user_id = :userId')
            ->setParameter('userId', $userId)
            ->executeQuery();
    }
}
