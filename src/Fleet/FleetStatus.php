<?php

declare(strict_types=1);

namespace EtoA\Fleet;

class FleetStatus
{
    public const DEPARTURE = 0;
    public const ARRIVAL = 1;
    public const CANCELLED = 2;
    public const WAITING = 3;

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::DEPARTURE => "Hinflug",
            self::ARRIVAL => "Rückflug",
            self::CANCELLED => "Abgebrochen",
            self::WAITING => "Allianz",
        ];
    }
}
