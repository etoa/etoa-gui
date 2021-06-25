<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipRequiredTechnology
{
    public int $id;
    public string $name;
    public int $requiredLevel;

    public static function createFromTech(array $data): ShipRequiredTechnology
    {
        $requirement = new ShipRequiredTechnology();
        $requirement->id = (int) $data['tech_id'];
        $requirement->name = $data['tech_name'];
        $requirement->requiredLevel = (int) $data['req_level'];

        return $requirement;
    }

    public static function createFromShip(array $data): ShipRequiredTechnology
    {
        $requirement = new ShipRequiredTechnology();
        $requirement->id = (int) $data['ship_id'];
        $requirement->name = $data['ship_name'];
        $requirement->requiredLevel = (int) $data['req_level'];

        return $requirement;
    }
}
