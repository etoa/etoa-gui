<?php declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Core\Database\AbstractSearch;

class PlanetSearch extends AbstractSearch
{
    public static function create(): PlanetSearch
    {
        return new PlanetSearch();
    }

    /**
     * @param int[] $ids
     */
    public function idIn(array $ids): self
    {
        $this->parts[] = 'p.id IN (:planetIds)';
        $this->stringArrayParameters['planetIds'] = $ids;

        return $this;
    }

    public function assignedToUser(): self
    {
        $this->parts[] = 'q.user is not null';

        return $this;
    }

    public function withoutUser(): self
    {
        $this->parts[] = 'q.user is null';

        return $this;
    }

    public function mainPlanet(): self
    {
        $this->parts[] = 'q.mainPlanet = 1';

        return $this;
    }
}
