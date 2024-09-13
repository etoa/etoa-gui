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

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }

    public function isMainPlanet(): bool
    {
        return $this->mainPlanet;
    }

    public function setMainPlanet(bool $mainPlanet): void
    {
        $this->mainPlanet = $mainPlanet;
    }

    public function getUserChanged(): int
    {
        return $this->userChanged;
    }

    public function setUserChanged(int $userChanged): void
    {
        $this->userChanged = $userChanged;
    }

    public function getLastUserId(): int
    {
        return $this->lastUserId;
    }

    public function setLastUserId(int $lastUserId): void
    {
        $this->lastUserId = $lastUserId;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getTypeId(): int
    {
        return $this->typeId;
    }

    public function setTypeId(int $typeId): void
    {
        $this->typeId = $typeId;
    }

    public function getFields(): int
    {
        return $this->fields;
    }

    public function setFields(int $fields): void
    {
        $this->fields = $fields;
    }

    public function getFieldsExtra(): int
    {
        return $this->fieldsExtra;
    }

    public function setFieldsExtra(int $fieldsExtra): void
    {
        $this->fieldsExtra = $fieldsExtra;
    }

    public function getFieldsUsed(): int
    {
        return $this->fieldsUsed;
    }

    public function setFieldsUsed(int $fieldsUsed): void
    {
        $this->fieldsUsed = $fieldsUsed;
    }

    public function getImage(): string
    {
        return $this->image;
    }

    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    public function getTempFrom(): int
    {
        return $this->tempFrom;
    }

    public function setTempFrom(int $tempFrom): void
    {
        $this->tempFrom = $tempFrom;
    }

    public function getTempTo(): int
    {
        return $this->tempTo;
    }

    public function setTempTo(int $tempTo): void
    {
        $this->tempTo = $tempTo;
    }

    public function getSemiMajorAxis(): float
    {
        return $this->semiMajorAxis;
    }

    public function setSemiMajorAxis(float $semiMajorAxis): void
    {
        $this->semiMajorAxis = $semiMajorAxis;
    }

    public function getEcccentricity(): float
    {
        return $this->ecccentricity;
    }

    public function setEcccentricity(float $ecccentricity): void
    {
        $this->ecccentricity = $ecccentricity;
    }

    public function getMass(): int
    {
        return $this->mass;
    }

    public function setMass(int $mass): void
    {
        $this->mass = $mass;
    }

    public function getResMetal(): float
    {
        return $this->resMetal;
    }

    public function setResMetal(float $resMetal): void
    {
        $this->resMetal = $resMetal;
    }

    public function getResCrystal(): float
    {
        return $this->resCrystal;
    }

    public function setResCrystal(float $resCrystal): void
    {
        $this->resCrystal = $resCrystal;
    }

    public function getResPlastic(): float
    {
        return $this->resPlastic;
    }

    public function setResPlastic(float $resPlastic): void
    {
        $this->resPlastic = $resPlastic;
    }

    public function getResFuel(): float
    {
        return $this->resFuel;
    }

    public function setResFuel(float $resFuel): void
    {
        $this->resFuel = $resFuel;
    }

    public function getResFood(): float
    {
        return $this->resFood;
    }

    public function setResFood(float $resFood): void
    {
        $this->resFood = $resFood;
    }

    public function getUsePower(): int
    {
        return $this->usePower;
    }

    public function setUsePower(int $usePower): void
    {
        $this->usePower = $usePower;
    }

    public function getLastUpdated(): int
    {
        return $this->lastUpdated;
    }

    public function setLastUpdated(int $lastUpdated): void
    {
        $this->lastUpdated = $lastUpdated;
    }

    public function getBunkerMetal(): int
    {
        return $this->bunkerMetal;
    }

    public function setBunkerMetal(int $bunkerMetal): void
    {
        $this->bunkerMetal = $bunkerMetal;
    }

    public function getBunkerCrystal(): int
    {
        return $this->bunkerCrystal;
    }

    public function setBunkerCrystal(int $bunkerCrystal): void
    {
        $this->bunkerCrystal = $bunkerCrystal;
    }

    public function getBunkerPlastic(): int
    {
        return $this->bunkerPlastic;
    }

    public function setBunkerPlastic(int $bunkerPlastic): void
    {
        $this->bunkerPlastic = $bunkerPlastic;
    }

    public function getBunkerFuel(): int
    {
        return $this->bunkerFuel;
    }

    public function setBunkerFuel(int $bunkerFuel): void
    {
        $this->bunkerFuel = $bunkerFuel;
    }

    public function getBunkerFood(): int
    {
        return $this->bunkerFood;
    }

    public function setBunkerFood(int $bunkerFood): void
    {
        $this->bunkerFood = $bunkerFood;
    }

    public function getProdMetal(): int
    {
        return $this->prodMetal;
    }

    public function setProdMetal(int $prodMetal): void
    {
        $this->prodMetal = $prodMetal;
    }

    public function getProdCrystal(): int
    {
        return $this->prodCrystal;
    }

    public function setProdCrystal(int $prodCrystal): void
    {
        $this->prodCrystal = $prodCrystal;
    }

    public function getProdPlastic(): int
    {
        return $this->prodPlastic;
    }

    public function setProdPlastic(int $prodPlastic): void
    {
        $this->prodPlastic = $prodPlastic;
    }

    public function getProdFuel(): int
    {
        return $this->prodFuel;
    }

    public function setProdFuel(int $prodFuel): void
    {
        $this->prodFuel = $prodFuel;
    }

    public function getProdFood(): int
    {
        return $this->prodFood;
    }

    public function setProdFood(int $prodFood): void
    {
        $this->prodFood = $prodFood;
    }

    public function getProdPower(): int
    {
        return $this->prodPower;
    }

    public function setProdPower(int $prodPower): void
    {
        $this->prodPower = $prodPower;
    }

    public function getProdPeople(): int
    {
        return $this->prodPeople;
    }

    public function setProdPeople(int $prodPeople): void
    {
        $this->prodPeople = $prodPeople;
    }

    public function getStoreMetal(): int
    {
        return $this->storeMetal;
    }

    public function setStoreMetal(int $storeMetal): void
    {
        $this->storeMetal = $storeMetal;
    }

    public function getStoreCrystal(): int
    {
        return $this->storeCrystal;
    }

    public function setStoreCrystal(int $storeCrystal): void
    {
        $this->storeCrystal = $storeCrystal;
    }

    public function getStorePlastic(): int
    {
        return $this->storePlastic;
    }

    public function setStorePlastic(int $storePlastic): void
    {
        $this->storePlastic = $storePlastic;
    }

    public function getStoreFuel(): int
    {
        return $this->storeFuel;
    }

    public function setStoreFuel(int $storeFuel): void
    {
        $this->storeFuel = $storeFuel;
    }

    public function getStoreFood(): int
    {
        return $this->storeFood;
    }

    public function setStoreFood(int $storeFood): void
    {
        $this->storeFood = $storeFood;
    }

    public function getWfMetal(): int
    {
        return $this->wfMetal;
    }

    public function setWfMetal(int $wfMetal): void
    {
        $this->wfMetal = $wfMetal;
    }

    public function getWfCrystal(): int
    {
        return $this->wfCrystal;
    }

    public function setWfCrystal(int $wfCrystal): void
    {
        $this->wfCrystal = $wfCrystal;
    }

    public function getWfPlastic(): int
    {
        return $this->wfPlastic;
    }

    public function setWfPlastic(int $wfPlastic): void
    {
        $this->wfPlastic = $wfPlastic;
    }

    public function getPeople(): float
    {
        return $this->people;
    }

    public function setPeople(float $people): void
    {
        $this->people = $people;
    }

    public function getPeoplePlace(): int
    {
        return $this->peoplePlace;
    }

    public function setPeoplePlace(int $peoplePlace): void
    {
        $this->peoplePlace = $peoplePlace;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getInvadedBy(): int
    {
        return $this->invadedBy;
    }

    public function setInvadedBy(int $invadedBy): void
    {
        $this->invadedBy = $invadedBy;
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
