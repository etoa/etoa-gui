<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Alliance\AllianceSpendRepository;

#[ORM\Entity(repositoryClass: AllianceSpendRepository::class)]
#[ORM\Table(name: 'alliance_spends')]
class AllianceSpend
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "alliance_spend_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'alliance_spend_alliance_id', referencedColumnName: 'alliance_id')]
    #[ORM\ManyToOne(targetEntity: Alliance::class)]
    private ?Alliance $alliance = null;

    #[ORM\JoinColumn(name: 'alliance_spend_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private ?User $user = null;

    #[ORM\Column(name: "alliance_spend_metal")]
    private int $metal;

    #[ORM\Column(name: "alliance_spend_crystal")]
    private int $crystal;

    #[ORM\Column(name: "alliance_spend_plastic")]
    private int $plastic;

    #[ORM\Column(name: "alliance_spend_fuel")]
    private int $fuel;

    #[ORM\Column(name: "alliance_spend_food")]
    private int $food;

    #[ORM\Column(name: "alliance_spend_time")]
    private int $time;

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

    public function getFuel(): ?int
    {
        return $this->fuel;
    }

    public function setFuel(int $fuel): static
    {
        $this->fuel = $fuel;

        return $this;
    }

    public function getFood(): ?int
    {
        return $this->food;
    }

    public function setFood(int $food): static
    {
        $this->food = $food;

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

    public function getAlliance(): ?Alliance
    {
        return $this->alliance;
    }

    public function setAlliance(?Alliance $alliance): static
    {
        $this->alliance = $alliance;

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
}
