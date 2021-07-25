<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceNewsRepository extends AbstractRepository
{
    public function add(int $userId, int $allianceId, string $title, string $text, int $toAllianceId): int
    {
        $this->createQueryBuilder()
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
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function update(int $id, int $userId, int $allianceId, string $title, string $text, int $toAllianceId): void
    {
        $this->createQueryBuilder()
            ->update('alliance_news')
            ->set('alliance_news_alliance_id', ':allianceId')
            ->set('alliance_news_user_id', ':userId')
            ->set('alliance_news_title', ':title')
            ->set('alliance_news_text', ':text')
            ->set('alliance_news_alliance_to_id', ':toAllianceId')
            ->where('alliance_news_id = :id')
            ->setParameters([
                'allianceId' => $allianceId,
                'userId' => $userId,
                'title' => $title,
                'text' => $text,
                'id' => $id,
                'toAllianceId' => $toAllianceId,
            ])
            ->execute();
    }

    /**
     * @return AllianceNews[]
     */
    public function getNewsEntries(?int $allianceId, int $limit = null): array
    {
        $qb = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceNews($row), $data);
    }

    /**
     * @return int[]
     */
    public function getNewsIds(): array
    {
        $data = $this->createQueryBuilder()
            ->select('alliance_news_id')
            ->from('alliance_news')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['alliance_news_id'], $data);
    }

    public function getEntry(int $id): ?AllianceNews
    {
        $data = $this->createQueryBuilder()
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
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new AllianceNews($data) : null;
    }

    public function countNewEntriesSince(int $allianceId, int $timestamp): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(alliance_news_id)')
            ->from('alliance_news')
            ->where('alliance_news_alliance_to_id = :allianceId OR alliance_news_alliance_to_id = 0')
            ->andWhere('alliance_news_date > :timestamp')
            ->setParameters([
                'timestamp' => $timestamp,
                'allianceId' => $allianceId,
            ])
            ->execute()
            ->fetchOne();
    }

    public function deleteAllianceEntries(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_news')
            ->where('alliance_news_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }

    public function deleteOlderThan(int $timestamp): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_news')
            ->where('alliance_news_date < :timestamp')
            ->setParameter('timestamp', $timestamp)
            ->execute();
    }

    public function deleteEntry(int $newsId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_news')
            ->where('alliance_news_id = :id')
            ->setParameter('id', $newsId)
            ->execute();
    }
}
