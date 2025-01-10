<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\DBAL\ArrayParameterType;
use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceRank;

class AllianceRankRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceRank::class);
    }

    public function add(int $allianceId): int
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_ranks')
            ->values([
                'rank_alliance_id' => ':allianceId',
            ])
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    public function addRankRight(int $rankId, int $rightId): void
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_rankrights')
            ->values([
                'rr_right_id' => ':rightId',
                'rr_rank_id' => ':rankId',
            ])
            ->setParameters([
                'rightId' => $rightId,
                'rankId' => $rankId,
            ])
            ->executeQuery();
    }

    /**
     * @return AllianceRank[]
     */
    public function getRanks(int $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select(
                'rank_id',
                'rank_level',
                'rank_name'
            )
            ->from('alliance_ranks')
            ->where('rank_alliance_id = :allianceId')
            ->orderBy('rank_level', 'DESC')
            ->setParameter('allianceId', $allianceId)
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceRank($row), $data);
    }

    public function getRank(int $rankId, int $allianceId): ?AllianceRank
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_ranks')
            ->where('rank_alliance_id = :allianceId')
            ->andWhere('rank_id = :rankId')
            ->setParameters([
                'allianceId' => $allianceId,
                'rankId' => $rankId,
            ])
            ->fetchAssociative();

        return $data !== false ? new AllianceRank($data) : null;
    }

    /**
     * @return int[]
     */
    public function getRightIds(int $rankId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('rr_right_id')
            ->from('alliance_rankrights')
            ->where('rr_rank_id = :rankId')
            ->setParameters([
                'rankId' => $rankId,
            ])
            ->fetchAllAssociative();

        return array_map(fn (array $row) => (int) $row['rr_right_id'], $data);
    }

    public function hasActionRights(int $allianceId, int $rankId, string $action): bool
    {
        return (bool) $this->createQueryBuilder('q')
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
            ->fetchOne();
    }

    /**
     * @return int[]
     */
    public function getAvailableRightIds(Alliance $alliance, AllianceRank $rank): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('rr.rightId')
            ->innerJoin('App:AllianceRankRight', 'rr', 'WITH', 'q.id = rr.rankId')
            ->where('q.alliance = :allianceId')
            ->andWhere('rr.rankId = :rankId')
            ->setParameters([
                'alliance' => $alliance,
                'rank' => $rank,
            ])
            ->getQuery()
            ->execute();

        return array_map(fn (array $row) => (int) $row['rightId'], $data);
    }

    public function updateRank(int $id, string $name, int $level): void
    {
        $this->createQueryBuilder('q')
            ->update('alliance_ranks')
            ->set('rank_name', ':name')
            ->set('rank_level', ':level')
            ->where('rank_id = :id')
            ->setParameters([
                'id' => $id,
                'name' => $name,
                'level' => $level,
            ])
            ->executeQuery();
    }

    public function removeRank(int $rankId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_ranks')
            ->where('rank_id = :rankId')
            ->setParameter('rankId', $rankId)
            ->executeQuery();

        $this->deleteRights($rankId);
    }

    public function deleteRights(int $rankId): void
    {
        $this->createQueryBuilder('q')
            ->delete('alliance_rankrights')
            ->where('rr_rank_id = :rankId')
            ->setParameter('rankId', $rankId)
            ->executeQuery();
    }

    public function deleteAllianceRanks(Alliance $alliance): void
    {
        $entries = $this->findBy(['alliance'=>$alliance]);

        foreach ($entries as $entry) {
            $this->remove($entry);
        }

        $this->save();
    }
}
