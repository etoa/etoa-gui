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

    public static function createFromArray(array $data): TicketMessage
    {
        $message = new TicketMessage();
        $message->id = (int) $data['id'];
        $message->ticketId = (int) $data['ticket_id'];
        $message->userId = (int) $data['user_id'];
        $message->adminId = (int) $data['admin_id'];
        $message->timestamp = (int) $data['timestamp'];
        $message->message = $data['message'];
        return $message;
    }
}
