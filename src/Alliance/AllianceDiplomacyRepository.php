<?php declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceDiplomacy;

class AllianceDiplomacyRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceDiplomacy::class);
    }

    public function add(int $allianceId, int $otherAllianceId, int $level, string $text, string $name, int $diplomatId, int $points = 0, string $publicText = ''): int
    {
        $this->createQueryBuilder('q')
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
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search, null, $limit)
            ->orderBy('q.date', 'DESC')
            ->getQuery()
            ->execute();
    }

    /**
     * @return AllianceDiplomacy[]
     */
    public function getDiplomacies(Alliance $alliance, int $level = null): array
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.alliance1 = :alliance OR q.alliance2 = :alliance')
            ->orderBy('q.level', 'DESC')
            ->addOrderBy('q.id', 'DESC')
            ->setParameter('alliance', $alliance);

        if ($level !== null) {
            $qb
                ->andWhere('q.level = :level')
                ->setParameter('level', $level);
        }

        return $qb
            ->getQuery()
            ->execute();
    }

    public function getDiplomacy(int $id, int $allianceId, int $level = null): ?AllianceDiplomacy
    {
        $qb = $this->createQueryBuilder('q')
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

    public function existsDiplomacyBetween(Alliance $alliance, Alliance $otherAlliance, int $level = null): bool
    {
        if ($alliance === $otherAlliance) {
            return false;
        }

        $qb = $this->createQueryBuilder('q')
            ->select('1')
            ->where('(q.alliance1 = :alliance AND q.alliance2 = :otherAlliance) OR (q.alliance2 = :alliance AND q.alliance1 = :otherAlliance)')
            ->setParameters([
                'alliance' => $alliance,
                'otherAlliance' => $otherAlliance,
            ]);

        if ($level !== null) {
            $qb
                ->andWhere('q.level = :level')
                ->setParameter('level', $level);
        } else {
            $qb->andWhere('q.level >= 0');
        }

        return (bool) $qb
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function updateDiplomacy(AllianceDiplomacy $diplomacy, int $level, string $name, int $points = null, int $date = null): void
    {
        $diplomacy->setLevel($level);
        $diplomacy->setName($name);

        if ($points !== null) {
            $diplomacy->setPoints($points);
        }

        if ($date !== null) {
            $diplomacy->setDate($date);
        }

        $this->save();
    }

    public function acceptBnd(AllianceDiplomacy $diplomacy, int $points): void
    {
        $diplomacy->setLevel(AllianceDiplomacyLevel::BND_CONFIRMED);
        $diplomacy->setPoints($points);

        $this->save();
    }

    public function updatePublicText(int $id, int $allianceId, int $level, string $publicText): void
    {
        $this->createQueryBuilder('q')
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
        return (bool) $this->createQueryBuilder('q')
            ->where('q.alliance2 = :allianceId')
            ->andWhere('q.level = :war')
            ->andWhere('q.date > :since')
            ->setParameters([
                'allianceId' => $allianceId,
                'war' => AllianceDiplomacyLevel::WAR,
                'since' => $since,
            ])
            ->getQuery()
            ->execute();
    }

    public function isAtWar(Alliance $alliance, Alliance $atWarWithAlliance = null): bool
    {
        $qb = $this->createQueryBuilder('q')
            ->where('q.alliance1= :alliance OR q.alliance2 = :alliance')
            ->andWhere('q.level = :war')
            ->setParameters([
                'alliance' => $alliance,
                'war' => AllianceDiplomacyLevel::WAR,
            ]);

        if ($atWarWithAlliance) {
            $qb
                ->andWhere('q.alliance1 = :otherAlliance OR q.alliance2 = :otherAlliance')
                ->setParameter('otherAlliance', $atWarWithAlliance);
        }

        return (bool) $qb
            ->getQuery()
            ->execute();
    }

    public function hasPendingBndRequests(int $allianceId): bool
    {
        return (bool) $this->createQueryBuilder('q')
            ->where('q.alliance2 = :allianceId')
            ->andWhere('q.level = :level')
            ->setParameters([
                'allianceId' => $allianceId,
                'level' => AllianceDiplomacyLevel::BND_REQUEST,
            ])
            ->getQuery()
            ->execute();
    }

    public function deleteDiplomacy(AllianceDiplomacy $diplomacy): void
    {
        $this->remove($diplomacy);
        $this->save();
    }

    public function deleteAllianceDiplomacies(Alliance $alliance): void
    {
        $this->createQueryBuilder('q')
            ->delete()
            ->where('q.alliance1 = :alliance OR q.alliance2 = :alliance')
            ->setParameter('alliance', $alliance)
            ->getQuery()
            ->execute();
    }
}
