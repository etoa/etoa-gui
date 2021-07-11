<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\AbstractRepository;

class MissileFlightRepository extends AbstractRepository
{
    /**
     * @param array<int, int> $missiles
     */
    public function startFlight(int $fromEntity, int $toEntity, int $duration, array $missiles): void
    {
        $this->createQueryBuilder()
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
            ])->execute();

        $flightId = (int) $this->getConnection()->lastInsertId();
        foreach ($missiles as $missileId => $count) {
            $this->createQueryBuilder()
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
                ])->execute();
        }
    }
}
