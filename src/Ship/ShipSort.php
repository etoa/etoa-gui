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
        "timeToStart" => "Startzeit",
        "timeToLand" => "Landezeit",
        "capacity" => "Kapazität",
        "costsMetal" => "Titan",
        "costsCrystal" => "Silizium",
        "costsPlastic" => "PVC",
        "costsFuel" => "Tritium",
    ];

    public static function id(): ShipSort
    {
        return new ShipSort(['q.id' => null]);
    }

    public static function name(): ShipSort
    {
        return new ShipSort(['q.name' => null]);
    }

    public static function category(): ShipSort
    {
        return new ShipSort(['q.cat' => null, 'q.order' => null, 'q.name' => null]);
    }

    public static function haven(): ShipSort
    {
        return new ShipSort(['q.special' => 'DESC', 'q.launchable' => 'DESC', 'q.name' => null]);
    }

    public static function specialWithUserSort(string $userSort, string $order): ShipSort
    {
        if (isset(self::USER_SORT_VALUES[$userSort])) {
            return new ShipSort(['q.special' => 'DESC','q.'. $userSort => $order]);
        }

        return new ShipSort(['q.special' => 'DESC', 'q.name' => null]);
    }
}
