<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceDiplomacyLevel
{
    public const BND_REQUEST = 0;
    public const BND_CONFIRMED = 2;
    public const WAR = 3;
    public const PEACE = 4;

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::BND_REQUEST => 'Bündnisanfrage',
            self::BND_CONFIRMED => 'Bündnis',
            self::WAR => 'Krieg',
            self::PEACE => 'Frieden',
        ];
    }
}
