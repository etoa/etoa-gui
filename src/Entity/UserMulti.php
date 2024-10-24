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

    #[ORM\Column]
    private int $userId;

    #[ORM\Column]
    private int $multiUserId;

    #[ORM\Column]
    private ?string $multiUserNick;

    #[ORM\Column]
    private string $reason;

    #[ORM\Column]
    private bool $active;

    #[ORM\Column]
    private int $timestamp;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getMultiUserId(): ?int
    {
        return $this->multiUserId;
    }

    public function setMultiUserId(int $multiUserId): static
    {
        $this->multiUserId = $multiUserId;

        return $this;
    }

    public function getMultiUserNick(): ?string
    {
        return $this->multiUserNick;
    }

    public function setMultiUserNick(string $multiUserNick): static
    {
        $this->multiUserNick = $multiUserNick;

        return $this;
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
}
