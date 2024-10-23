<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Help\TicketSystem\TicketSolution;
use EtoA\Help\TicketSystem\TicketStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
class Ticket
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id = 0;

    #[ORM\Column]
    private string $solution;

    #[ORM\Column]
    private string $status;

    #[ORM\Column(type: "integer")]
    private int $catId;

    #[ORM\Column(type: "integer")]
    private int $userId;

    #[ORM\Column(type: "integer")]
    private ?int $adminId = null;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column]
    private ?string $adminComment = null;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolution(): ?string
    {
        return $this->solution;
    }

    public function setSolution(string $solution): static
    {
        $this->solution = $solution;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getCatId(): ?int
    {
        return $this->catId;
    }

    public function setCatId(int $catId): static
    {
        $this->catId = $catId;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getAdminId(): ?int
    {
        return $this->adminId;
    }

    public function setAdminId(int $adminId): static
    {
        $this->adminId = $adminId;

        return $this;
    }

    public function getTimestamp(): ?int
    {
        return $this->timestamp;
    }

    public function setTimestamp(int $timestamp): static
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getAdminComment(): ?string
    {
        return $this->adminComment;
    }

    public function setAdminComment(string $adminComment): static
    {
        $this->adminComment = $adminComment;

        return $this;
    }
}
