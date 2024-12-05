<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceBoardPost;

class AllianceBoardPostRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceBoardPost::class);
    }
    public function getUserAlliancePostCounts(int $allianceId, int $userId): int
    {
        $posts = (int) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->innerJoin('App:AllianceBoardTopic', 't', 'WITH', 't.id = q.topic')
            ->innerJoin('App:AllianceBoardCategory', 'c', 'WITH', 'c.id = t.category')
            ->where('c.alliance = :allianceId')
            ->andWhere('q.userId = :userId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->getQuery()
            ->getSingleResult();

        $bndPosts = (int) $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->innerJoin('App:AllianceBoardTopic', 't', 'WITH', 't.id = q.topic')
            ->innerJoin('App:AllianceDiplomacy', 'b', 'WITH', 'b.id = t.bndId')
            ->where('b.alliance1 = :allianceId OR b.alliance2 = :allianceId')
            ->andWhere('q.userId = :userId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->getQuery()
            ->getSingleResult();

        return $posts + $bndPosts;
    }

    public function addPost(int $topicId, string $text, int $userId, string $userNick): int
    {
        $this->createQueryBuilder('q')
            ->insert('allianceboard_posts')
            ->values([
                'post_topic_id' => ':topicId',
                'post_user_id' => ':userId',
                'post_user_nick' => ':userNick',
                'post_text' => ':text',
                'post_timestamp' => ':timestamp',
            ])
            ->setParameters([
                'topicId' => $topicId,
                'userId' => $userId,
                'userNick' => $userNick,
                'text' => $text,
                'timestamp' => time(),
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }


    public function updatePost(int $postId, string $text, int $authorId = null): void
    {
        $qb = $this->createQueryBuilder('q')
            ->update('allianceboard_posts')
            ->set('post_text', ':text')
            ->set('post_changed', ':now')
            ->where('post_id = :postId')
            ->setParameters([
                'postId' => $postId,
                'now' => time(),
                'text' => $text,
            ]);

        if ($authorId !== null) {
            $qb
                ->andWhere('post_user_id = :authorId')
                ->setParameter('authorId', $authorId);
        }

        $qb->executeQuery();
    }
    /**
     * @return AllianceBoardPost[]
     */
    public function getPosts(int $topicId, int $limit = null): array
    {
      return $this->findBy(['topic'=>$topicId],['timestamp'=>'ASC'],$limit);
    }

    public function getPost(int $postId): ?AllianceBoardPost
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('allianceboard_posts')
            ->where('post_id = :postId')
            ->setParameter('postId', $postId)
            ->fetchAssociative();

        return $data !== false ? new AllianceBoardPost($data) : null;
    }

    public function deletePost(int $postId, int $authorId = null): void
    {
        $qb = $this->createQueryBuilder('q')
            ->delete('allianceboard_posts')
            ->where('post_id = :postId')
            ->setParameter('postId', $postId);

        if ($authorId !== null) {
            $qb
                ->andWhere('post_user_id = :authorId')
                ->setParameter('authorId', $authorId);
        }

        $qb->executeQuery();
    }

    public function getLatestAlliancePost(int $allianceId, int $myRankId = null): ?AllianceBoardPost
    {
        $qb = $this->createQueryBuilder('q')
            ->innerJoin('App:AllianceBoardTopic', 't', 'WITH', 't.id = q.topic')
            ->innerJoin('App:AllianceBoardCategory', 'c', 'WITH', 'c.id = t.category')
            ->where('c.alliance = :allianceId')
            ->orderBy('q.timestamp', 'DESC')
            ->setMaxResults(1)
            ->setParameter('allianceId', $allianceId);

        if ($myRankId !== null) {
            $qb
                ->innerJoin('App:AllianceBoardCategoryRank', 'r', 'WITH', 'r.catId = c.id')
                ->andWhere('r.rankId = :rank')
                ->setParameter('rank', $myRankId);
        }

        $data = $qb
            ->getQuery()
            ->getOneOrNullResult();

        return $data;
    }
}
