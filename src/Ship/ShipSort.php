<?php declare(strict_types=1);

namespace EtoA\Ship;

use EtoA\Core\Database\AbstractSort;

class ShipSort extends AbstractSort
{
    public const USER_SORT_VALUES = [
        "name" => "Name",
        "points" => "Kosten",
        "weapon" => "Waffen",
        "structure" => "Struktur",
        "shield" => "Schild",
        "speed" => "Geschwindigkeit",
        "time2start" => "Startzeit",
        "time2land" => "Landezeit",
        "capacity" => "Kapazität",
        "costs_metal" => "Titan",
        "costs_crystal" => "Silizium",
        "costs_plastic" => "PVC",
        "costs_fuel" => "Tritium",
    ];

    public static function id(): ShipSort
    {
        return new ShipSort(['ship_id' => null]);
    }

    public static function name(): ShipSort
    {
        return new ShipSort(['ship_name' => null]);
    }

    public static function category(): ShipSort
    {
        return new ShipSort(['ship_cat_id' => null, 'ship_order' => null, 'ship_name' => null]);
    }

    public static function haven(): ShipSort
    {
        return new ShipSort(['special_ship' => 'DESC', 'ship_launchable' => 'DESC', 'ship_name' => null]);
    }

    public static function specialWithUserSort(string $userSort, string $order): ShipSort
    {
        if (isset(self::USER_SORT_VALUES[$userSort])) {
            return new ShipSort(['special_ship' => 'DESC', 'ship_' . $userSort => $order]);
        }

        return new ShipSort(['special_ship' => 'DESC', 'ship_name' => null]);
    }
}
