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

    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\JoinColumn(name: 'admin_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: AdminUser::class)]
    private ?AdminUser $admin = null;

    #[ORM\Column(type: "integer")]
    private int $time;

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

    public function getTime(): ?int
    {
        return $this->time;
    }

    public function setTime(int $time): static
    {
        $this->time = $time;

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
}
