<?php declare(strict_types=1);

namespace EtoA\Entity;

use EtoA\Core\ObjectWithImage;
use EtoA\Missile\MissileDataRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MissileDataRepository::class)]
#[ORM\Table(name: 'missiles')]
class Missile implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "missile_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "missile_name")]
    private string $name;

    #[ORM\Column(name: "missile_sdesc")]
    private string $shortDescription;

    #[ORM\Column(name: "missile_ldesc")]
    private string $longDescription;

    #[ORM\Column(name: "missile_costs_metal", type: "integer")]
    private int $costsMetal;

    #[ORM\Column(name: "missile_costs_crystal", type: "integer")]
    private int $costsCrystal;

    #[ORM\Column(name: "missile_costs_plastic", type: "integer")]
    private int $costsPlastic;

    #[ORM\Column(name: "missile_costs_fuel", type: "integer")]
    private int $costsFuel;

    #[ORM\Column(name: "missile_costs_food", type: "integer")]
    private int $costsFood;

    #[ORM\Column(name: "missile_damage", type: "integer")]
    private int $damage;

    #[ORM\Column(name: "missile_speed", type: "integer")]
    private int $speed;

    #[ORM\Column(name: "missile_range", type: "integer")]
    private int $range;

    #[ORM\Column(name: "missile_deactivate", type: "integer")]
    private int $deactivate;

    #[ORM\Column(name: "missile_def", type: "integer")]
    private int $def;

    #[ORM\Column(name: "missile_launchable", type: "integer")]
    private bool $launchable;

    #[ORM\Column(name: "missile_show", type: "boolean")]
    private bool $show;

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/missiles/missile".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH."/missiles/missile".$this->id."_middle.png";
            default:
                return self::BASE_PATH."/missiles/missile".$this->id.".png";
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getShortDescription(): ?string
    {
        return $this->shortDescription;
    }

    public function setShortDescription(string $shortDescription): static
    {
        $this->shortDescription = $shortDescription;

        return $this;
    }

    public function getLongDescription(): ?string
    {
        return $this->longDescription;
    }

    public function setLongDescription(string $longDescription): static
    {
        $this->longDescription = $longDescription;

        return $this;
    }

    public function getCostsMetal(): ?int
    {
        return $this->costsMetal;
    }

    public function setCostsMetal(int $costsMetal): static
    {
        $this->costsMetal = $costsMetal;

        return $this;
    }

    public function getCostsCrystal(): ?int
    {
        return $this->costsCrystal;
    }

    public function setCostsCrystal(int $costsCrystal): static
    {
        $this->costsCrystal = $costsCrystal;

        return $this;
    }

    public function getCostsPlastic(): ?int
    {
        return $this->costsPlastic;
    }

    public function setCostsPlastic(int $costsPlastic): static
    {
        $this->costsPlastic = $costsPlastic;

        return $this;
    }

    public function getCostsFuel(): ?int
    {
        return $this->costsFuel;
    }

    public function setCostsFuel(int $costsFuel): static
    {
        $this->costsFuel = $costsFuel;

        return $this;
    }

    public function getCostsFood(): ?int
    {
        return $this->costsFood;
    }

    public function setCostsFood(int $costsFood): static
    {
        $this->costsFood = $costsFood;

        return $this;
    }

    public function getDamage(): ?int
    {
        return $this->damage;
    }

    public function setDamage(int $damage): static
    {
        $this->damage = $damage;

        return $this;
    }

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): static
    {
        $this->speed = $speed;

        return $this;
    }

    public function getRange(): ?int
    {
        return $this->range;
    }

    public function setRange(int $range): static
    {
        $this->range = $range;

        return $this;
    }

    public function getDeactivate(): ?int
    {
        return $this->deactivate;
    }

    public function setDeactivate(int $deactivate): static
    {
        $this->deactivate = $deactivate;

        return $this;
    }

    public function getDef(): ?int
    {
        return $this->def;
    }

    public function setDef(int $def): static
    {
        $this->def = $def;

        return $this;
    }

    public function getLaunchable(): ?int
    {
        return $this->launchable;
    }

    public function setLaunchable(int $launchable): static
    {
        $this->launchable = $launchable;

        return $this;
    }

    public function isShow(): ?bool
    {
        return $this->show;
    }

    public function setShow(bool $show): static
    {
        $this->show = $show;

        return $this;
    }
}
