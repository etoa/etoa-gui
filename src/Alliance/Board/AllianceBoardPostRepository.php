<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use EtoA\Core\AbstractRepository;

class AllianceBoardPostRepository extends AbstractRepository
{
    public function getUserAlliancePostCounts(int $allianceId, int $userId): int
    {
        $posts = (int) $this->createQueryBuilder()
            ->select('COUNT(p.post_id)')
            ->from('allianceboard_cat', 'c')
            ->innerJoin('c', 'allianceboard_topics', 't', 't.topic_cat_id = c.cat_id')
            ->innerJoin('t', 'allianceboard_posts', 'p', 'p.post_topic_id = t.topic_id')
            ->where('c.cat_alliance_id = :allianceId')
            ->andWhere('p.post_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->execute()
            ->fetchOne();

        $bndPosts = (int) $this->createQueryBuilder()
            ->select('COUNT(p.post_id)')
            ->from('alliance_bnd', 'b')
            ->innerJoin('b', 'allianceboard_topics', 't', 't.topic_bnd_id = b.alliance_bnd_id')
            ->innerJoin('t', 'allianceboard_posts', 'p', 'p.post_topic_id = t.topic_id')
            ->where('b.alliance_bnd_alliance_id1 = :allianceId OR b.alliance_bnd_alliance_id2 = :allianceId')
            ->andWhere('p.post_user_id = :userId')
            ->setParameters([
                'userId' => $userId,
                'allianceId' => $allianceId,
            ])
            ->execute()
            ->fetchOne();

        return $posts + $bndPosts;
    }

    public function addPost(int $topicId, string $text, int $userId, string $userNick): int
    {
        $this->createQueryBuilder()
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
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }


    public function updatePost(int $postId, string $text, int $authorId = null): void
    {
        $qb = $this->createQueryBuilder()
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

        $qb->execute();
    }
    /**
     * @return Post[]
     */
    public function getPosts(int $topicId, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('allianceboard_posts')
            ->where('post_topic_id = :topicId')
            ->setParameter('topicId', $topicId)
            ->orderBy('post_timestamp', 'ASC');

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Post($row), $data);
    }

    public function getPost(int $postId): ?Post
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('allianceboard_posts')
            ->where('post_id = :postId')
            ->setParameter('postId', $postId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Post($data) : null;
    }

    public function deletePost(int $postId, int $authorId = null): void
    {
        $qb = $this->createQueryBuilder()
            ->delete('allianceboard_posts')
            ->where('post_id = :postId')
            ->setParameter('postId', $postId);

        if ($authorId !== null) {
            $qb
                ->andWhere('post_user_id = :authorId')
                ->setParameter('authorId', $authorId);
        }

        $qb->execute();
    }
}
