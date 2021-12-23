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
            0 => "Erforschung abgebrochen",
            3 => "Erforschung",
        ];
    }
}
