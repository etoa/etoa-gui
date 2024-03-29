<?php

declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\Core\ObjectWithImage;

class Planet implements ObjectWithImage
{
    public const COLONY_DELETE_THRESHOLD = 24 * 3600 * 5;

    public int $id;
    public int $userId;
    public bool $mainPlanet;
    public int $userChanged;
    public int $lastUserId;
    public ?string $name;
    public int $typeId;
    public int $fields;
    public int $fieldsExtra;
    public int $fieldsUsed;
    public string $image;
    public int $tempFrom;
    public int $tempTo;
    public float $semiMajorAxis;
    public float $ecccentricity;
    public int $mass;
    public float $resMetal;
    public float $resCrystal;
    public float $resPlastic;
    public float $resFuel;
    public float $resFood;
    public int $usePower;
    public int $lastUpdated;
    public int $bunkerMetal;
    public int $bunkerCrystal;
    public int $bunkerPlastic;
    public int $bunkerFuel;
    public int $bunkerFood;
    public int $prodMetal;
    public int $prodCrystal;
    public int $prodPlastic;
    public int $prodFuel;
    public int $prodFood;
    public int $prodPower;
    public int $prodPeople;
    public int $storeMetal;
    public int $storeCrystal;
    public int $storePlastic;
    public int $storeFuel;
    public int $storeFood;
    public int $wfMetal;
    public int $wfCrystal;
    public int $wfPlastic;
    public float $people;
    public int $peoplePlace;
    public ?string $description;
    public int $invadedBy;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['planet_user_id'];
        $this->mainPlanet = (bool) $data['planet_user_main'];
        $this->userChanged = (int) $data['planet_user_changed'];
        $this->lastUserId = (int) $data['planet_last_user_id'];
        $this->name = $data['planet_name'];
        $this->typeId = (int) $data['planet_type_id'];
        $this->fields = (int) $data['planet_fields'];
        $this->fieldsExtra = (int) $data['planet_fields_extra'];
        $this->fieldsUsed = (int) $data['planet_fields_used'];
        $this->image = $data['planet_image'];
        $this->tempFrom = (int) $data['planet_temp_from'];
        $this->tempTo = (int) $data['planet_temp_to'];
        $this->semiMajorAxis = (float) $data['planet_semi_major_axis'];
        $this->ecccentricity = (float) $data['planet_ecccentricity'];
        $this->mass = (int) $data['planet_mass'];
        $this->resMetal = (float) $data['planet_res_metal'];
        $this->resCrystal = (float) $data['planet_res_crystal'];
        $this->resPlastic = (float) $data['planet_res_plastic'];
        $this->resFuel = (float) $data['planet_res_fuel'];
        $this->resFood = (float) $data['planet_res_food'];
        $this->usePower = (int) $data['planet_use_power'];
        $this->lastUpdated = (int) $data['planet_last_updated'];
        $this->bunkerMetal = (int) $data['planet_bunker_metal'];
        $this->bunkerCrystal = (int) $data['planet_bunker_crystal'];
        $this->bunkerPlastic = (int) $data['planet_bunker_plastic'];
        $this->bunkerFuel = (int) $data['planet_bunker_fuel'];
        $this->bunkerFood = (int) $data['planet_bunker_food'];
        $this->prodMetal = (int) $data['planet_prod_metal'];
        $this->prodCrystal = (int) $data['planet_prod_crystal'];
        $this->prodPlastic = (int) $data['planet_prod_plastic'];
        $this->prodFuel = (int) $data['planet_prod_fuel'];
        $this->prodFood = (int) $data['planet_prod_food'];
        $this->prodPower = (int) $data['planet_prod_power'];
        $this->prodPeople = (int) $data['planet_prod_people'];
        $this->storeMetal = (int) $data['planet_store_metal'];
        $this->storeCrystal = (int) $data['planet_store_crystal'];
        $this->storePlastic = (int) $data['planet_store_plastic'];
        $this->storeFuel = (int) $data['planet_store_fuel'];
        $this->storeFood = (int) $data['planet_store_food'];
        $this->wfMetal = (int) $data['planet_wf_metal'];
        $this->wfCrystal = (int) $data['planet_wf_crystal'];
        $this->wfPlastic = (int) $data['planet_wf_plastic'];
        $this->people = (int) $data['planet_people'];
        $this->peoplePlace = (int) $data['planet_people_place'];
        $this->description = $data['planet_desc'];
        $this->invadedBy = (int) $data['invadedby'];
    }

    public function displayName(): string
    {
        return filled($this->name) ? $this->name : 'Unbenannt';
    }

    public function hasDebrisField(): bool
    {
        return $this->wfMetal + $this->wfCrystal + $this->wfPlastic > 0;
    }

    public function solarPowerBonus(): float
    {
        return self::getSolarPowerBonus($this->tempFrom, $this->tempTo);
    }

    public static function getSolarPowerBonus(int $tempFrom, int $tempTo): float
    {
        $value = floor(($tempFrom + $tempTo) / 4);
        if ($value <= -100) {
            $value = -99;
        }

        return $value;
    }

    public function fuelProductionBonus(): float
    {
        $value = floor(($this->tempFrom + $this->tempTo) / 25);

        return -$value;
    }

    public function getFuelProductionBonusFactor(): float
    {
        $value = floor(($this->tempFrom + $this->tempTo) / 25);

        return $value / 100;
    }

    public function getImagePath(string $type = "small"): string
    {
        switch ($type) {
            case 'small':
                return self::BASE_PATH . "/planets/planet" . $this->image . "_small.png";
            case 'medium':
                return self::BASE_PATH . "/planets/planet" . $this->image . "_middle.png";
            default:
                return self::BASE_PATH . "/planets/planet" . $this->image . ".png";
        }
    }
}
