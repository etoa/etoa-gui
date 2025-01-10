<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceNews;
use EtoA\Entity\User;

class AllianceNewsRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceNews::class);
    }

    public function add(User $user, Alliance $alliance, string $title, string $text, ?Alliance $toAlliance = null): AllianceNews
    {
        $allianceNews = new AllianceNews();
        $allianceNews->setAlliance($alliance);
        $allianceNews->setAuthor($user);
        $allianceNews->setTitle($title);
        $allianceNews->setText($text);
        $allianceNews->setDate(time());
        $allianceNews->setToAlliance($toAlliance);

        $this->persist($allianceNews);
        $this->save();

        return $allianceNews;
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
    public function getNewsEntries(?Alliance $alliance = null, int $limit = null): array
    {
        $filter = [];

        if ($alliance) {
            $filter = ['alliance'=>$alliance];
        }

        return $this->findBy($filter,['date'=>'DESC'],$limit);
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

    public function countNewEntriesSince(Alliance $alliance, int $timestamp): int
    {
         return $this->createQueryBuilder('q')
            ->select('COUNT(q.id)')
            ->where('q.toAlliance= :alliance OR q.toAlliance is null')
            ->andWhere('q.date > :timestamp')
            ->setParameters([
                'timestamp' => $timestamp,
                'alliance' => $alliance,
            ])
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function deleteAllianceEntries(Alliance $alliance): void
    {
        $entries = $this->findBy(['alliance'=>$alliance]);

        foreach ($entries as $entry) {
            $this->remove($entry);
        }

        $this->save();
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
