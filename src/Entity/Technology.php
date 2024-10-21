<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Core\ObjectWithImage;
use EtoA\Technology\TechnologyDataRepository;

#[ORM\Entity(repositoryClass: TechnologyDataRepository::class)]
#[ORM\Table(name: 'technologies')]
class Technology implements ObjectWithImage
{

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "tech_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "tech_name", type: "string")]
    private string $name;

    #[ORM\Column(name: "user_last_login", type: "integer")]
    private int $typeId;

    #[ORM\Column(name: "tech_shortcomment", type: "string")]
    private string $shortComment;

    #[ORM\Column(name: "tech_longcomment", type: "string")]
    private string $longComment;

    #[ORM\Column(name: "tech_costs_metal", type: "integer")]
    private int $costsMetal;

    #[ORM\Column(name: "tech_costs_crystal", type: "integer")]
    private int $costsCrystal;

    #[ORM\Column(name: "tech_costs_plastic", type: "integer")]
    private int $costsPlastic;

    #[ORM\Column(name: "tech_costs_fuel", type: "integer")]
    private int $costsFuel;

    #[ORM\Column(name: "tech_costs_food", type: "integer")]
    private int $costsFood;

    #[ORM\Column(name: "tech_costs_power", type: "integer")]
    private int $costsPower;

    #[ORM\Column(name: "tech_build_costs_factor", type: "float")]
    private float $buildCostsFactor;

    #[ORM\Column(name: "tech_last_level", type: "integer")]
    private int $lastLevel;

    #[ORM\Column(name: "tech_show", type: "boolean")]
    private bool $show;

    #[ORM\Column(name: "tech_order", type: "integer")]
    private int $order;

    #[ORM\Column(name: "tech_stealable", type: "boolean")]
    private bool $stealable;

    public function getImagePath(string $type = 'small'): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH . "/technologies/technology".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH . "/technologies/technology".$this->id."_middle.png";
            default:
                return self::BASE_PATH . "/technologies/technology".$this->id.".png";
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

    public function getTypeId(): ?int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): static
    {
        $this->typeId = $typeId;

        return $this;
    }

    public function getShortComment(): ?string
    {
        return $this->shortComment;
    }

    public function setShortComment(string $shortComment): static
    {
        $this->shortComment = $shortComment;

        return $this;
    }

    public function getLongComment(): ?string
    {
        return $this->longComment;
    }

    public function setLongComment(string $longComment): static
    {
        $this->longComment = $longComment;

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

    public function getCostsPower(): ?int
    {
        return $this->costsPower;
    }

    public function setCostsPower(int $costsPower): static
    {
        $this->costsPower = $costsPower;

        return $this;
    }

    public function getBuildCostsFactor(): ?float
    {
        return $this->buildCostsFactor;
    }

    public function setBuildCostsFactor(float $buildCostsFactor): static
    {
        $this->buildCostsFactor = $buildCostsFactor;

        return $this;
    }

    public function getLastLevel(): ?int
    {
        return $this->lastLevel;
    }

    public function setLastLevel(int $lastLevel): static
    {
        $this->lastLevel = $lastLevel;

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

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder(int $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function isStealable(): ?bool
    {
        return $this->stealable;
    }

    public function setStealable(bool $stealable): static
    {
        $this->stealable = $stealable;

        return $this;
    }
}
