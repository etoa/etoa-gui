<?php declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Building\BuildingRepository;
use EtoA\Core\ObjectWithImage;
use EtoA\Universe\Resources\BaseResources;

#[ORM\Entity(repositoryClass: BuildingRepository::class)]
#[ORM\Table(name: 'buildings')]
class Building implements ObjectWithImage
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "building_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "building_name")]
    private string $name;

    #[ORM\Column(name: "building_type_id", type: "integer")]
    private int $typeId;

    #[ORM\Column(name: "building_short_comment")]
    private string $shortComment;

    #[ORM\Column(name: "building_long_comment")]
    private string $longComment;

    #[ORM\Column(name: "building_costs_metal", type: "integer")]
    private int $costsMetal;

    #[ORM\Column(name: "building_costs_crystal", type: "integer")]
    private int $costsCrystal;

    #[ORM\Column(name: "building_costs_plastic", type: "integer")]
    private int $costsPlastic;

    #[ORM\Column(name: "building_costs_fuel", type: "integer")]
    private int $costsFuel;

    #[ORM\Column(name: "building_costs_food", type: "integer")]
    private int $costsFood;

    #[ORM\Column(name: "building_costs_power", type: "integer")]
    private int $costsPower;

    #[ORM\Column(name: "building_build_costs_factor", type: "float")]
    private float $buildCostsFactor;

    #[ORM\Column(name: "building_demolish_costs_factor", type: "float")]
    private float $demolishCostsFactor;

    #[ORM\Column(name: "building_power_use", type: "integer")]
    private int $powerUse;

    #[ORM\Column(name: "building_power_required", type: "integer")]
    private int $powerRequired;

    #[ORM\Column(name: "building_fuel_use", type: "integer")]
    private int $fuelUse;

    #[ORM\Column(name: "building_prod_metal", type: "integer")]
    private int $prodMetal;

    #[ORM\Column(name: "building_prod_crystal", type: "integer")]
    private int $prodCrystal;

    #[ORM\Column(name: "building_prod_plastic", type: "integer")]
    private int $prodPlastic;

    #[ORM\Column(name: "building_prod_fuel", type: "integer")]
    private int $prodFuel;

    #[ORM\Column(name: "building_prod_food", type: "integer")]
    private int $prodFood;

    #[ORM\Column(name: "building_prod_power", type: "integer")]
    private int $prodPower;
    private float $productionFactor;

    #[ORM\Column(name: "building_store_metal", type: "integer")]
    private int $storeMetal;

    #[ORM\Column(name: "building_store_crystal", type: "integer")]
    private int $storeCrystal;

    #[ORM\Column(name: "building_store_plastic", type: "integer")]
    private int $storePlastic;

    #[ORM\Column(name: "building_store_fuel", type: "integer")]
    private int $storeFuel;

    #[ORM\Column(name: "building_store_food", type: "integer")]
    private int $storeFood;

    #[ORM\Column(name: "building_store_factor", type: "float")]
    private float $storeFactor;

    #[ORM\Column(name: "building_people_place", type: "integer")]
    private int $peoplePlace;

    #[ORM\Column(name: "building_last_level", type: "integer")]
    private int $lastLevel;

    #[ORM\Column(name: "building_fields", type: "integer")]
    private int $fields;

    #[ORM\Column(name: "building_shiw", type: "boolean")]
    private bool $show;

    #[ORM\Column(name: "building_order", type: "integer")]
    private int $order;

    #[ORM\Column(name: "building_fields_provide", type: "integer")]
    private int $fieldsProvide;

    #[ORM\Column(name: "building_workplace", type: "boolean")]
    private bool $workplace;

    #[ORM\Column(name: "building_bunker_res", type: "integer")]
    private int $bunkerRes;

    #[ORM\Column(name: "building_bunker_fleet_count", type: "integer")]
    private int $bunkerFleetCount;

    #[ORM\Column(name: "building_bunker_fleet_space", type: "integer")]
    private int $bunkerFleetSpace;

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

    public function getDemolishCostsFactor(): ?float
    {
        return $this->demolishCostsFactor;
    }

    public function setDemolishCostsFactor(float $demolishCostsFactor): static
    {
        $this->demolishCostsFactor = $demolishCostsFactor;

        return $this;
    }

    public function getPowerUse(): ?int
    {
        return $this->powerUse;
    }

    public function setPowerUse(int $powerUse): static
    {
        $this->powerUse = $powerUse;

        return $this;
    }

    public function getPowerRequired(): ?int
    {
        return $this->powerRequired;
    }

    public function setPowerRequired(int $powerRequired): static
    {
        $this->powerRequired = $powerRequired;

        return $this;
    }

    public function getFuelUse(): ?int
    {
        return $this->fuelUse;
    }

    public function setFuelUse(int $fuelUse): static
    {
        $this->fuelUse = $fuelUse;

        return $this;
    }

    public function getProdMetal(): ?int
    {
        return $this->prodMetal;
    }

    public function setProdMetal(int $prodMetal): static
    {
        $this->prodMetal = $prodMetal;

        return $this;
    }

    public function getProdCrystal(): ?int
    {
        return $this->prodCrystal;
    }

    public function setProdCrystal(int $prodCrystal): static
    {
        $this->prodCrystal = $prodCrystal;

        return $this;
    }

    public function getProdPlastic(): ?int
    {
        return $this->prodPlastic;
    }

    public function setProdPlastic(int $prodPlastic): static
    {
        $this->prodPlastic = $prodPlastic;

        return $this;
    }

    public function getProdFuel(): ?int
    {
        return $this->prodFuel;
    }

    public function setProdFuel(int $prodFuel): static
    {
        $this->prodFuel = $prodFuel;

        return $this;
    }

    public function getProdFood(): ?int
    {
        return $this->prodFood;
    }

    public function setProdFood(int $prodFood): static
    {
        $this->prodFood = $prodFood;

        return $this;
    }

    public function getProdPower(): ?int
    {
        return $this->prodPower;
    }

    public function setProdPower(int $prodPower): static
    {
        $this->prodPower = $prodPower;

        return $this;
    }

    public function getStoreMetal(): ?int
    {
        return $this->storeMetal;
    }

    public function setStoreMetal(int $storeMetal): static
    {
        $this->storeMetal = $storeMetal;

        return $this;
    }

    public function getStoreCrystal(): ?int
    {
        return $this->storeCrystal;
    }

    public function setStoreCrystal(int $storeCrystal): static
    {
        $this->storeCrystal = $storeCrystal;

        return $this;
    }

    public function getStorePlastic(): ?int
    {
        return $this->storePlastic;
    }

    public function setStorePlastic(int $storePlastic): static
    {
        $this->storePlastic = $storePlastic;

        return $this;
    }

    public function getStoreFuel(): ?int
    {
        return $this->storeFuel;
    }

    public function setStoreFuel(int $storeFuel): static
    {
        $this->storeFuel = $storeFuel;

        return $this;
    }

    public function getStoreFood(): ?int
    {
        return $this->storeFood;
    }

    public function setStoreFood(int $storeFood): static
    {
        $this->storeFood = $storeFood;

        return $this;
    }

    public function getStoreFactor(): ?float
    {
        return $this->storeFactor;
    }

    public function setStoreFactor(float $storeFactor): static
    {
        $this->storeFactor = $storeFactor;

        return $this;
    }

    public function getPeoplePlace(): ?int
    {
        return $this->peoplePlace;
    }

    public function setPeoplePlace(int $peoplePlace): static
    {
        $this->peoplePlace = $peoplePlace;

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

    public function getFields(): ?int
    {
        return $this->fields;
    }

    public function setFields(int $fields): static
    {
        $this->fields = $fields;

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

    public function getFieldsProvide(): ?int
    {
        return $this->fieldsProvide;
    }

    public function setFieldsProvide(int $fieldsProvide): static
    {
        $this->fieldsProvide = $fieldsProvide;

        return $this;
    }

    public function isWorkplace(): ?bool
    {
        return $this->workplace;
    }

    public function setWorkplace(bool $workplace): static
    {
        $this->workplace = $workplace;

        return $this;
    }

    public function getBunkerRes(): ?int
    {
        return $this->bunkerRes;
    }

    public function setBunkerRes(int $bunkerRes): static
    {
        $this->bunkerRes = $bunkerRes;

        return $this;
    }

    public function getBunkerFleetCount(): ?int
    {
        return $this->bunkerFleetCount;
    }

    public function setBunkerFleetCount(int $bunkerFleetCount): static
    {
        $this->bunkerFleetCount = $bunkerFleetCount;

        return $this;
    }

    public function getBunkerFleetSpace(): ?int
    {
        return $this->bunkerFleetSpace;
    }

    public function setBunkerFleetSpace(int $bunkerFleetSpace): static
    {
        $this->bunkerFleetSpace = $bunkerFleetSpace;

        return $this;
    }
}
