<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Race;
use EtoA\Entity\Ship;

class ShipSearch extends AbstractSearch
{
    public static function create(): ShipSearch
    {
        return new ShipSearch();
    }

    public function id(Ship|int $id): ShipSearch
    {
        $this->parts[] = "q.id = :id";
        $this->parameters['id'] = $id;

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function ids(array $ids): ShipSearch
    {
        $this->parts[] = "q.id IN(:ids)";
        $this->stringArrayParameters['ids'] = $ids;

        return $this;
    }

    public function nameLike(string $name): ShipSearch
    {
        $this->parts[] = "q.name LIKE :nameLike";
        $this->parameters['nameLike'] = $name . '%';

        return $this;
    }

    public function name(string $name): ShipSearch
    {
        $this->parts[] = "q.name = :name";
        $this->parameters['name'] = $name;

        return $this;
    }

    public function showOrBuildable(): ShipSearch
    {
        $this->parts[] = 'q.show=1 OR q.buildable=1';

        return $this;
    }

    public function show(bool $show): ShipSearch
    {
        $this->parts[] = 'q.show = :show';
        $this->parameters['show'] = (int) $show;

        return $this;
    }

    public function buildable(): ShipSearch
    {
        $this->parts[] = 'q.buildable=1';

        return $this;
    }

    public function special(bool $special): ShipSearch
    {
        $this->parts[] = 'q.special = :special';
        $this->parameters['special'] = (int) $special;

        return $this;
    }

    public function tradeable(bool $tradeable): ShipSearch
    {
        $this->parts[] = 'q.tradable = :tradeable';
        $this->parameters['tradeable'] = $tradeable;

        return $this;
    }

    public function allianceShip(bool $allianceShip): ShipSearch
    {
        if ($allianceShip) {
            $this->parts[] = 'q.allianceCosts > 0';
        } else {
            $this->parts[] = 'q.allianceCosts = 0';
        }

        return $this;
    }

    public function raceId(int|Race $raceId): ShipSearch
    {
        $this->parts[] = 'q.race = :raceId';
        $this->parameters['raceId'] = $raceId;

        return $this;
    }

    public function raceOrNull(Race|int $raceId): ShipSearch
    {
        $this->parts[] = 'q.race = 0 OR q.race = :raceIdOrNull';
        $this->parameters['raceIdOrNull'] = $raceId;

        return $this;
    }

    public function producesPower(): self
    {
        $this->parts[] = 'q.prodPower > 0';

        return $this;
    }
}
