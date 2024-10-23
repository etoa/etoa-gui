<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Help\TicketSystem\TicketMessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Encapsulates a single ticket message
 */

#[ORM\Entity(repositoryClass: TicketMessageRepository::class)]
#[ORM\Table(name: 'ticket_msg')]
class TicketMessage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $ticketId;

    #[ORM\Column]
    private string $message;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column(type: "integer")]
    private ?int $userId;

    #[ORM\Column(type: "integer")]
    private ?int $adminId;

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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTicketId(): ?int
    {
        return $this->ticketId;
    }

    public function setTicketId(int $ticketId): static
    {
        $this->ticketId = $ticketId;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setMessage(string $message): static
    {
        $this->message = $message;

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
}
