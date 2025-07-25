<?php

declare(strict_types=1);

namespace EtoA\Fleet;

enum FleetStatus: int
{
    case DEPARTURE = 0;
    case ARRIVAL = 1;
    case CANCELLED = 2;
    case WAITING = 3;

    public function label(): string
    {
        return match($this) {
            self::DEPARTURE => "Hinflug",
            self::ARRIVAL => "Rückflug",
            self::CANCELLED => "Abgebrochen",
            self::WAITING => "Allianz",
        };
    }

    /**
     * @return array<int, string>
     */
    public static function all(): array
    {
        return array_reduce(self::cases(), function ($carry, self $status) {
            $carry[$status->value] = $status->label();
            return $carry;
        }, []);
    }
}
