<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\AbstractRepository;

class MissileFlightRepository extends AbstractRepository
{
    /**
     * @return MissileFlight[]
     */
    public function getFlights(MissileFlightSearch $search): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder('q'), $search)
            ->select('f.flight_landtime, f.flight_id, p.planet_name, p.id, f.flight_entity_from')
            ->from('missile_flights', 'f')
            ->innerJoin('f', 'planets', 'p', 'p.id = f.flight_entity_to')
            ->orderBy('flight_landtime', 'ASC')
            ->fetchAllAssociative();

        $objects = [];
        if (count($data) > 0) {
            $ids = array_map(fn (array $row) => $row['flight_id'], $data);
            $qb = $this->createQueryBuilder('q');
            $rows = $qb
                ->select('f.obj_flight_id, f.obj_missile_id, f.obj_cnt')
                ->from('missile_flights_obj', 'f')
                ->where($qb->expr()->in('obj_flight_id', $ids))
                ->fetchAllAssociative();

            foreach ($rows as $row) {
                $objects[(int) $row['obj_flight_id']][(int) $row['obj_missile_id']] = (int) $row['obj_cnt'];
            }
        }

        return array_map(fn (array $row) => new MissileFlight($row, $objects[(int) $row['flight_id']] ?? []), $data);
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

    public function deleteFlight(int $flightId, int $fromEntity): bool
    {
        $deleted = (bool) $this->createQueryBuilder('q')
            ->delete('missile_flights')
            ->where('flight_id = :flightId')
            ->andWhere('flight_entity_from = :fromEntity')
            ->setParameters([
                'flightId' => $flightId,
                'fromEntity' => $fromEntity,
            ])
            ->executeQuery()
            ->rowCount();

        if (!$deleted) {
            return false;
        }

        $this->createQueryBuilder('q')
            ->delete('missile_flights_obj')
            ->where('obj_flight_id = :flightId')
            ->setParameters([
                'flightId' => $flightId,
            ])
            ->executeQuery();

        return true;
    }
}
