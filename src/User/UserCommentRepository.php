<?php declare(strict_types=1);

namespace EtoA\User;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\User;
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
    public function getCommentInformation(User $user): array
    {
        return $this->createQueryBuilder('q')
            ->select('COUNT(q.id) as count, MAX(q.timestamp) as latest')
            ->where('q.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    /**
     * @return UserComment[]
     */
    public function getComments(User|int $user): array
    {
        return $this->findBy(['user'=>$user],['timestamp'=>'DESC']);
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
