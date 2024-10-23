<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Log\DebrisLogRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DebrisLogRepository::class)]
#[ORM\Table(name: 'logs_debris')]
class DebrisLog
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(type: "integer")]
    private int $adminId;

    #[ORM\Column(type: "integer")]
    private int $userId;

    #[ORM\Column(type: "integer")]
    private int $timestamp;

    #[ORM\Column(type: "integer")]
    private int $metal;

    #[ORM\Column(type: "integer")]
    private int $crystal;

    #[ORM\Column(type: "integer")]
    private int $plastic;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

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

    public function getMetal(): ?int
    {
        return $this->metal;
    }

    public function setMetal(int $metal): static
    {
        $this->metal = $metal;

        return $this;
    }

    public function getCrystal(): ?int
    {
        return $this->crystal;
    }

    public function setCrystal(int $crystal): static
    {
        $this->crystal = $crystal;

        return $this;
    }

    public function getPlastic(): ?int
    {
        return $this->plastic;
    }

    public function setPlastic(int $plastic): static
    {
        $this->plastic = $plastic;

        return $this;
    }
}
