<?php declare(strict_types=1);

namespace EtoA\Technology;

class TechnologyBuildType
{
    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            0 => "Untätig",
            3 => "Erforschung",
        ];
    }
}
