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
            ->orderBy('r.race_name')
            ->execute()
            ->fetchAllKeyValue();
    }
}
