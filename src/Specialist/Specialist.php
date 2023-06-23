<?php declare(strict_types=1);

namespace EtoA\Specialist;

class Specialist
{
    public int $id;
    public string $name;
    public string $description;
    public bool $enabled;
    public int $pointsRequirement;
    public int $costsMetal;
    public int $costsCrystal;
    public int $costsPlastic;
    public int $costsFuel;
    public int $costsFood;
    public int $days;
    public float $prodMetal;
    public float $prodCrystal;
    public float $prodPlastic;
    public float $prodFuel;
    public float $prodFood;
    public float $prodPower;
    public float $prodPeople;
    public float $timeTechnologies;
    public float $timeBuildings;
    public float $timeDefense;
    public float $timeShips;
    public float $costsBuildings;
    public float $costsDefense;
    public float $costsShips;
    public float $costsTechnologies;
    public float $fleetSpeed;
    public int $fleetMax;
    public float $defenseRepair;
    public int $spyLevel;
    public int $tarnLevel;
    public float $tradeTime;
    public float $tradeBonus;

    public function __construct(array $data)
    {
        $this->id = (int) $data['specialist_id'];
        $this->name = $data['specialist_name'];
        $this->description = $data['specialist_desc'];
        $this->enabled = (bool) $data['specialist_enabled'];
        $this->pointsRequirement = (int) $data['specialist_points_req'];
        $this->costsMetal = (int) $data['specialist_costs_metal'];
        $this->costsCrystal = (int) $data['specialist_costs_crystal'];
        $this->costsPlastic = (int) $data['specialist_costs_plastic'];
        $this->costsFuel = (int) $data['specialist_costs_fuel'];
        $this->costsFood = (int) $data['specialist_costs_food'];
        $this->days = (int) $data['specialist_days'];
        $this->prodMetal = (float) $data['specialist_prod_metal'];
        $this->prodCrystal = (float) $data['specialist_prod_crystal'];
        $this->prodPlastic = (float) $data['specialist_prod_plastic'];
        $this->prodFuel = (float) $data['specialist_prod_fuel'];
        $this->prodFood = (float) $data['specialist_prod_food'];
        $this->prodPower = (float) $data['specialist_power'];
        $this->prodPeople = (float) $data['specialist_population'];
        $this->timeTechnologies = (float) $data['specialist_time_tech'];
        $this->timeBuildings = (float) $data['specialist_time_buildings'];
        $this->timeDefense = (float) $data['specialist_time_defense'];
        $this->timeShips = (float) $data['specialist_time_ships'];
        $this->costsBuildings = (float) $data['specialist_costs_buildings'];
        $this->costsDefense = (float) $data['specialist_costs_defense'];
        $this->costsShips = (float) $data['specialist_costs_ships'];
        $this->costsTechnologies = (float) $data['specialist_costs_tech'];
        $this->fleetSpeed = (float) $data['specialist_fleet_speed'];
        $this->fleetMax = (int) $data['specialist_fleet_max'];
        $this->defenseRepair = (float) $data['specialist_def_repair'];
        $this->spyLevel = (int) $data['specialist_spy_level'];
        $this->tarnLevel = (int) $data['specialist_tarn_level'];
        $this->tradeTime = (float) $data['specialist_trade_time'];
        $this->tradeBonus = (float) $data['specialist_trade_bonus'];
    }
}
