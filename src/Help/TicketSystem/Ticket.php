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
