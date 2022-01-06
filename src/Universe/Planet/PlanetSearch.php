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
        $this->parts[] = 'planet_user_id > 0';

        return $this;
    }

    public function withoutUser(): self
    {
        $this->parts[] = 'planet_user_id = 0';

        return $this;
    }

    public function mainPlanet(): self
    {
        $this->parts[] = 'planet_user_main = 1';

        return $this;
    }
}
