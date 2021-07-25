<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\DBAL\Connection;
use EtoA\Core\AbstractRepository;

class AllianceRankRepository extends AbstractRepository
{
    public function add(int $allianceId): int
    {
        $this->createQueryBuilder()
            ->insert('alliance_ranks')
            ->values([
                'rank_alliance_id' => ':allianceId',
            ])
            ->setParameter('allianceId', $allianceId)
            ->execute();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function addRankRight(int $rankId, int $rightId): void
    {
        $this->createQueryBuilder()
            ->insert('alliance_rankrights')
            ->values([
                'rr_right_id' => ':rightId',
                'rr_rank_id' => ':rankId',
            ])
            ->setParameters([
                'rightId' => $rightId,
                'rankId' => $rankId,
            ])
            ->execute();
    }

    /**
     * @return AllianceRank[]
     */
    public function getRanks(int $allianceId): array
    {
        $data = $this->createQueryBuilder()
            ->select(
                'rank_id',
                'rank_level',
                'rank_name'
            )
            ->from('alliance_ranks')
            ->where('rank_alliance_id = :allianceId')
            ->orderBy('rank_level', 'DESC')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceRank($row), $data);
    }

    public function getRank(int $rankId, int $allianceId): ?AllianceRank
    {
        $data = $this->createQueryBuilder()
            ->select('*')
            ->from('alliance_ranks')
            ->where('rank_alliance_id = :allianceId')
            ->andWhere('rank_id = :rankId')
            ->setParameters([
                'allianceId' => $allianceId,
                'rankId' => $rankId,
            ])
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new AllianceRank($data) : null;
    }

    /**
     * @return int[]
     */
    public function getRightIds(int $rankId): array
    {
        $data = $this->createQueryBuilder()
            ->select('rr_right_id')
            ->from('alliance_rankrights')
            ->where('rr_rank_id = :rankId')
            ->setParameters([
                'rankId' => $rankId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['rr_right_id'], $data);
    }

    public function hasActionRights(int $allianceId, int $rankId, string $action): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('alliance_ranks', 'ra')
            ->innerJoin('ra', 'alliance_rankrights', 'rr', 'ra.rank_id = rr.rr_rank_id')
            ->innerJoin('rr', 'alliance_rights', 'ri', 'rr.rr_right_id = ri.right_id')
            ->where('ra.rank_alliance_id = :allianceId')
            ->andWhere('ri.right_key = :action')
            ->andWhere('rr.rr_rank_id = :rankId')
            ->setParameters([
                'allianceId' => $allianceId,
                'rankId' => $rankId,
                'action' => $action,
            ])
            ->execute()
            ->fetchOne();
    }

    /**
     * @return int[]
     */
    public function getAvailableRightIds(int $allianceId, int $rankId): array
    {
        $data = $this->createQueryBuilder()
            ->select('rr.rr_right_id')
            ->from('alliance_ranks', 'ra')
            ->innerJoin('ra', 'alliance_rankrights', 'rr', 'ra.rank_id = rr.rr_rank_id')
            ->where('ra.rank_alliance_id = :allianceId')
            ->andWhere('rr.rr_rank_id = :rankId')
            ->setParameters([
                'allianceId' => $allianceId,
                'rankId' => $rankId,
            ])
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['rr_right_id'], $data);
    }

    public function updateRank(int $id, string $name, int $level): void
    {
        $this->createQueryBuilder()
            ->update('alliance_ranks')
            ->set('rank_name', ':name')
            ->set('rank_level', ':level')
            ->where('rank_id = :id')
            ->setParameters([
                'id' => $id,
                'name' => $name,
                'level' => $level,
            ])
            ->execute();
    }

    public function removeRank(int $rankId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_ranks')
            ->where('rank_id = :rankId')
            ->setParameter('rankId', $rankId)
            ->execute();

        $this->deleteRights($rankId);
    }

    public function deleteRights(int $rankId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_rankrights')
            ->where('rr_rank_id = :rankId')
            ->setParameter('rankId', $rankId)
            ->execute();
    }

    public function deleteAllianceRanks(int $allianceId): void
    {
        $rankIds = array_column($this->createQueryBuilder()
            ->select('rank_id')
            ->from('alliance_ranks')
            ->where('rank_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute()
            ->fetchAllAssociative(), 'rank_id');

        $this->createQueryBuilder()
            ->delete('alliance_rankrights')
            ->where('rr_rank_id IN (:rankIds)')
            ->setParameter('rankIds', $rankIds, Connection::PARAM_INT_ARRAY)
            ->execute();

        $this->createQueryBuilder()
            ->delete('alliance_ranks')
            ->where('rank_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->execute();
    }

    public function countOrphanedRanks(): int
    {
        return (int) $this->getConnection()
            ->executeQuery(
                "SELECT
                    COUNT(r.rank_id)
                FROM alliance_ranks r
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE r.rank_alliance_id = a.alliance_id
                );"
            )
            ->fetchOne();
    }

    public function deleteOrphanedRanks(): int
    {
        return $this->getConnection()
            ->executeStatement(
                "DELETE FROM alliance_ranks
                WHERE NOT EXISTS (
                    SELECT 1
                    FROM alliances a
                    WHERE rank_alliance_id = a.alliance_id
                );"
            );
    }
}
