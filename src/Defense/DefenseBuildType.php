<?php declare(strict_types=1);

namespace EtoA\Defense;

class DefenseBuildType
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
