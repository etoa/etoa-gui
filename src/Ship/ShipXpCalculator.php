<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipXpCalculator
{
    public static function xpByLevel(int $base_xp, float $factor, int $level): int
    {
        return $base_xp * intpow($factor, $level - 1);
    }

    public static function levelByXp(int $base_xp, float $factor, int $xp): int
    {
        return (int) max(0, floor(1 + ((log($xp) - log($base_xp)) / log($factor))));
    }
}
