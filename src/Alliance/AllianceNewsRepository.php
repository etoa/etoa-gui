<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\AllianceNews;

class AllianceNewsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceNews::class);
    }

    public function add(int $userId, int $allianceId, string $title, string $text, int $toAllianceId): int
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_news')
            ->values([
                'alliance_news_alliance_id' => ':allianceId',
                'alliance_news_user_id' => ':userId',
                'alliance_news_title' => ':title',
                'alliance_news_text' => ':text',
                'alliance_news_date' => ':now',
                'alliance_news_alliance_to_id' => ':toAllianceId',
            ])
            ->setParameters([
                'allianceId' => $allianceId,
                'userId' => $userId,
                'title' => $title,
                'text' => $text,
                'now' => time(),
                'toAllianceId' => $toAllianceId,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(AllianceNews $news): void
    {
        $this->createQueryBuilder('q')
            ->update('alliance_news')
            ->set('alliance_news_alliance_id', ':allianceId')
            ->set('alliance_news_user_id', ':userId')
            ->set('alliance_news_title', ':title')
            ->set('alliance_news_text', ':text')
            ->set('alliance_news_alliance_to_id', ':toAllianceId')
            ->where('alliance_news_id = :id')
            ->setParameters([
                'allianceId' => $news->authorAllianceId,
                'userId' => $news->authorUserId,
                'title' => $news->title,
                'text' => $news->text,
                'id' => $news->id,
                'toAllianceId' => (int) $news->toAllianceId,
            ])
            ->executeQuery();
    }

    /**
     * @return AllianceNews[]
     */
    public function getNewsEntries(?int $allianceId, int $limit = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->select('n.*')
            ->addSelect('a.alliance_name, a.alliance_tag')
            ->addSelect('u.user_id, u.user_nick')
            ->addSelect('ta.alliance_name AS to_alliance_name, ta.alliance_tag AS to_alliance_tag')
            ->from('alliance_news', 'n')
            ->leftJoin('n', 'alliances', 'a', 'n.alliance_news_alliance_id=a.alliance_id')
            ->leftJoin('n', 'users', 'u', 'u.user_id = n.alliance_news_user_id')
            ->leftJoin('n', 'alliances', 'ta', 'n.alliance_news_alliance_id=ta.alliance_id')
            ->orderBy('n.alliance_news_date', 'DESC');

        if ($allianceId !== null) {
            $qb
                ->where('n.alliance_news_alliance_to_id = :allianceId')
                ->setParameter('allianceId', $allianceId);
        }

        if ($limit > 0) {
            $qb->setMaxResults($limit);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceNews($row), $data);
    }

    /**
     * @return int[]
     */
    public function getNewsIds(): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('alliance_news_id')
            ->from('alliance_news')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['alliance_news_id'], $data);
    }

    public function getEntry(int $id): ?AllianceNews
    {
        $data = $this->createQueryBuilder('q')
            ->select('n.*')
            ->addSelect('a.alliance_name, a.alliance_tag')
            ->addSelect('u.user_id, u.user_nick')
            ->addSelect('ta.alliance_name AS to_alliance_name, ta.alliance_tag AS to_alliance_tag')
            ->from('alliance_news', 'n')
            ->leftJoin('n', 'alliances', 'a', 'n.alliance_news_alliance_id=a.alliance_id')
            ->leftJoin('n', 'users', 'u', 'u.user_id = n.alliance_news_user_id')
            ->leftJoin('n', 'alliances', 'ta', 'n.alliance_news_alliance_id=ta.alliance_id')
            ->where('alliance_news_id = :id')
            ->setParameter('id', $id)
            ->fetchAssociative();

        return $data !== false ? new AllianceNews($data) : null;
    }

    public function countNewEntriesSince(int $allianceId, int $timestamp): int
    {
         return $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->where('q.toAllianceId = :allianceId OR q.toAllianceId = 0')
            ->andWhere('q.date > :timestamp')
            ->setParameters([
                'timestamp' => $timestamp,
                'allianceId' => $allianceId,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteAllianceEntries(int $allianceId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_news')
            ->where('alliance_news_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();
    }

    public function deleteOlderThan(int $timestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete('alliance_news')
            ->where('alliance_news_date < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->executeQuery()
            ->rowCount();
    }

    public function deleteEntry(int $newsId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_news')
            ->where('alliance_news_id = :id')
            ->setParameter('id', $newsId)
            ->executeQuery();
    }
}
