<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

/**
 * Workflow status of a ticket
 *
 * @todo Once compatible with PHP 8.1, convert to Enum
 */
class TicketStatus
{
    const NEW = "new";
    const ASSIGNED = "assigned";
    const CLOSED = "closed";

    public static function label(string $status): string
    {
        if ($status == self::CLOSED) {
            return "Abgeschlossen";
        }
        if ($status == self::ASSIGNED) {
            return "Zugeteilt";
        }

        return 'Neu';
    }

    /**
     * @return array<string,string>
     */
    public static function items(): array
    {
        return [
            self::NEW => self::label(self::NEW),
            self::ASSIGNED => self::label(self::ASSIGNED),
            self::CLOSED => self::label(self::CLOSED),
        ];
    }
}
