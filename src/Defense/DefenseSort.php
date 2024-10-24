<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSort;

class DefenseSort extends AbstractSort
{
    public const USER_SORT_VALUES = [
        "order" => "Vorgabe",
        "name" => "Name",
        "points" => "Kosten",
        "fields" => "Felder",
        "weapon" => "Waffen",
        "structure" => "Struktur",
        "shield" => "Schild",
        "costs_metal" => "Titan",
        "costs_crystal" => "Silizium",
        "costs_plastic" => "PVC",
        "costs_fuel" => "Tritium",
    ];

    public static function id(): DefenseSort
    {
        return new DefenseSort(['q.id' => null]);
    }

    public static function name(): DefenseSort
    {
        return new DefenseSort(['q.name' => null]);
    }

    public static function category(): DefenseSort
    {
        return new DefenseSort(['q.catId' => null, 'q.order' => null, 'q.name' => null]);
    }

    public static function specialWithUserSort(string $userSort, string $order): DefenseSort
    {
        if (isset(self::USER_SORT_VALUES[$userSort])) {
            return new DefenseSort(['q.' . $userSort => $order]);
        }

        return new DefenseSort(['q.order' => null]);
    }
}
