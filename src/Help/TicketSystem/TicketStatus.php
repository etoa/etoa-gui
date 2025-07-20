<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

/**
 * Workflow status of a ticket
 */
enum TicketStatus: string
{
    case NEW = 'new';
    case ASSIGNED = 'assigned';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::CLOSED => 'Abgeschlossen',
            self::ASSIGNED => 'Zugeteilt',
            self::NEW => 'Neu',
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
