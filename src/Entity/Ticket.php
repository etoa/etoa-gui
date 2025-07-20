<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Help\TicketSystem\TicketSolution;
use EtoA\Help\TicketSystem\TicketStatus;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TicketRepository::class)]
#[ORM\Table(name: 'tickets')]
class Ticket
{
    public function __construct() {
        $this->ticketMessages = new ArrayCollection();
    }

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id = 0;

    #[ORM\Column]
    private ?string $solution = 'open';

    #[ORM\Column]
    private string $status = 'new';

    #[ORM\JoinColumn(name: 'cat_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: TicketCategory::class)]
    private ?TicketCategory $cat = null;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'admin_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    private ?AdminUser $admin = null;

    #[ORM\OneToMany(mappedBy: 'ticket', targetEntity: TicketMessage::class, cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(name: 'id', referencedColumnName: 'ticket_id')]
    private Collection $ticketMessages;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column]
    private ?string $adminComment = null;

    public function getIdString(): string
    {
        return "#" . sprintf("%'.06d", $this->id);
    }

    public function getStatusName(): string
    {
        if ($this->status == TicketStatus::CLOSED && isset(TicketSolution::items()[$this->solution])) {
            return TicketStatus::items()[$this->status] . ": " . TicketSolution::items()[$this->solution];
        }

        return TicketStatus::items()[$this->status];
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

    public function getCat(): ?TicketCategory
    {
        return $this->cat;
    }

    public function setCat(?TicketCategory $cat): static
    {
        $this->cat = $cat;

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

    /**
     * @return Collection<int, TicketMessage>
     */
    public function getTicketMessages(): Collection
    {
        return $this->ticketMessages;
    }

    public function addTicketMessage(TicketMessage $ticketMessage): static
    {
        if (!$this->ticketMessages->contains($ticketMessage)) {
            $this->ticketMessages->add($ticketMessage);
            $ticketMessage->setTicket($this);
        }

        return $this;
    }

    public function removeTicketMessage(TicketMessage $ticketMessage): static
    {
        if ($this->ticketMessages->removeElement($ticketMessage)) {
            // set the owning side to null (unless already changed)
            if ($ticketMessage->getTicket() === $this) {
                $ticketMessage->setTicket(null);
            }
        }

        return $this;
    }
}
