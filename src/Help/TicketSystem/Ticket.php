<?php

declare(strict_types=1);

namespace EtoA\Help\TicketSystem;

class Ticket
{
    public int $id = 0;
    public string $solution;
    public string $status;
    public int $catId;
    public int $userId;
    public ?int $adminId = null;
    public int $timestamp;
    public ?string $adminComment = null;

    public static function createFromArray(array $data): Ticket
    {
        $ticket = new Ticket();
        $ticket->id = (int) $data['id'];
        $ticket->solution = $data['solution'];
        $ticket->status = $data['status'];
        $ticket->timestamp = (int) $data['timestamp'];
        $ticket->adminComment = $data['admin_comment'];
        $ticket->catId = (int) $data['cat_id'];
        $ticket->userId = (int) $data['user_id'];
        $ticket->adminId = (int) $data['admin_id'];
        return $ticket;
    }

    public function getIdString(): string
    {
        return "#" . sprintf("%'.06d", $this->id);
    }

    public function getStatusName(): string
    {
        if ($this->status == TicketStatus::CLOSED && isset(TicketSolution::items()[$this->solution])) {
            return TicketStatus::label($this->status) . ": " . TicketSolution::label($this->solution);
        }
        return TicketStatus::label($this->status);
    }
}
