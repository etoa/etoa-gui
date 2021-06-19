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
}
