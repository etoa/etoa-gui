<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Specialist\SpecialistDataRepository;

#[ORM\Entity(repositoryClass: SpecialistDataRepository::class)]
class Specialist
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "specialist_id", type: "integer")]
    protected int $id;

    #[ORM\Column(name: "specialist_name", type: "string")]
    protected string $name;

    #[ORM\Column(name: "specialist_desc", type: "string")]
    protected string $description;

    #[ORM\Column(name: "specialist_enabled", type: "bool")]
    protected bool $enabled;

    #[ORM\Column(name: "specialist_points_req", type: "integer")]
    protected int $pointsRequirement;

    #[ORM\Column(name: "specialist_costs_metal", type: "integer")]
    protected int $costsMetal;

    #[ORM\Column(name: "specialist_costs_crystal", type: "integer")]
    protected int $costsCrystal;

    #[ORM\Column(name: "specialist_costs_plastic", type: "integer")]
    protected int $costsPlastic;

    #[ORM\Column(name: "specialist_costs_fuel", type: "integer")]
    protected int $costsFuel;

    #[ORM\Column(name: "specialist_costs_food", type: "integer")]
    protected int $costsFood;

    #[ORM\Column(name: "specialist_days", type: "integer")]
    protected int $days;

    #[ORM\Column(name: "specialist_prod_metal", type: "float")]
    protected float $prodMetal;

    #[ORM\Column(name: "specialist_prod_crystal", type: "float")]
    protected float $prodCrystal;

    #[ORM\Column(name: "specialist_prod_plastic", type: "float")]
    protected float $prodPlastic;

    #[ORM\Column(name: "specialist_prod_fuel", type: "float")]
    protected float $prodFuel;

    #[ORM\Column(name: "specialist_prod_food", type: "float")]
    protected float $prodFood;

    #[ORM\Column(name: "specialist_prod_power", type: "float")]
    protected float $prodPower;

    #[ORM\Column(name: "specialist_prod_population", type: "float")]
    protected float $prodPeople;

    #[ORM\Column(name: "specialist_time_tech", type: "float")]
    protected float $timeTechnologies;

    #[ORM\Column(name: "specialist_time_buildings", type: "float")]
    protected float $timeBuildings;

    #[ORM\Column(name: "specialist_time_defense", type: "float")]
    protected float $timeDefense;

    #[ORM\Column(name: "specialist_time_ships", type: "float")]
    protected float $timeShips;

    #[ORM\Column(name: "specialist_costs_buildings", type: "float")]
    protected float $costsBuildings;

    #[ORM\Column(name: "specialist_costs_defense", type: "float")]
    protected float $costsDefense;

    #[ORM\Column(name: "specialist_costs_ships", type: "float")]
    protected float $costsShips;

    #[ORM\Column(name: "specialist_costs_tech", type: "float")]
    protected float $costsTechnologies;

    #[ORM\Column(name: "specialist_fleet_speed", type: "float")]
    protected float $fleetSpeed;

    #[ORM\Column(name: "specialist_fleet_max", type: "integer")]
    protected int $fleetMax;

    #[ORM\Column(name: "specialist_defense_repair", type: "float")]
    protected float $defenseRepair;

    #[ORM\Column(name: "specialist_spy_level", type: "integer")]
    protected int $spyLevel;

    #[ORM\Column(name: "specialist_tarn_level", type: "integer")]
    protected int $tarnLevel;

    #[ORM\Column(name: "specialist_trade_time", type: "float")]
    protected float $tradeTime;

    #[ORM\Column(name: "specialist_trade_bonus", type: "float")]
    protected float $tradeBonus;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    public function setEnabled($enabled): static
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getPointsRequirement(): ?int
    {
        return $this->pointsRequirement;
    }

    public function setPointsRequirement(int $pointsRequirement): static
    {
        $this->pointsRequirement = $pointsRequirement;

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

    public function getDays(): ?int
    {
        return $this->days;
    }

    public function setDays(int $days): static
    {
        $this->days = $days;

        return $this;
    }

    public function getProdMetal(): ?float
    {
        return $this->prodMetal;
    }

    public function setProdMetal(float $prodMetal): static
    {
        $this->prodMetal = $prodMetal;

        return $this;
    }

    public function getProdCrystal(): ?float
    {
        return $this->prodCrystal;
    }

    public function setProdCrystal(float $prodCrystal): static
    {
        $this->prodCrystal = $prodCrystal;

        return $this;
    }

    public function getProdPlastic(): ?float
    {
        return $this->prodPlastic;
    }

    public function setProdPlastic(float $prodPlastic): static
    {
        $this->prodPlastic = $prodPlastic;

        return $this;
    }

    public function getProdFuel(): ?float
    {
        return $this->prodFuel;
    }

    public function setProdFuel(float $prodFuel): static
    {
        $this->prodFuel = $prodFuel;

        return $this;
    }

    public function getProdFood(): ?float
    {
        return $this->prodFood;
    }

    public function setProdFood(float $prodFood): static
    {
        $this->prodFood = $prodFood;

        return $this;
    }

    public function getProdPower(): ?float
    {
        return $this->prodPower;
    }

    public function setProdPower(float $prodPower): static
    {
        $this->prodPower = $prodPower;

        return $this;
    }

    public function getProdPeople(): ?float
    {
        return $this->prodPeople;
    }

    public function setProdPeople(float $prodPeople): static
    {
        $this->prodPeople = $prodPeople;

        return $this;
    }

    public function getTimeTechnologies(): ?float
    {
        return $this->timeTechnologies;
    }

    public function setTimeTechnologies(float $timeTechnologies): static
    {
        $this->timeTechnologies = $timeTechnologies;

        return $this;
    }

    public function getTimeBuildings(): ?float
    {
        return $this->timeBuildings;
    }

    public function setTimeBuildings(float $timeBuildings): static
    {
        $this->timeBuildings = $timeBuildings;

        return $this;
    }

    public function getTimeDefense(): ?float
    {
        return $this->timeDefense;
    }

    public function setTimeDefense(float $timeDefense): static
    {
        $this->timeDefense = $timeDefense;

        return $this;
    }

    public function getTimeShips(): ?float
    {
        return $this->timeShips;
    }

    public function setTimeShips(float $timeShips): static
    {
        $this->timeShips = $timeShips;

        return $this;
    }

    public function getCostsBuildings(): ?float
    {
        return $this->costsBuildings;
    }

    public function setCostsBuildings(float $costsBuildings): static
    {
        $this->costsBuildings = $costsBuildings;

        return $this;
    }

    public function getCostsDefense(): ?float
    {
        return $this->costsDefense;
    }

    public function setCostsDefense(float $costsDefense): static
    {
        $this->costsDefense = $costsDefense;

        return $this;
    }

    public function getCostsShips(): ?float
    {
        return $this->costsShips;
    }

    public function setCostsShips(float $costsShips): static
    {
        $this->costsShips = $costsShips;

        return $this;
    }

    public function getCostsTechnologies(): ?float
    {
        return $this->costsTechnologies;
    }

    public function setCostsTechnologies(float $costsTechnologies): static
    {
        $this->costsTechnologies = $costsTechnologies;

        return $this;
    }

    public function getFleetSpeed(): ?float
    {
        return $this->fleetSpeed;
    }

    public function setFleetSpeed(float $fleetSpeed): static
    {
        $this->fleetSpeed = $fleetSpeed;

        return $this;
    }

    public function getFleetMax(): ?int
    {
        return $this->fleetMax;
    }

    public function setFleetMax(int $fleetMax): static
    {
        $this->fleetMax = $fleetMax;

        return $this;
    }

    public function getDefenseRepair(): ?float
    {
        return $this->defenseRepair;
    }

    public function setDefenseRepair(float $defenseRepair): static
    {
        $this->defenseRepair = $defenseRepair;

        return $this;
    }

    public function getSpyLevel(): ?int
    {
        return $this->spyLevel;
    }

    public function setSpyLevel(int $spyLevel): static
    {
        $this->spyLevel = $spyLevel;

        return $this;
    }

    public function getTarnLevel(): ?int
    {
        return $this->tarnLevel;
    }

    public function setTarnLevel(int $tarnLevel): static
    {
        $this->tarnLevel = $tarnLevel;

        return $this;
    }

    public function getTradeTime(): ?float
    {
        return $this->tradeTime;
    }

    public function setTradeTime(float $tradeTime): static
    {
        $this->tradeTime = $tradeTime;

        return $this;
    }

    public function getTradeBonus(): ?float
    {
        return $this->tradeBonus;
    }

    public function setTradeBonus(float $tradeBonus): static
    {
        $this->tradeBonus = $tradeBonus;

        return $this;
    }
}
