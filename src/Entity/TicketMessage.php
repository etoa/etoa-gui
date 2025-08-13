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

    #[ORM\JoinColumn(name: 'ticket_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Ticket::class, inversedBy:'ticketMessages')]
    private ?Ticket $ticket = null;

    #[ORM\Column]
    private string $message = '';

    #[ORM\Column(type: "integer")]
    private int $timestamp = 0;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'admin_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    private ?AdminUser $admin = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getTicket(): ?Ticket
    {
        return $this->ticket;
    }

    public function setTicket(?Ticket $ticket): static
    {
        $this->ticket = $ticket;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getAdmin(): ?AdminUser
    {
        return $this->admin;
    }

    public function setAdmin(?AdminUser $admin): static
    {
        $this->admin = $admin;

        return $this;
    }

    public function getAuthor():string
    {
        if ($this->getUser()) {
            return $this->getUser()->getNick();
        }
        if ($this->getAdmin()) {
            return $this->getAdmin()->getNick() . " (Admin)";
        }

        return "System";
    }
}
