<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AllianceDiplomacyRepository extends AbstractRepository
{
    public function add(int $allianceId, int $otherAllianceId, int $level, string $text, string $name, int $diplomatId, int $points = 0, string $publicText = ''): int
    {
        $this->createQueryBuilder()
            ->insert('alliance_bnd')
            ->values([
                'alliance_bnd_alliance_id1' => ':allianceId',
                'alliance_bnd_alliance_id2' => ':otherAllianceId',
                'alliance_bnd_level' => ':level',
                'alliance_bnd_text' => ':text',
                'alliance_bnd_name' => ':name',
                'alliance_bnd_date' => ':now',
                'alliance_bnd_diplomat_id' => ':diplomatId',
                'alliance_bnd_points' => ':points',
                'alliance_bnd_text_pub' => ':publicText',
            ])
            ->setParameters([
                'allianceId' => $allianceId,
                'otherAllianceId' => $otherAllianceId,
                'level' => $level,
                'text' => $text,
                'name' => $name,
                'now' => time(),
                'diplomatId' => $diplomatId,
                'points' => $points,
                'publicText' => $publicText,
            ])
            ->executeQuery();

        return (int) $this->getConnection()->lastInsertId();
    }

    /**
     * @return AllianceDiplomacy[]
     */
    public function search(AllianceDiplomacySearch $search, int $limit = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit)
            ->select('b.*')
            ->addSelect('a1.alliance_name as alliance1Name, a1.alliance_tag as alliance1Tag')
            ->addSelect('a2.alliance_name as alliance2Name, a2.alliance_tag as alliance2Tag')
            ->from('alliance_bnd', 'b')
            ->leftJoin('b', 'alliances', 'a1', 'alliance_bnd_alliance_id1 = a1.alliance_id')
            ->leftJoin('b', 'alliances', 'a2', 'alliance_bnd_alliance_id2 = a2.alliance_id')
            ->orderBy('b.alliance_bnd_date', 'DESC')
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceDiplomacy($row, 0), $data);
    }

    /**
     * @return AllianceDiplomacy[]
     */
    public function getDiplomacies(int $allianceId, int $level = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select('b.*')
            ->addSelect('a1.alliance_name as alliance1Name, a1.alliance_tag as alliance1Tag')
            ->addSelect('a2.alliance_name as alliance2Name, a2.alliance_tag as alliance2Tag')
            ->from('alliance_bnd', 'b')
            ->leftJoin('b', 'alliances', 'a1', 'alliance_bnd_alliance_id1 = a1.alliance_id')
            ->leftJoin('b', 'alliances', 'a2', 'alliance_bnd_alliance_id2 = a2.alliance_id')
            ->where('b.alliance_bnd_alliance_id1 = :allianceId OR b.alliance_bnd_alliance_id2 = :allianceId')
            ->orderBy('b.alliance_bnd_level', 'DESC')
            ->addOrderBy('b.alliance_bnd_id', 'DESC')
            ->setParameter('allianceId', $allianceId);

        if ($level !== null) {
            $qb
                ->andWhere('b.alliance_bnd_level = :level')
                ->setParameter('level', $level);
        }

        $data = $qb
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AllianceDiplomacy($row, $allianceId), $data);
    }

    public function getDiplomacy(int $id, int $allianceId, int $level = null): ?AllianceDiplomacy
    {
        $qb = $this->createQueryBuilder()
            ->select('b.*')
            ->addSelect('a1.alliance_name as alliance1Name, a1.alliance_tag as alliance1Tag')
            ->addSelect('a2.alliance_name as alliance2Name, a2.alliance_tag as alliance2Tag')
            ->from('alliance_bnd', 'b')
            ->leftJoin('b', 'alliances', 'a1', 'alliance_bnd_alliance_id1 = a1.alliance_id')
            ->leftJoin('b', 'alliances', 'a2', 'alliance_bnd_alliance_id2 = a2.alliance_id')
            ->where('b.alliance_bnd_alliance_id1 = :allianceId OR b.alliance_bnd_alliance_id2 = :allianceId')
            ->andWhere('b.alliance_bnd_id = :bndId')
            ->setParameters([
                'allianceId' => $allianceId,
                'bndId' => $id,
            ]);

        if ($level !== null) {
            $qb
                ->andWhere('b.alliance_bnd_level = :level')
                ->setParameter('level', $level);
        }

        $data = $qb
            ->fetchAssociative();

        return $data !== false ? new AllianceDiplomacy($data, $id) : null;
    }

    public function existsDiplomacyBetween(int $allianceId, int $otherAllianceId, int $level = null): bool
    {
        if ($allianceId === 0 || $otherAllianceId === 0 || $allianceId === $otherAllianceId) {
            return false;
        }

        $qb = $this->createQueryBuilder()
            ->select('1')
            ->from('alliance_bnd')
            ->where('(alliance_bnd_alliance_id1 = :allianceId AND alliance_bnd_alliance_id2 = :otherAllianceId) OR (alliance_bnd_alliance_id2 = :allianceId AND alliance_bnd_alliance_id1 = :otherAllianceId)')
            ->setParameters([
                'allianceId' => $allianceId,
                'otherAllianceId' => $otherAllianceId,
            ]);

        if ($level !== null) {
            $qb
                ->andWhere('alliance_bnd_level = :level')
                ->setParameter('level', $level);
        } else {
            $qb->andWhere('alliance_bnd_level > 0');
        }

        return (bool) $qb
            ->fetchOne();
    }

    public function updateDiplomacy(int $id, int $level, string $name, int $points = null, int $date = null): void
    {
        $qb = $this->createQueryBuilder()
            ->update('alliance_bnd')
            ->set('alliance_bnd_level', ':level')
            ->set('alliance_bnd_name', ':name')
            ->where('alliance_bnd_id = :id')
            ->setParameters([
                'id' => $id,
                'level' => $level,
                'name' => $name,
            ]);

        if ($points !== null) {
            $qb
                ->set('alliance_bnd_points', ':points')
                ->setParameter('points', $points);
        }

        if ($date !== null) {
            $qb
                ->set('alliance_bnd_date', ':date')
                ->setParameter('date', $date);
        }

        $qb
            ->executeQuery();
    }

    public function acceptBnd(int $id, int $points): void
    {
        $this->createQueryBuilder()
            ->update('alliance_bnd')
            ->set('alliance_bnd_level', ':level')
            ->set('alliance_bnd_points', ':points')
            ->where('alliance_bnd_id = :id')
            ->setParameters([
                'id' => $id,
                'points' => $points,
                'level' => AllianceDiplomacyLevel::BND_CONFIRMED,
            ])
            ->executeQuery();
    }

    public function updatePublicText(int $id, int $allianceId, int $level, string $publicText): void
    {
        $this->createQueryBuilder()
            ->update('alliance_bnd')
            ->set('alliance_bnd_text_pub', ':publicText')
            ->where('alliance_bnd_id = :id')
            ->andWhere('alliance_bnd_level = :level')
            ->andWhere('alliance_bnd_alliance_id1 = :allianceId OR alliance_bnd_alliance_id2 = :allianceId')
            ->setParameters([
                'id' => $id,
                'allianceId' => $allianceId,
                'level' => $level,
                'publicText' => $publicText,
            ])
            ->executeQuery();
    }

    public function wasWarDeclaredAgainstSince(int $allianceId, int $since): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('alliance_bnd')
            ->where('alliance_bnd_alliance_id2 = :allianceId')
            ->andWhere('alliance_bnd_level = :war')
            ->andWhere('alliance_bnd_date > :since')
            ->setParameters([
                'allianceId' => $allianceId,
                'war' => AllianceDiplomacyLevel::WAR,
                'since' => $since,
            ])
            ->fetchOne();
    }

    public function isAtWar(int $allianceId, int $atWarWithAllianceId = null): bool
    {
        $qb = $this->createQueryBuilder()
            ->select('1')
            ->from('alliance_bnd')
            ->where('alliance_bnd_alliance_id1 = :allianceId OR alliance_bnd_alliance_id2 = :allianceId')
            ->andWhere('alliance_bnd_level = :war')
            ->setParameters([
                'allianceId' => $allianceId,
                'war' => AllianceDiplomacyLevel::WAR,
            ]);

        if ($atWarWithAllianceId !== null) {
            $qb
                ->andWhere('alliance_bnd_alliance_id1 = :otherAllianceId OR alliance_bnd_alliance_id2 = :otherAllianceId')
                ->setParameter('otherAllianceId', $atWarWithAllianceId);
        }

        return (bool) $qb
            ->fetchOne();
    }

    public function hasPendingBndRequests(int $allianceId): bool
    {
        return (bool) $this->createQueryBuilder()
            ->select('1')
            ->from('alliance_bnd')
            ->where('alliance_bnd_alliance_id2 = :allianceId')
            ->andWhere('alliance_bnd_level = :level')
            ->setParameters([
                'allianceId' => $allianceId,
                'level' => AllianceDiplomacyLevel::BND_REQUEST,
            ])
            ->fetchOne();
    }

    public function deleteDiplomacy(int $id): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_bnd')
            ->where('alliance_bnd_id = :id')
            ->setParameter('id', $id)
            ->executeQuery();
    }

    public function deleteAllianceDiplomacies(int $allianceId): void
    {
        $this->createQueryBuilder()
            ->delete('alliance_bnd')
            ->where('alliance_bnd_alliance_id1 = :allianceId OR alliance_bnd_alliance_id2 = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->executeQuery();
    }
}
