<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\Database\AbstractSearch;

class ShipSearch extends AbstractSearch
{
    public static function create(): ShipSearch
    {
        return new ShipSearch();
    }

    public function id(int $id): ShipSearch
    {
        $this->parts[] = "ship_id = :id";
        $this->parameters['id'] = $id;

        return $this;
    }

    /**
     * @param int[] $ids
     */
    public function ids(array $ids): ShipSearch
    {
        $this->parts[] = "ship_id IN(:ids)";
        $this->stringArrayParameters['ids'] = $ids;

        return $this;
    }

    public function nameLike(string $name): ShipSearch
    {
        $this->parts[] = "ship_name LIKE :nameLike";
        $this->parameters['nameLike'] = $name . '%';

        return $this;
    }

    public function name(string $name): ShipSearch
    {
        $this->parts[] = "ship_name = :name";
        $this->parameters['name'] = $name;

        return $this;
    }

    public function showOrBuildable(): ShipSearch
    {
        $this->parts[] = 'ship_show=1 OR ship_buildable=1';

        return $this;
    }

    public function show(bool $show): ShipSearch
    {
        $this->parts[] = 'ship_show = :show';
        $this->parameters['show'] = (int) $show;

        return $this;
    }

    public function buildable(): ShipSearch
    {
        $this->parts[] = 'ship_buildable=1';

        return $this;
    }

    public function special(bool $special): ShipSearch
    {
        $this->parts[] = 'special_ship = :special';
        $this->parameters['special'] = (int) $special;

        return $this;
    }

    public function tradeable(bool $tradeable): ShipSearch
    {
        $this->parts[] = 'ship_tradable = :tradeable';
        $this->parameters['tradeable'] = (int) $tradeable;

        return $this;
    }

    public function allianceShip(bool $allianceShip): ShipSearch
    {
        if ($allianceShip) {
            $this->parts[] = 'ship_alliance_costs > 0';
        } else {
            $this->parts[] = 'ship_alliance_costs = 0';
        }

        return $this;
    }

    public function raceId(int $raceId): ShipSearch
    {
        $this->parts[] = 'ship_race_id = :raceId';
        $this->parameters['raceId'] = $raceId;

        return $this;
    }

    public function raceOrNull(int $raceId): ShipSearch
    {
        $this->parts[] = 'ship_race_id = 0 OR ship_race_id = :raceIdOrNull';
        $this->parameters['raceIdOrNull'] = $raceId;

        return $this;
    }

    public function producesPower(): self
    {
        $this->parts[] = 'ship_prod_power > 0';

        return $this;
    }
}
