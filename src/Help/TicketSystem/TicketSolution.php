<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

/**
 * Solution type of a ticket
 *
 * @todo Once compatible with PHP 8.1, convert to Enum
 */
class TicketSolution
{
    const OPEN = "open";
    const SOLVED = "solved";
    const DUPLICATE = "duplicate";
    const INVALID = "invalid";

    public static function label(string $status): string
    {
        if ($status == self::SOLVED) {
            return "Behoben";
        }
        if ($status == self::DUPLICATE) {
            return "Duplikat";
        }
        if ($status == self::INVALID) {
            return "Ungültig";
        }
        return 'Offen';
    }

    /**
     * @return array<string,string>
     */
    public static function items(): array
    {
        return [
            self::OPEN => self::label(self::OPEN),
            self::SOLVED => self::label(self::SOLVED),
            self::DUPLICATE => self::label(self::DUPLICATE),
            self::INVALID => self::label(self::INVALID),
        ];
    }
}
