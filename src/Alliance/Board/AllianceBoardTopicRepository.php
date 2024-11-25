<?php declare(strict_types=1);

namespace EtoA\Alliance\Board;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceBoardPost;
use EtoA\Entity\AllianceBoardTopic;

class AllianceBoardTopicRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceBoardTopic::class);
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
     * @return AllianceBoardTopic[]
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

        return array_map(fn (array $row) => new AllianceBoardTopic($row), $data);
    }

    /**
     * @return AllianceBoardTopic[]
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

        return array_map(fn (array $row) => new AllianceBoardTopic($row), $data);
    }

    /**
     * @param int[] $categoryIds
     * @return array<int, int>
     */
    public function getTopicPostCountsByCategory(array $categoryIds): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.category)')
            ->addSelect('COUNT(q.id)')
            ->innerJoin('App:AllianceBoardPost', 'p', 'WITH', 'p.topic = q.id')
            ->where('q.category IN (:categoryIds)')
            ->groupBy('q.category')
            ->setParameter('categoryIds', $categoryIds, ArrayParameterType::INTEGER)
            ->getQuery()
            ->execute();

        $counts = [];
        foreach ($categoryIds as $categoryId) {
            $counts[$categoryId] = (int) ($data[$categoryId] ?? 0);
        }

        return $counts;
    }

    public function getCategoryTopicCounts(array $categoryIds): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.category)')
            ->addSelect('COUNT(q.id)')
            ->where('q.category IN (:categoryIds)')
            ->groupBy('q.category')
            ->setParameter('categoryIds', $categoryIds, ArrayParameterType::INTEGER)
            ->getQuery()
            ->execute();

        $counts = [];
        foreach ($categoryIds as $categoryId) {
            $counts[$categoryId] = (int) ($data[$categoryId] ?? 0);
        }

        return $counts;
    }

    public function getTopic(int $topicId, int $bndId = null): ?AllianceBoardTopic
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

        return $data !== false ? new AllianceBoardTopic($data) : null;
    }



    public function getTopicWithLatestPost(int $categoryId, int $bndId = null): ?AllianceBoardPost
    {
        $qb = $this->createQueryBuilder('q')
            ->select('p')
            ->innerJoin('App:AllianceboardPost', 'p', 'WITH', 'p.topic = q.id')
            ->orderBy('p.timestamp', 'DESC')
            ->setMaxResults(1);

        if ($bndId !== null) {
            $qb
                ->andWhere('q.bndId = :bndId')
                ->setParameter('bndId', $bndId);
        } else {
            $qb
                ->andWhere('q.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $qb
            ->getQuery()
            ->getOneOrNullResult();
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
