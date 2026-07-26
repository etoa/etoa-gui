<?php

declare(strict_types=1);

namespace EtoA\Message;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\BattleReportData;
use EtoA\Entity\Entity;
use EtoA\Entity\MarketReportData;
use EtoA\Entity\OtherReportData;
use EtoA\Entity\Report;
use EtoA\Entity\SpyReportData;
use EtoA\Entity\User;

class ReportRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Report::class);
    }

    public function countNotArchived(): int
    {
        return $this->count(['archived'=>false]);
    }

    public function countDeleted(): int
    {
        return $this->count(['deleted'=>true]);
    }

    public function countUserUnread(User $user): int
    {
        return $this->count(['user'=>$user,'read'=>0,'deleted'=>0]);
    }

    /**
     * @return Report[]
     */
    public function searchReports(ReportSearch $search, int $limit, ?int $first = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->orderBy('q.timestamp', 'DESC');

        return $this->applySearchSortLimit($qb, $search, null, $limit, $first)
            ->getQuery()
            ->execute();
    }

    public function searchReport(ReportSearch $search): ?Report
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('*')
            ->from('reports')
            ->orderBy('timestamp', 'DESC')
            ->setMaxResults(1)
            ->fetchAssociative();

        return $data !== false ? Report::createFromArray($data) : null;
    }

    protected function addReport(string $type, User $user, ?Alliance $alliance, ?string $content, Entity $entity1, ?Entity $entity2 = null, ?User $opponent = null): Report
    {
        $report = new Report();
        $report->setTimestamp(time());
        $report->setUser($user);
        $report->setAlliance($alliance);
        $report->setType($type);
        $report->setContent($content);
        $report->setEntity1($entity1);
        $report->setEntity2($entity2);
        $report->setOpponent1($opponent);

        $this->persist($report);
        $this->save();

        return $report;
    }

    /**
     * @param int[] $ids
     */
    public function archive(int $userId, array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        $this->createQueryBuilder('q')
            ->update('reports')
            ->set('archived', '1')
            ->where('user_id = :userId')
            ->andWhere('id IN (:ids)')
            ->setParameter('userId', $userId)
            ->setParameter('ids', $ids, ArrayParameterType::INTEGER)
            ->executeQuery();
    }

    /**
     * @param int[] $ids
     */
    public function delete(int $userId, bool $archived, ?array $ids = null, ?string $type = null): void
    {
        if ($ids !== null && count($ids) === 0) {
            return;
        }

        $qb = $this->createQueryBuilder('q')
            ->update('reports')
            ->set('deleted', '1')
            ->where('user_id = :userId')
            ->andWhere('archived = :archived')
            ->setParameter('userId', $userId)
            ->setParameter('archived', $archived);

        if ($ids !== null) {
            $qb
                ->andWhere('id IN (:ids)')
                ->setParameter('ids', $ids, ArrayParameterType::INTEGER);
        }

        if ($type !== null) {
            $qb
                ->andWhere('type = :type')
                ->setParameter('type', $type);
        }

        $qb
            ->executeQuery();
    }

    public function setDeleted(int $id, bool $deleted): void
    {
        $this->createQueryBuilder('q')
            ->update('reports')
            ->set('deleted', ':deleted')
            ->where('id = :id')
            ->setParameters([
                'id' => $id,
                'deleted' => (int) $deleted,
            ])
            ->executeQuery();
    }

    /**
     * @param int[] $ids
     */
    public function markAsRead(int $userId, array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        $this->createQueryBuilder('q')
            ->update('reports')
            ->set('read', '1')
            ->where('user_id = :userId')
            ->andWhere('id IN (:ids)')
            ->setParameter('userId', $userId)
            ->setParameter('ids', $ids, ArrayParameterType::INTEGER)
            ->executeQuery();
    }

    public function removeUnarchivedread(int $beforeTimestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.archived = 0')
            ->andWhere('q.read = 1')
            ->andWhere('q.timestamp < :timestamp')
            ->setParameter('timestamp', $beforeTimestamp)
            ->getQuery()
            ->execute();
    }

    public function removeDeleted(int $beforeTimestamp): int
    {
        return $this->createQueryBuilder('q')
            ->delete()
            ->where('q.deleted = 1')
            ->andWhere('q.timestamp < :timestamp')
            ->setParameter('timestamp', $beforeTimestamp)
            ->getQuery()
            ->execute();
    }

    /**
     * @return ?array<string, mixed>
     */
    public function getOneBattleData(int $id): ?array
    {
        $data = $this->getConnection()->fetchAssociative('SELECT * FROM reports_battle WHERE id = :id', ['id' => $id]);

        return $data !== false ? $data : null;
    }

    /**
     * @param int[] $ids
     * @return BattleReportData[]
     */
    public function getBattleData(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_battle WHERE id IN (:ids)', ['ids' => $ids], ['ids' => ArrayParameterType::INTEGER]);

        $map = [];
        foreach ($rows as $row) {
            $data = BattleReportData::createFromArray($row);
            $map[$data->id] = $data;
        }

        return $map;
    }

    /**
     * @return ?array<string, mixed>
     */
    public function getOneMarketData(int $id): ?array
    {
        $data = $this->getConnection()->fetchAssociative('SELECT * FROM reports_market WHERE id = :id', ['id' => $id]);

        return $data !== false ? $data : null;
    }

    /**
     * @param int[] $ids
     * @return MarketReportData[]
     */
    public function getMarketData(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_market WHERE id IN (:ids)', ['ids' => $ids], ['ids' => ArrayParameterType::INTEGER]);

        $map = [];
        foreach ($rows as $row) {
            $data = MarketReportData::createFromArray($row);
            $map[$data->id] = $data;
        }

        return $map;
    }

    /**
     * @return ?array<string, mixed>
     */
    public function getOneOtherData(int $id): ?array
    {
        $data = $this->getConnection()->fetchAssociative('SELECT * FROM reports_other WHERE id = :id', ['id' => $id]);

        return $data !== false ? $data : null;
    }

    /**
     * @param int[] $ids
     * @return OtherReportData[]
     */
    public function getOtherData(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_other WHERE id IN (:ids)', ['ids' => $ids], ['ids' => ArrayParameterType::INTEGER]);

        $map = [];
        foreach ($rows as $row) {
            $data = OtherReportData::createFromArray($row);
            $map[$data->id] = $data;
        }

        return $map;
    }

    /**
     * @return ?array<string, mixed>
     */
    public function getOneSpyData(int $id): ?array
    {
        $data = $this->getConnection()->fetchAssociative('SELECT * FROM reports_spy WHERE id = :id', ['id' => $id]);

        return $data !== false ? $data : null;
    }

    /**
     * @param int[] $ids
     * @return SpyReportData[]
     */
    public function getSpyData(array $ids): array
    {
        if (count($ids) === 0) {
            return [];
        }

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_spy WHERE id IN (:ids)', ['ids' => $ids], ['ids' => ArrayParameterType::INTEGER]);

        $map = [];
        foreach ($rows as $row) {
            $data = SpyReportData::createFromArray($row);
            $map[$data->id] = $data;
        }

        return $map;
    }

    public function removeForUser(User $user) : void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.id = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();

        $this->createQueryBuilder('q')
            ->update()
            ->set('q.opponent1',null)
            ->where('q.opponent1 = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->execute();
    }

    public function countBySearch(?ReportSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('COUNT(q)')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
