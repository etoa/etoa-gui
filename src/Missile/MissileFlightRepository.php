<?php declare(strict_types=1);

namespace EtoA\Missile;

use Doctrine\Persistence\ManagerRegistry;
use EtoA\Core\AbstractRepository;
use EtoA\Entity\MissileFlight;

class MissileFlightRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MissileFlight::class);
    }

    /**
     * @return MissileFlight[]
     */
    public function getFlights(MissileFlightSearch $search): array
    {
        return $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->orderBy('q.landTime', 'ASC')
            ->getQuery()
            ->execute();
    }

    /**
     * @param array<int, int> $missiles
     */
    public function startFlight(int $fromEntity, int $toEntity, int $duration, array $missiles): int
    {
        $this->createQueryBuilder('q')
            ->insert('missile_flights')
            ->values([
                'flight_entity_from' => ':fromEntity',
                'flight_entity_to' => ':toEntity',
                'flight_starttime' => 'UNIX_TIMESTAMP()',
                'flight_landtime' => 'UNIX_TIMESTAMP() + :duration',
            ])
            ->setParameters([
                'fromEntity' => $fromEntity,
                'toEntity' => $toEntity,
                'duration' => $duration,
            ])->executeQuery();

        $flightId = (int) $this->getConnection()->lastInsertId();
        foreach ($missiles as $missileId => $count) {
            $this->createQueryBuilder('q')
                ->insert('missile_flights_obj')
                ->values([
                    'obj_flight_id' => ':flightId',
                    'obj_missile_id' => ':missileId',
                    'obj_cnt' => ':count',
                ])
                ->setParameters([
                    'flightId' => $flightId,
                    'missileId' => $missileId,
                    'count' => $count,
                ])->executeQuery();
        }

        return $flightId;
    }

    public function deleteFlight(MissileFlight $missileFlight): void
    {
        $this->remove($missileFlight);
        $this->save();
    }
}
