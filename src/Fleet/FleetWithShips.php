<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\Database\PropertyAssign;

class FleetWithShips extends Fleet
{
    /** @var array{shipId: int, count: int}[] */
    public array $ships;

    /**
     * @param FleetShip[]|array<int, int> $ships
     */
    public function __construct(Fleet $fleet, array $ships)
    {
        PropertyAssign::assign($fleet, $this);

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
