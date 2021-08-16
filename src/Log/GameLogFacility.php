<?php declare(strict_types=1);

namespace EtoA\Log;

class GameLogFacility
{
    public const OTHER = 0;
    public const BUILD = 1;
    public const TECH = 2;
    public const SHIP = 3;
    public const DEF = 4;
    public const QUESTS = 5;

    public const FACILITIES = [
        self::OTHER => "Sonstiges",
        self::BUILD => "Gebäude",
        self::TECH => "Forschungen",
        self::SHIP => "Schiffe",
        self::DEF => "Verteidigungsanlagen",
        self::QUESTS => "Quests",
    ];
}
