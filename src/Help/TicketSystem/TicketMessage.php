<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

/**
 * Encapsulates a single ticket message
 */
class TicketMessage
{
    public int $id;
    public string $message;
    public int $timestamp;
    public ?int $userId;
    public ?int $adminId;
}
