<?php

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Message\MessageIgnoreRepository;

#[ORM\Entity(repositoryClass: MessageIgnoreRepository::class)]
class MessageIgnore
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name:"ignore_id")]
    private ?int $id = null;

    #[ORM\Column(name:"ignore_owner_id")]
    private ?int $ownerId = null;

    #[ORM\Column(name:"ignore_target_id")]
    private ?int $targetId = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOwnerId(): ?int
    {
        return $this->ownerId;
    }

    public function setOwnerId(int $ownerId): static
    {
        $this->ownerId = $ownerId;

        return $this;
    }

    public function getTargetId(): ?int
    {
        return $this->targetId;
    }

    public function setTargetId(int $targetId): static
    {
        $this->targetId = $targetId;

        return $this;
    }
}
