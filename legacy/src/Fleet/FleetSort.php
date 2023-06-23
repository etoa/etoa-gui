<?php declare(strict_types=1);

namespace EtoA\Fleet;

use EtoA\Core\Database\AbstractSort;

class FleetSort extends AbstractSort
{
    public static function landtime(string $order): FleetSort
    {
        return new FleetSort(['landtime' => $order]);
    }

    public static function launchtime(string $order): FleetSort
    {
        return new FleetSort(['launchtime' => $order]);
    }
}
