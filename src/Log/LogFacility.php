<?php declare(strict_types=1);

namespace EtoA\Log;

class LogFacility
{
    public const OTHER = 0;
    public const BATTLE = 1;
    public const INSULT = 2;
    public const USER = 3;
    public const SYSTEM = 4;
    public const ALLIANCE = 5;
    public const GALAXY = 6;
    public const MARKET = 7;
    public const ADMIN = 8;
    public const MULTICHEAT = 9;
    public const MULTITRADE = 10;
    public const SHIPTRADE = 11;
    public const RECYCLING = 12;
    public const FLEETACTION = 13;
    public const ECONOMY = 14;
    public const UPDATES = 15;
    public const SHIPS = 16;
    public const RANKING = 17;
    public const ILLEGALACTION = 18;

    public const FACILITIES = [
        self::OTHER => "Sonstiges",
        self::BATTLE => "Kampfberichte",
        self::INSULT => "Beleidigungen",
        self::USER => "User",
        self::SYSTEM => "System",
        self::ALLIANCE => "Allianzen",
        self::GALAXY => "Galaxie",
        self::MARKET => "Markt",
        self::ADMIN => "Administration",
        self::MULTICHEAT => "Multi-Verstoss",
        self::MULTITRADE => "Multi-Handel",
        self::SHIPTRADE => "Schiffshandel",
        self::RECYCLING => "Recycling",
        self::FLEETACTION => "Flottenaktionen",
        self::ECONOMY => "Wirtschaft",
        self::UPDATES => "Updates",
        self::SHIPS => "Schiffe",
        self::RANKING => "Ranglisten",
        self::ILLEGALACTION => "Illegale Useraktion",
    ];
}
