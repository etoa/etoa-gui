<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use EtoA\Fleet\FleetShipRepository;

#[ORM\Entity(repositoryClass: FleetShipRepository::class)]
#[ORM\Table(name: 'fleet_ships')]
class FleetShip
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "fs_id")]
    private int $id;

    #[ORM\JoinColumn(name: 'fs_fleet_id', referencedColumnName: 'id')]
    #[ORM\ManyToOne(targetEntity: Fleet::class)]
    private ?Fleet $fleet = null;

    #[ORM\JoinColumn(name: 'fs_ship_id', referencedColumnName: 'ship_id')]
    #[ORM\ManyToOne(targetEntity: Ship::class)]
    private ?Ship $ship = null;

    #[ORM\Column(name: "fs_ship_cnt")]
    private int $count;

    #[ORM\Column(name: "fs_ship_faked")]
    private int $shipFaked;

    #[ORM\Column(name: "fs_special_ship", type: Types::BOOLEAN)]
    private bool $specialShip;

    #[ORM\Column(name: "fs_special_ship_level")]
    private int $specialShipLevel;

    #[ORM\Column(name: "fs_special_ship_exp")]
    private int $specialShipExperience;

    #[ORM\Column(name: "fs_special_ship_bonus_weapon")]
    private int $specialShipBonusWeapon;

    #[ORM\Column(name: "fs_special_ship_bonus_structure")]
    private int $specialShipBonusStructure;

    #[ORM\Column(name: "fs_special_ship_bonus_shield")]
    private int $specialShipBonusShield;

    #[ORM\Column(name: "fs_special_ship_bonus_heal")]
    private int $specialShipBonusHeal;

    #[ORM\Column(name: "fs_special_ship_bonus_capacity")]
    private int $specialShipBonusCapacity;

    #[ORM\Column(name: "fs_special_ship_bonus_speed")]
    private int $specialShipBonusSpeed;

    #[ORM\Column(name: "fs_special_ship_bonus_pilots")]
    private int $specialShipBonusPilots;

    #[ORM\Column(name: "fs_special_ship_bonus_tarn")]
    private int $specialShipBonusTarn;

    #[ORM\Column(name: "fs_special_ship_bonus_antrax")]
    private int $specialShipBonusAnthrax;

    #[ORM\Column(name: "fs_special_ship_bonus_forsteal")]
    private int $specialShipBonusForSteal;

    #[ORM\Column(name: "fs_special_ship_bonus_build_destroy")]
    private int $specialShipBonusBuildDestroy;

    #[ORM\Column(name: "fs_special_ship_bonus_antrax_food")]
    private int $specialShipBonusAnthraxFood;

    #[ORM\Column(name: "fs_special_ship_bonus_deactivade")]
    private int $specialShipBonusDeactivate;

    #[ORM\Column(name: "fs_special_ship_bonus_readiness")]
    private int $specialShipBonusReadiness;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): static
    {
        $this->count = $count;

        return $this;
    }

    public function getShipFaked(): ?int
    {
        return $this->shipFaked;
    }

    public function setShipFaked(int $shipFaked): static
    {
        $this->shipFaked = $shipFaked;

        return $this;
    }

    public function isSpecialShip(): ?bool
    {
        return $this->specialShip;
    }

    public function setSpecialShip(bool $specialShip): static
    {
        $this->specialShip = $specialShip;

        return $this;
    }

    public function getSpecialShipLevel(): ?int
    {
        return $this->specialShipLevel;
    }

    public function setSpecialShipLevel(int $specialShipLevel): static
    {
        $this->specialShipLevel = $specialShipLevel;

        return $this;
    }

    public function getSpecialShipExperience(): ?int
    {
        return $this->specialShipExperience;
    }

    public function setSpecialShipExperience(int $specialShipExperience): static
    {
        $this->specialShipExperience = $specialShipExperience;

        return $this;
    }

    public function getSpecialShipBonusWeapon(): ?int
    {
        return $this->specialShipBonusWeapon;
    }

    public function setSpecialShipBonusWeapon(int $specialShipBonusWeapon): static
    {
        $this->specialShipBonusWeapon = $specialShipBonusWeapon;

        return $this;
    }

    public function getSpecialShipBonusStructure(): ?int
    {
        return $this->specialShipBonusStructure;
    }

    public function setSpecialShipBonusStructure(int $specialShipBonusStructure): static
    {
        $this->specialShipBonusStructure = $specialShipBonusStructure;

        return $this;
    }

    public function getSpecialShipBonusShield(): ?int
    {
        return $this->specialShipBonusShield;
    }

    public function setSpecialShipBonusShield(int $specialShipBonusShield): static
    {
        $this->specialShipBonusShield = $specialShipBonusShield;

        return $this;
    }

    public function getSpecialShipBonusHeal(): ?int
    {
        return $this->specialShipBonusHeal;
    }

    public function setSpecialShipBonusHeal(int $specialShipBonusHeal): static
    {
        $this->specialShipBonusHeal = $specialShipBonusHeal;

        return $this;
    }

    public function getSpecialShipBonusCapacity(): ?int
    {
        return $this->specialShipBonusCapacity;
    }

    public function setSpecialShipBonusCapacity(int $specialShipBonusCapacity): static
    {
        $this->specialShipBonusCapacity = $specialShipBonusCapacity;

        return $this;
    }

    public function getSpecialShipBonusSpeed(): ?int
    {
        return $this->specialShipBonusSpeed;
    }

    public function setSpecialShipBonusSpeed(int $specialShipBonusSpeed): static
    {
        $this->specialShipBonusSpeed = $specialShipBonusSpeed;

        return $this;
    }

    public function getSpecialShipBonusPilots(): ?int
    {
        return $this->specialShipBonusPilots;
    }

    public function setSpecialShipBonusPilots(int $specialShipBonusPilots): static
    {
        $this->specialShipBonusPilots = $specialShipBonusPilots;

        return $this;
    }

    public function getSpecialShipBonusTarn(): ?int
    {
        return $this->specialShipBonusTarn;
    }

    public function setSpecialShipBonusTarn(int $specialShipBonusTarn): static
    {
        $this->specialShipBonusTarn = $specialShipBonusTarn;

        return $this;
    }

    public function getSpecialShipBonusAnthrax(): ?int
    {
        return $this->specialShipBonusAnthrax;
    }

    public function setSpecialShipBonusAnthrax(int $specialShipBonusAnthrax): static
    {
        $this->specialShipBonusAnthrax = $specialShipBonusAnthrax;

        return $this;
    }

    public function getSpecialShipBonusForSteal(): ?int
    {
        return $this->specialShipBonusForSteal;
    }

    public function setSpecialShipBonusForSteal(int $specialShipBonusForSteal): static
    {
        $this->specialShipBonusForSteal = $specialShipBonusForSteal;

        return $this;
    }

    public function getSpecialShipBonusBuildDestroy(): ?int
    {
        return $this->specialShipBonusBuildDestroy;
    }

    public function setSpecialShipBonusBuildDestroy(int $specialShipBonusBuildDestroy): static
    {
        $this->specialShipBonusBuildDestroy = $specialShipBonusBuildDestroy;

        return $this;
    }

    public function getSpecialShipBonusAnthraxFood(): ?int
    {
        return $this->specialShipBonusAnthraxFood;
    }

    public function setSpecialShipBonusAnthraxFood(int $specialShipBonusAnthraxFood): static
    {
        $this->specialShipBonusAnthraxFood = $specialShipBonusAnthraxFood;

        return $this;
    }

    public function getSpecialShipBonusDeactivate(): ?int
    {
        return $this->specialShipBonusDeactivate;
    }

    public function setSpecialShipBonusDeactivate(int $specialShipBonusDeactivate): static
    {
        $this->specialShipBonusDeactivate = $specialShipBonusDeactivate;

        return $this;
    }

    public function getSpecialShipBonusReadiness(): ?int
    {
        return $this->specialShipBonusReadiness;
    }

    public function setSpecialShipBonusReadiness(int $specialShipBonusReadiness): static
    {
        $this->specialShipBonusReadiness = $specialShipBonusReadiness;

        return $this;
    }

    public function getFleet(): ?Fleet
    {
        return $this->fleet;
    }

    public function setFleet(?Fleet $fleet): static
    {
        $this->fleet = $fleet;

        return $this;
    }

    public function getShip(): ?Ship
    {
        return $this->ship;
    }

    public function setShip(?Ship $ship): static
    {
        $this->ship = $ship;

        return $this;
    }
}
