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
        $this->parts[] = 'q.entityFrom = :entityFrom';
        $this->parameters['entityFrom'] = $entityFrom;

        return $this;
    }

    public function landed(): self
    {
        $this->parts[] = 'q.landTime < :landTime';
        $this->parameters['landTime'] = time();

        return $this;
    }
}
