<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipTransform
{
    public int $availableShips = 0;
    public int $availableDefense = 0;
    public int $shipId;
    public int $defenseId;
    public int $numberOfDefense;

    public static function createFromShip(array $data): ShipTransform
    {
        $transform = new ShipTransform();
        $transform->shipId = (int) $data['ship_id'];
        $transform->defenseId = (int) $data['def_id'];
        $transform->availableShips = (int) $data['count'];
        $transform->numberOfDefense = (int) $data['num_def'];

        return $transform;
    }

    public static function createFromDefense(array $data): ShipTransform
    {
        $transform = new ShipTransform();
        $transform->shipId = (int) $data['ship_id'];
        $transform->defenseId = (int) $data['def_id'];
        $transform->availableDefense = (int) $data['count'];
        $transform->numberOfDefense = (int) $data['num_def'];

        return $transform;
    }
}
