<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\Core\Database\AbstractSearch;

class MissileFlightSearch extends AbstractSearch
{
    public static function create(): MissileFlightSearch
    {
        return new MissileFlightSearch();
    }

    public function entityFrom(int $entityFrom): self
    {
        $this->parts[] = 'flight_entity_from = :entityFrom';
        $this->parameters['entityFrom'] = $entityFrom;

        return $this;
    }

    public function landed(): self
    {
        $this->parts[] = 'flight_landtime < :landTime';
        $this->parameters['landTime'] = time();

        return $this;
    }
}
