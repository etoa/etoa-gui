<?php

declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Message\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: 'messages')]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name:"message_id", type: "integer")]
    private int $id;

    #[ORM\OneToOne(mappedBy: 'message', targetEntity: MessageData::class, cascade: ['persist', 'remove'])]
    private ?MessageData $messageData = null;

    #[ORM\JoinColumn(name: 'message_cat_id', referencedColumnName: 'cat_id')]
    #[ORM\ManyToOne(targetEntity: MessageCategory::class)]
    private ?MessageCategory $cat = null;

    #[ORM\JoinColumn(name: 'message_user_from', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messagesFrom')]
    private ?User $userFrom = null;

    #[ORM\JoinColumn(name: 'message_user_to', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'messagesTo')]
    private ?User $userTo = null;

    #[ORM\Column(name:"message_timestamp", type: "integer")]
    private int $timestamp;

    #[ORM\Column(name:"message_read", type: "boolean")]
    private bool $read = false;

    #[ORM\Column(name:"message_deleted", type: "boolean")]
    private bool $deleted = false;

    #[ORM\Column(name:"message_massmail", type: "boolean")]
    private bool $massMail = false;

    #[ORM\Column(name:"message_archived", type: "boolean")]
    private bool $archived = false;

    #[ORM\Column(name:"message_forwarded", type: "boolean")]
    private bool $forwarded = false;

    #[ORM\Column(name:"message_replied", type: "boolean")]
    private bool $replied = false;

    #[ORM\Column(name:"message_mailed", type: "boolean")]
    private bool $mailed = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function isRead(): ?bool
    {
        return $this->read;
    }

    public function setRead(bool $read): static
    {
        $this->read = $read;

        return $this;
    }

    public function isDeleted(): ?bool
    {
        return $this->deleted;
    }

    public function setDeleted(bool $deleted): static
    {
        $this->deleted = $deleted;

        return $this;
    }

    public function isMassMail(): ?bool
    {
        return $this->massMail;
    }

    public function setMassMail(bool $massMail): static
    {
        $this->massMail = $massMail;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): static
    {
        $this->archived = $archived;

        return $this;
    }

    public function isForwarded(): ?bool
    {
        return $this->forwarded;
    }

    public function setForwarded(bool $forwarded): static
    {
        $this->forwarded = $forwarded;

        return $this;
    }

    public function isReplied(): ?bool
    {
        return $this->replied;
    }

    public function setReplied(bool $replied): static
    {
        $this->replied = $replied;

        return $this;
    }

    public function isMailed(): ?bool
    {
        return $this->mailed;
    }

    public function setMailed(bool $mailed): static
    {
        $this->mailed = $mailed;

        return $this;
    }

    public function getMessageData(): ?MessageData
    {
        return $this->messageData;
    }

    public function setMessageData(?MessageData $messageData): static
    {
        $messageData->setMessage($this);
        $this->messageData = $messageData;

        return $this;
    }

    public function getCat(): ?MessageCategory
    {
        return $this->cat;
    }

    public function setCat(?MessageCategory $cat): static
    {
        $this->cat = $cat;

        return $this;
    }

    public function getUserFrom(): ?User
    {
        return $this->userFrom;
    }

    public function setUserFrom(?User $userFrom): static
    {
        $this->userFrom = $userFrom;

        return $this;
    }

    public function getUserTo(): ?User
    {
        return $this->userTo;
    }

    public function setUserTo(?User $userTo): static
    {
        $this->userTo = $userTo;

        return $this;
    }
}
