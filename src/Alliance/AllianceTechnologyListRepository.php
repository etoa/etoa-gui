<?php

declare(strict_types=1);

namespace EtoA\Alliance;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\Alliance;
use EtoA\Entity\AllianceTechnology;
use EtoA\Entity\AllianceTechnologyListItem;

class AllianceTechnologyListRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AllianceTechnologyListItem::class);
    }

    public function existsInAlliance(int|Alliance $allianceId, int|AllianceTechnology $technologyId): bool
    {
        return $this->count(['alliance'=>$allianceId,'technology'=>$technologyId]) > 0;
    }

    public function getLevel(Alliance $alliance, AllianceTechnology|int $technology): ?int
    {

        return $this->findOneBy(['alliance'=>$alliance,'technology'=>$technology])?->getlevel();
    }

    /**
     * @return array<int, int>
     */
    public function getLevels(int|Alliance $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('IDENTITY(q.technology) as id, q.level')
            ->where('q.alliance = :alliance')
            ->andWhere('q.level > 0')
            ->setParameters([
                'alliance' => $allianceId,
            ])
            ->getQuery()
            ->execute();

        return array_column($data, 'level', 'id');
    }

    public function addToAlliance(int $allianceId, int $technologyId, int $level, int $amount, int $startTime = 0, int $endTime = 0): void
    {
        $this->createQueryBuilder('q')
            ->insert('alliance_techlist')
            ->values([
                'alliance_techlist_alliance_id' => ':alliance',
                'alliance_techlist_tech_id' => ':technologyId',
                'alliance_techlist_current_level' => ':level',
                'alliance_techlist_build_start_time' => ':startTime',
                'alliance_techlist_build_end_time' => ':endTime',
                'alliance_techlist_member_for' => ' :amount',
            ])
            ->setParameters([
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
                'level' => $level,
                'amount' => $amount,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ])
            ->executeQuery();
    }

    public function updateMembersForAlliance(Alliance $alliance, int $amount): void
    {
        $this->createQueryBuilder('q')
            ->set('q.memberFor', ':amount')
            ->where('q.alliance = :alliance')
            ->andWhere('q.memberFor < :amount')
            ->setParameters([
                'amount' => $amount,
                'alliance' => $alliance,
            ])
            ->getQuery()
            ->execute();
    }

    public function updateForAlliance(int|Alliance $allianceId, int|AllianceTechnology $technologyId, int $level, int $amount, int $startTime = 0, int $endTime = 0): void
    {
        $this->createQueryBuilder('q')
            ->update()
            ->set('q.level', ':level')
            ->set('q.memberFor', ':amount')
            ->set('q.startTime', ':startTime')
            ->set('q.endTime', ':endTime')
            ->where('q.alliance = :alliance')
            ->andWhere('q.technology = :technologyId')
            ->setParameters([
                'level' => $level,
                'amount' => $amount,
                'alliance' => $allianceId,
                'technologyId' => $technologyId,
                'startTime' => $startTime,
                'endTime' => $endTime,
            ])
            ->getQuery()
            ->execute();
    }

    public function removeForAlliance(Alliance $alliance): void
    {
        $entries = $this->findBy(['alliance'=>$alliance]);

        foreach ($entries as $entry) {
            $this->remove($entry);
        }

        $this->save();
    }


    /**
     * @return AllianceTechnologyListItem[]
     */
    public function getTechnologyList(int $allianceId): array
    {
        $data = $this->createQueryBuilder('q')
            ->select('*')
            ->from('alliance_techlist')
            ->where('alliance_techlist_alliance_id = :allianceId')
            ->setParameter('allianceId', $allianceId)
            ->fetchAllAssociative();

        $result = [];
        foreach ($data as $row) {
            $entry = AllianceTechnologyListItem::createFromData($row);
            $result[$entry->technologyId] = $entry;
        }

        return $result;
    }

    /**
     * @return ?array{name: string, endTime: int}
     */
    public function getInProgress(int $allianceId): ?array
    {
        $data = $this->createQueryBuilder('q')
            ->where('q.alliance = :allianceId')
            ->andWhere('q.buildEndTime > 0')
            ->setParameter('allianceId', $allianceId)
            ->getQuery()
            ->execute();

        return $data ? ['name' => $data['alliance_tech_name'], 'endTime' => (int) $data['alliance_techlist_build_end_time']] : null;
    }
}
