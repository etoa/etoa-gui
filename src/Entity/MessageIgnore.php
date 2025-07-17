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

    #[ORM\JoinColumn(name: 'ignore_owner_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $owner = null;

    #[ORM\JoinColumn(name: 'ignore_target_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $target = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $owner): static
    {
        $this->owner = $owner;

        return $this;
    }

    public function getTarget(): ?User
    {
        return $this->target;
    }

    public function setTarget(?User $target): static
    {
        $this->target = $target;

        return $this;
    }
}
