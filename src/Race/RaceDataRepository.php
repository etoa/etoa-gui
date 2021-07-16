<?php declare(strict_types=1);

namespace EtoA\Race;

use EtoA\Core\AbstractRepository;

class RaceDataRepository extends AbstractRepository
{
    /**
     * Returns an array of race names indexed by the race id.
     *
     * @return array<int, string>
     */
    public function getRaceNames(): array
    {
        return $this->createQueryBuilder()
            ->select('r.race_id, r.race_name')
            ->from('races', 'r')
            ->andWhere('r.race_active = 1')
            ->orderBy('r.race_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    /**
     * Returns an array of race leader titles indexed by the race id.
     *
     * @return array<int, string>
     */
    public function getRaceLeaderTitles(): array
    {
        return $this->createQueryBuilder()
            ->select('r.race_id, r.race_leadertitle')
            ->from('races', 'r')
            ->andWhere('r.race_active = 1')
            ->orderBy('r.race_name')
            ->execute()
            ->fetchAllKeyValue();
    }

    public function getRace(int $raceId): ?Race
    {
        $data = $this->createQueryBuilder()
            ->select('r.*')
            ->from('races', 'r')
            ->where('r.race_id = :id')
            ->andWhere('r.race_active = 1')
            ->setParameter('id', $raceId)
            ->execute()
            ->fetchAssociative();

        return $data !== false ? new Race($data) : null;
    }

    /**
     * @return Race[]
     */
    public function getActiveRaces(string $order = 'race_name', string $sort = 'ASC'): array
    {
        $data = $this->createQueryBuilder()
            ->select('r.*')
            ->from('races', 'r')
            ->where('r.race_active = 1')
            ->orderBy('r.' . $order, $sort)
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new Race($row), $data);
    }

    /**
     * @return array<array<string,int>>
     */
    public function getNumberOfRacesByType(): array
    {
        return $this->getConnection()
            ->executeQuery(
                "SELECT
                    races.race_name as name,
                    COUNT(users.user_race_id) as cnt
                FROM
                    users
                INNER JOIN
                    races
                ON
                    users.user_race_id = races.race_id
                    AND users.user_ghost = 0
                    AND users.user_hmode_from = 0
                    AND users.user_hmode_to = 0
                GROUP BY
                    races.race_id
                ORDER BY
                    cnt DESC;"
            )
            ->fetchAllAssociative();
    }
}
