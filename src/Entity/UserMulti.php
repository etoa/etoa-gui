<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\User\UserMultiRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserMultiRepository::class)]
class UserMulti
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column]
    private int $id;

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class, cascade: ['persist'], inversedBy: 'userMultis')]
    private ?User $user;

    #[ORM\JoinColumn(name: 'multi_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $multiUser;

    #[ORM\Column(name: 'connection')]
    private string $reason = '';

    #[ORM\Column(name: 'activ')]
    private bool $active = true;

    #[ORM\Column]
    private int $timestamp = 0;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getMultiUser(): ?User
    {
        return $this->multiUser;
    }

    public function setMultiUser(?User $multiUser): static
    {
        $this->multiUser = $multiUser;

        return $this;
    }
}
