<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipSearch
{
    /** @var string[] */
    public array $parts = [];
    /** @var array<string, mixed> */
    public array $parameters = [];

    public static function create(): ShipSearch
    {
        return new ShipSearch();
    }

    public function nameLike(string $name): ShipSearch
    {
        $this->parts[] = "ship_name LIKE :nameLike";
        $this->parameters['nameLike'] = $name . '%';

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

    public function raceOrNull(int $raceId): ShipSearch
    {
        $this->parts[] = 'ship_race_id = 0 OR ship_race_id = :raceId';
        $this->parameters['raceId'] = $raceId;

        return $this;
    }
}
