<?php declare(strict_types=1);

namespace EtoA\Fleet;

class FleetWithShips extends Fleet
{
    /** @var array{shipId: int, count: int}[] */
    public array $ships;

    /**
     * @param FleetShip[]|array<int, int> $ships
     */
    public function __construct(Fleet $fleet, array $ships)
    {
        foreach ($fleet as $property => $value) {
            $this->{$property} = $value;
        }

        $this->ships = [];
        foreach ($ships as $key => $ship) {
            if ($ship instanceof FleetShip) {
                $this->ships[] = ['shipId' => $ship->shipId, 'count' => $ship->count];
            } else {
                $this->ships[] = ['shipId' => $ship, 'count' => $key];
            }
        }
    }
}
