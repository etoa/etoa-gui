<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipBuildType
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            0 => "Bau abgebrochen",
            1 => "Bau",
        ];
    }
}
