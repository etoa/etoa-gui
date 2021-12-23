<?php declare(strict_types=1);

namespace EtoA\Building;

class BuildingBuildType
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            1 => "Ausbau abgebrochen",
            2 => "Abriss abgebrochen",
            3 => "Ausbau",
            4 => "Abriss",
        ];
    }
}
