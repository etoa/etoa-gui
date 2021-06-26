<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\AbstractRepository;

class AlliancePointsRepository extends AbstractRepository
{
    /**
     * @return AlliancePoints[]
     */
    public function getPoints(int $allianceId, int $limit, int $start = null, int $end = null): array
    {
        $qb = $this->createQueryBuilder()
            ->select("*")
            ->from('alliance_points')
            ->where('point_alliance_id = :allianceId')
            ->andWhere('point_points > 0')
            ->setParameter('allianceId', $allianceId)
            ->orderBy('point_timestamp', 'DESC')
            ->setMaxResults($limit);

        if ($start > 0) {
            $qb
                ->andWhere('point_timestamp > :start')
                ->setParameter('start', $start);
        }

        if ($end > 0) {
            $qb
                ->andWhere('point_timestamp < :end')
                ->setParameter('end', $end);
        }

        $data = $qb
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new AlliancePoints($row), $data);
    }
}
