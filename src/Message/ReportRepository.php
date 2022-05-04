<?php

declare(strict_types=1);

namespace EtoA\Message;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;
use EtoA\Message\ReportData\BattleReportData;
use EtoA\Message\ReportData\MarketReportData;
use EtoA\Message\ReportData\OtherReportData;
use EtoA\Message\ReportData\SpyReportData;

class ReportRepository extends AbstractRepository
{
    public function count(ReportSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(*)')
            ->from('reports')
            ->fetchOne();
    }

    public function countNotArchived(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->where('archived = 0')
            ->fetchOne();
    }

    public function countDeleted(): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(*)')
            ->from('reports')
            ->where('deleted = 1')
            ->fetchOne();
    }

    public function countUserUnread(int $userId): int
    {
        return (int) $this->createQueryBuilder()
            ->select('COUNT(id)')
            ->from('reports')
            ->where('user_id = :userId')
            ->andWhere('`read` = 0')
            ->andWhere('`deleted` = 0')
            ->setParameter('userId', $userId)
            ->fetchOne();
    }

    /**
     * @return Report[]
     */
    public function searchReports(ReportSearch $search, int $limit, int $first = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('*')
            ->from('reports')
            ->orderBy('timestamp', 'DESC');

        $data = $this->applySearchSortLimit($qb, $search, null, $limit, $first)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => Report::createFromArray($row), $data);
    }

    public function searchReport(ReportSearch $search): ?Report
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('*')
            ->from('reports')
            ->orderBy('timestamp', 'DESC')
            ->setMaxResults(1)
            ->fetchAssociative();

        return $data !== false ? Report::createFromArray($data) : null;
    }

    protected function addReport(string $type, int $userId, int $allianceId, ?string $content, int $entity1Id, int $entity2Id, int $opponentId): int
    {
        $this->createQueryBuilder()
            ->insert('reports')
            ->values([
                'timestamp' => ':time',
                'type' => ':type',
                'user_id' => ':userId',
                'alliance_id' => ':allianceId',
                'content' => ':content',
                'entity1_id' => ':entity1Id',
                'entity2_id' => ':entity2Id',
                'opponent1_id' => ':opponentId',
            ])
            ->setParameters([
                ':time' => time(),
                'type' => $type,
                'userId' => $userId,
                'allianceId' => $allianceId,
                'content' => $content,
                'entity1Id' => $entity1Id,
                'entity2Id' => $entity2Id,
                'opponentId' => $opponentId,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @param int[] $ids
     */
    public function archive(int $userId, array $ids): void
    {
        if (count($ids) === 0) {
            return;
        }

        $this->createQueryBuilder()
            ->update('reports')
            ->set('archived', '1')
            ->where('user_id = :userId')
            ->andWhere('id IN (:ids)')
            ->setParameter('userId', $userId)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->executeQuery();
    }

    /**
     * @param int[] $ids
     */
    public function delete(int $userId, bool $archived, array $ids = null, string $type = null): void
    {
        if ($ids !== null && count($ids) === 0) {
            return;
        }

        $qb = $this->createQueryBuilder()
            ->update('reports')
            ->set('deleted', '1')
            ->where('user_id = :userId')
            ->andWhere('archived = :archived')
            ->setParameter('userId', $userId)
            ->setParameter('archived', $archived);

        if ($ids !== null) {
            $qb
                ->andWhere('id IN (:ids)')
                ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY);
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
        $this->createQueryBuilder()
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

        $this->createQueryBuilder()
            ->update('reports')
            ->set('read', '1')
            ->where('user_id = :userId')
            ->andWhere('id IN (:ids)')
            ->setParameter('userId', $userId)
            ->setParameter('ids', $ids, Connection::PARAM_INT_ARRAY)
            ->executeQuery();
    }

    public function removeUnarchivedread(int $beforeTimestamp): int
    {
        return $this->createQueryBuilder()
            ->delete('reports')
            ->where('archived = 0')
            ->andWhere('`read` = 1')
            ->andWhere('timestamp < :timestamp')
            ->setParameter('timestamp', $beforeTimestamp)
            ->executeQuery()
            ->rowCount();
    }

    public function removeDeleted(int $beforeTimestamp): int
    {
        return $this->createQueryBuilder()
            ->delete('reports')
            ->where('deleted = 1')
            ->andWhere('timestamp < :timestamp')
            ->setParameter('timestamp', $beforeTimestamp)
            ->executeQuery()
            ->rowCount();
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

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_battle WHERE id IN (:ids)', ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

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

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_market WHERE id IN (:ids)', ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

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

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_other WHERE id IN (:ids)', ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

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

        $rows = $this->getConnection()->fetchAllAssociative('SELECT * FROM reports_spy WHERE id IN (:ids)', ['ids' => $ids], ['ids' => Connection::PARAM_INT_ARRAY]);

        $map = [];
        foreach ($rows as $row) {
            $data = SpyReportData::createFromArray($row);
            $map[$data->id] = $data;
        }

        return $map;
    }
}
