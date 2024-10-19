<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;

class AllianceBoardTopicRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Topic::class);
    }

    /**
     * @param int[] $topicIds
     * @return array<int, int>
     */
    public function getTopicPostCounts(array $topicIds): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('p.post_topic_id, COUNT(p.post_id)')
            ->from('allianceboard_posts', 'p')
            ->where('p.post_topic_id IN (:topicIds)')
            ->groupBy('p.post_topic_id')
            ->setParameter('topicIds', $topicIds, ArrayParameterType::INTEGER)
            ->fetchAllKeyValue();

        $counts = [];
        foreach ($topicIds as $topicId) {
            $counts[$topicId] = (int) ($data[$topicId] ?? 0);
        }

        return $counts;
    }

    /**
     * @param int[] $bndIds
     * @return array<int, int>
     */
    public function getBndTopicCounts(array $bndIds): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('topic_bnd_id, COUNT(topic_id)')
            ->from('allianceboard_topics')
            ->where('topic_bnd_id IN (:bndIds)')
            ->setParameter('bndIds', $bndIds, ArrayParameterType::INTEGER)
            ->fetchAllAssociative();

        $counts = [];
        foreach ($bndIds as $bndId) {
            $counts[$bndId] = (int) ($data[$bndId] ?? 0);
        }

        return $counts;
    }

    /**
     * @param int[] $bndIds
     * @return array<int, int>
     */
    public function getBndPostCounts(array $bndIds): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('t.topic_bnd_id, COUNT(p.post_id)')
            ->from('allianceboard_topics', 't')
            ->innerJoin('t', 'allianceboard_posts', 'p', 'p.post_topic_id = t.topic_id')
            ->where('t.topic_bnd_id IN (:bndIds)')
            ->setParameter('bndIds', $bndIds, ArrayParameterType::INTEGER)
            ->fetchAllAssociative();

        $counts = [];
        foreach ($bndIds as $bndId) {
            $counts[$bndId] = (int) ($data[$bndId] ?? 0);
        }

        return $counts;
    }
    /**
     * @return Topic[]
     */
    public function getTopics(int $categoryId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('allianceboard_topics')
            ->where('topic_cat_id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('topic_top', 'DESC')
            ->addOrderBy('topic_timestamp', 'DESC')
            ->addOrderBy('topic_subject', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Topic($row), $data);
    }

    /**
     * @return Topic[]
     */
    public function getBndTopics(int $bndId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('allianceboard_topics')
            ->where('topic_bnd_id = :bndId')
            ->setParameter('bndId', $bndId)
            ->orderBy('topic_top', 'DESC')
            ->addOrderBy('topic_timestamp', 'DESC')
            ->addOrderBy('topic_subject', 'ASC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Topic($row), $data);
    }

    public function getTopic(int $topicId, int $bndId = null): ?Topic
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('allianceboard_topics')
            ->where('topic_id = :topicId')
            ->setParameter('topicId', $topicId);

        if ($bndId !== null) {
            $qb
                ->andWhere('topic_bnd_id = :bndId')
                ->andWhere('topic_cat_id = 0')
                ->setParameter('bndId', $bndId);
        }

        $data = $qb
            ->fetchAssociative();

        return $data !== false ? new Topic($data) : null;
    }

    public function getAllianceTopicWithLatestPost(int $allianceId, int $myRankId = null): ?TopicWithLatestPost
    {
        $qb = $this->createQueryBuilder('q')
            ->select('t.*', 'p.*')
            ->from('allianceboard_cat', 'c')
            ->innerJoin('c', 'allianceboard_topics', 't', 't.topic_cat_id = c.cat_id')
            ->innerJoin('t', 'allianceboard_posts', 'p', 'p.post_topic_id = t.topic_id')
            ->where('c.cat_alliance_id = :allianceId')
            ->orderBy('p.post_timestamp', 'DESC')
            ->setMaxResults(1)
            ->setParameter('allianceId', $allianceId);

        if ($myRankId !== null) {
            $qb
                ->innerJoin('c', 'allianceboard_catranks', 'r', 'r.cr_cat_id = c.cat_id')
                ->andWhere('r.cr_rank_id = :rank')
                ->setParameter('rank', $myRankId);
        }

        $data = $qb
            ->fetchAssociative();

        return $data !== false ? new TopicWithLatestPost($data) : null;
    }

    public function getTopicWithLatestPost(int $categoryId, int $bndId = null): ?TopicWithLatestPost
    {
        $qb = $this->createQueryBuilder('q')
            ->select('*')
            ->from('allianceboard_topics', 't')
            ->innerJoin('t', 'allianceboard_posts', 'p', 'p.post_topic_id = t.topic_id')
            ->orderBy('p.post_timestamp', 'DESC')
            ->setMaxResults(1);

        if ($bndId !== null) {
            $qb
                ->andWhere('t.topic_bnd_id = :bndId')
                ->setParameter('bndId', $bndId);
        } else {
            $qb
                ->andWhere('t.topic_cat_id = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        $data = $qb
            ->fetchAssociative();

        return $data !== false ? new TopicWithLatestPost($data) : null;
    }

    public function addTopic(string $subject, ?int $bndId, int $categoryId, int $userId, string $userNick): int
    {
        $this->createQueryBuilder('q')
            ->insert('allianceboard_topics')
            ->values([
                'topic_subject' => ':subject',
                'topic_bnd_id' => ':bndId',
                'topic_cat_id' => ':categoryId',
                'topic_user_id' => ':userId',
                'topic_user_nick' => ':userNick',
                'topic_timestamp' => ':now',
            ])
            ->setParameters([
                'subject' => $subject,
                'bndId' => $bndId,
                'categoryId' => $categoryId,
                'userId' => $userId,
                'userNick' => $userNick,
                'now' => time(),
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }


    public function updateTopic(int $topicId, string $subject, int $bndId, int $categoryId, bool $top, bool $closed): void
    {
        $this->createQueryBuilder('q')
            ->update('allianceboard_topics')
            ->set('topic_subject', ':subject')
            ->set('topic_top', ':top')
            ->set('topic_closed', ':closed')
            ->set('topic_cat_id', ':categoryId')
            ->set('topic_bnd_id', ':bndId')
            ->where('topic_id = :topicId')
            ->setParameters([
                'topicId' => $topicId,
                'subject' => $subject,
                'top' => (int) $top,
                'closed' => (int) $closed,
                'categoryId' => $categoryId,
                'bndId' => $bndId,
            ])
            ->executeQuery();
    }

    public function updateTopicTimestamp(int $topicId): void
    {
        $this->createQueryBuilder('q')
            ->update('allianceboard_topics')
            ->set('topic_timestamp', ':now')
            ->where('topic_id = :topicId')
            ->setParameters([
                'topicId' => $topicId,
                'now' => time(),
            ])
            ->executeQuery();
    }

    public function increaseTopicCount(int $topicId): void
    {
        $this->createQueryBuilder('q')
            ->update('allianceboard_topics')
            ->set('topic_count', 'topic_count + 1')
            ->where('topic_id = :topicId')
            ->setParameters([
                'topicId' => $topicId,
            ])
            ->executeQuery();
    }

    public function deleteTopic(int $topicId): void
    {
        $this->createQueryBuilder('q')
            ->delete('allianceboard_posts')
            ->where('post_topic_id = :topicId')
            ->setParameter('topicId', $topicId)
            ->executeQuery();

        $this->createQueryBuilder('q')
            ->delete('allianceboard_topics')
            ->where('topic_id = :topicId')
            ->setParameter('topicId', $topicId)
            ->executeQuery();
    }

    public function deleteBndTopic(int $bndId): void
    {
        $topicIds = array_column($this->createQueryBuilder('q')
            ->select('topic_id')
            ->from('allianceboard_topics')
            ->where('topic_bnd_id = :bndId')
            ->setParameter('bndId', $bndId)
            ->fetchAllAssociative(), 'topic_id');

        if (count($topicIds) > 0) {
            $this->createQueryBuilder('q')
                ->delete('allianceboard_posts')
                ->where('post_topic_id IN (:topicId)')
                ->setParameter('topicId', $topicIds, ArrayParameterType::INTEGER)
                ->executeQuery();
        }

        $this->createQueryBuilder('q')
            ->delete('allianceboard_topics')
            ->where('topic_bnd_id = :bndId')
            ->setParameter('bndId', $bndId)
            ->executeQuery();
    }
}
