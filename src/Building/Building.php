<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\Core\ObjectWithImage;
use EtoA\Universe\Resources\BaseResources;

class Building implements ObjectWithImage
{
    public int $id;
    public string $name;
    public int $typeId;
    public string $shortComment;
    public string $longComment;
    public int $costsMetal;
    public int $costsCrystal;
    public int $costsPlastic;
    public int $costsFuel;
    public int $costsFood;
    public int $costsPower;
    public float $buildCostsFactor;
    public float $demolishCostsFactor;
    public int $powerUse;
    public int $powerRequired;
    public int $fuelUse;
    public int $prodMetal;
    public int $prodCrystal;
    public int $prodPlastic;
    public int $prodFuel;
    public int $prodFood;
    public int $prodPower;
    public float $productionFactor;
    public int $storeMetal;
    public int $storeCrystal;
    public int $storePlastic;
    public int $storeFuel;
    public int $storeFood;
    public float $storeFactor;
    public int $peoplePlace;
    public int $lastLevel;
    public int $fields;
    public bool $show;
    public int $order;
    public int $fieldsProvide;
    public bool $workplace;
    public int $bunkerRes;
    public int $bunkerFleetCount;
    public int $bunkerFleetSpace;

    public function __construct(array $data)
    {
        $this->id = (int) $data['building_id'];
        $this->name = $data['building_name'];
        $this->typeId = (int) $data['building_type_id'];
        $this->shortComment = $data['building_shortcomment'];
        $this->longComment = $data['building_longcomment'];
        $this->costsMetal = (int) $data['building_costs_metal'];
        $this->costsCrystal = (int) $data['building_costs_crystal'];
        $this->costsPlastic = (int) $data['building_costs_plastic'];
        $this->costsFuel = (int) $data['building_costs_fuel'];
        $this->costsFood = (int) $data['building_costs_food'];
        $this->costsPower = (int) $data['building_costs_power'];
        $this->buildCostsFactor = (float) $data['building_build_costs_factor'];
        $this->demolishCostsFactor = (float) $data['building_demolish_costs_factor'];
        $this->powerUse = (int) $data['building_power_use'];
        $this->powerRequired = (int) $data['building_power_req'];
        $this->fuelUse = (int) $data['building_fuel_use'];
        $this->prodMetal = (int) $data['building_prod_metal'];
        $this->prodCrystal = (int) $data['building_prod_crystal'];
        $this->prodPlastic = (int) $data['building_prod_plastic'];
        $this->prodFuel = (int) $data['building_prod_fuel'];
        $this->prodFood = (int) $data['building_prod_food'];
        $this->prodPower = (int) $data['building_prod_power'];
        $this->productionFactor = (float) $data['building_production_factor'];
        $this->storeMetal = (int) $data['building_store_metal'];
        $this->storeCrystal = (int) $data['building_store_crystal'];
        $this->storePlastic = (int) $data['building_store_plastic'];
        $this->storeFuel = (int) $data['building_store_fuel'];
        $this->storeFood = (int) $data['building_store_food'];
        $this->storeFactor = (float) $data['building_store_factor'];
        $this->peoplePlace = (int) $data['building_people_place'];
        $this->lastLevel = (int) $data['building_last_level'];
        $this->fields = (int) $data['building_fields'];
        $this->show = (bool) $data['building_show'];
        $this->order = (int) $data['building_order'];
        $this->fieldsProvide = (int) $data['building_fieldsprovide'];
        $this->workplace = (bool) $data['building_workplace'];
        $this->bunkerRes = (int) $data['building_bunker_res'];
        $this->bunkerFleetCount = (int) $data['building_bunker_fleet_count'];
        $this->bunkerFleetSpace = (int) $data['building_bunker_fleet_space'];
    }

    public function calculateBunkerResources(int $level): int
    {
        return $this->bunkerRes * (int) $this->storeFactor ** ($level - 1);
    }

    public function calculateBunkerFleetSpace(int $level): int
    {
        return $this->bunkerFleetSpace * (int) $this->storeFactor ** ($level - 1);
    }

    public function calculateBunkerFleetCount(int $level): int
    {
        return $this->bunkerFleetCount * (int) $this->storeFactor ** ($level - 1);
    }

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH."/buildings/building".$this->id."_small.png";
            case 'medium':
                return self::BASE_PATH."/buildings/building".$this->id."_middle.png";
            default:
                return self::BASE_PATH."/buildings/building".$this->id.".png";
        }
    }

    public function getCosts(): BaseResources
    {
        $costs = new BaseResources();
        $costs->metal = $this->costsMetal;
        $costs->crystal = $this->costsCrystal;
        $costs->plastic = $this->costsPlastic;
        $costs->fuel = $this->costsFuel;
        $costs->food = $this->costsFood;

        return $costs;
    }
}
