<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

/**
 * Solution type of a ticket
 */
enum TicketSolution: string
{
    case OPEN = 'open';
    case SOLVED = 'solved';
    case DUPLICATE = 'duplicate';
    case INVALID = 'invalid';

    public function label(): string
    {
        return match ($this) {
            self::SOLVED => 'Behoben',
            self::DUPLICATE => 'Duplikat',
            self::INVALID => 'Ungültig',
            self::OPEN => 'Offen',
        };
    }

    /**
     * @return array<string,string>
     */
    public static function items(): array
    {
        $items = [];
        foreach (self::cases() as $case) {
            $items[$case->value] = $case->label();
        }
        return $items;
    }
}
