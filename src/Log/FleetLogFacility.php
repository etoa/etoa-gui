<?php declare(strict_types=1);

namespace EtoA\Log;

class FleetLogFacility
{
    public const OTHER = 0;
    public const LAUNCH = 1;
    public const CANCEL = 2;
    public const ACTION = 3;
    public const RETURN = 4;

    public const FACILITIES = [
        self::OTHER => "Sonstige",
        self::LAUNCH => "Start",
        self::CANCEL => "Abbruch",
        self::ACTION => "Aktion",
        self::RETURN => "Rückkehr",
    ];
}
