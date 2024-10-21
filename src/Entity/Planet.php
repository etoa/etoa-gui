<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Core\ObjectWithImage;
use EtoA\Universe\Entity\AbstractEntity;
use EtoA\Universe\Planet\PlanetRepository;

#[ORM\Entity(repositoryClass: PlanetRepository::class)]
#[ORM\Table(name: 'planets')]
class Planet extends AbstractEntity implements ObjectWithImage
{
    public const COLONY_DELETE_THRESHOLD = 24 * 3600 * 5;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[ORM\Column(name: "planet_user_id", type: "integer")]
    private int $userId;

    #[ORM\JoinColumn(name: 'planet_user_id', referencedColumnName: 'user_id')]
    #[ORM\ManyToOne(targetEntity: User::class)]
    private User $user;

    #[ORM\Column(name: "planet_user_main", type: "boolean")]
    private bool $mainPlanet;

    #[ORM\Column(name: "planet_user_changed", type: "integer")]
    private int $userChanged;

    #[ORM\Column(name: "planet_last_user_id", type: "integer")]
    private int $lastUserId;

    #[ORM\Column(name: "planet_name", type: "string")]
    private ?string $name;

    #[ORM\JoinColumn(name: 'planet_type_id', referencedColumnName: 'type_id')]
    #[ORM\ManyToOne(targetEntity: PlanetType::class)]
    private PlanetType $planetType;

    #[ORM\Column(name: "planet_type_id", type: "integer")]
    private int $typeId;

    #[ORM\Column(name: "planet_fields", type: "integer")]
    private int $fields;

    #[ORM\Column(name: "planet_fields_extra", type: "integer")]
    private int $fieldsExtra;

    #[ORM\Column(name: "planet_fields_used", type: "integer")]
    private int $fieldsUsed;

    #[ORM\Column(name: "planet_image", type: "string")]
    private string $image;

    #[ORM\Column(name: "planet_temp_from", type: "integer")]
    private int $tempFrom;

    #[ORM\Column(name: "planet_temp_to", type: "integer")]
    private int $tempTo;

    #[ORM\Column(name: "planet_semi_major_axis", type: "float")]
    private float $semiMajorAxis;

    #[ORM\Column(name: "planet_ecccentricity", type: "float")]
    private float $ecccentricity;

    #[ORM\Column(name: "planet_mass", type: "integer")]
    private int $mass;

    #[ORM\Column(name: "planet_res_metal", type: "float")]
    private float $resMetal;

    #[ORM\Column(name: "planet_res_crystal", type: "float")]
    private float $resCrystal;

    #[ORM\Column(name: "planet_res_plastic", type: "float")]
    private float $resPlastic;

    #[ORM\Column(name: "planet_res_fuel", type: "float")]
    private float $resFuel;

    #[ORM\Column(name: "planet_res_food", type: "float")]
    private float $resFood;

    #[ORM\Column(name: "planet_use_power", type: "integer")]
    private int $usePower;

    #[ORM\Column(name: "planet_last_updated", type: "integer")]
    private int $lastUpdated;

    #[ORM\Column(name: "planet_bunker_metal", type: "integer")]
    private int $bunkerMetal;

    #[ORM\Column(name: "planet_bunker_crystal", type: "integer")]
    private int $bunkerCrystal;

    #[ORM\Column(name: "planet_bunker_plastic", type: "integer")]
    private int $bunkerPlastic;

    #[ORM\Column(name: "planet_bunker_fuel", type: "integer")]
    private int $bunkerFuel;

    #[ORM\Column(name: "planet_bunker_food", type: "integer")]
    private int $bunkerFood;

    #[ORM\Column(name: "planet_prod_metal", type: "integer")]
    private int $prodMetal;

    #[ORM\Column(name: "planet_prod_crystal", type: "integer")]
    private int $prodCrystal;

    #[ORM\Column(name: "planet_prod_plastic", type: "integer")]
    private int $prodPlastic;

    #[ORM\Column(name: "planet_prod_fuel", type: "integer")]
    private int $prodFuel;

    #[ORM\Column(name: "planet_prod_food", type: "integer")]
    private int $prodFood;

    #[ORM\Column(name: "planet_prod_power", type: "integer")]
    private int $prodPower;

    #[ORM\Column(name: "planet_prod_people", type: "integer")]
    private int $prodPeople;

    #[ORM\Column(name: "planet_store_metal", type: "integer")]
    private int $storeMetal;

    #[ORM\Column(name: "planet_store_crystal", type: "integer")]
    private int $storeCrystal;

    #[ORM\Column(name: "planet_store_plastic", type: "integer")]
    private int $storePlastic;

    #[ORM\Column(name: "planet_store_fuel", type: "integer")]
    private int $storeFuel;

    #[ORM\Column(name: "planet_store_food", type: "integer")]
    private int $storeFood;

    #[ORM\Column(name: "planet_wf_metal", type: "integer")]
    private int $wfMetal;

    #[ORM\Column(name: "planet_wf_crystal", type: "integer")]
    private int $wfCrystal;

    #[ORM\Column(name: "planet_wf_plastic", type: "integer")]
    private int $wfPlastic;

    #[ORM\Column(name: "planet_people", type: "float")]
    private float $people;

    #[ORM\Column(name: "planet_people_place", type: "integer")]
    private int $peoplePlace;

    #[ORM\Column(name: "planet_desc", type: "string")]
    private ?string $description;

    #[ORM\Column(name: "invadedby", type: "integer")]
    private int $invadedBy;
    private array $allowedFleetActions;

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

    public function getAllowedFleetActions(): array
    {
        return $this->allowedFleetActions;
    }

    public function setAllowedFleetActions(array $allowedFleetActions): void
    {
        $this->allowedFleetActions = $allowedFleetActions;
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

    public function getEntityCodeString(): string
    {
        return "Planet";
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

    public function getPlanetType(): ?PlanetType
    {
        return $this->planetType;
    }

    public function setPlanetType(?PlanetType $planetType): static
    {
        $this->planetType = $planetType;

        return $this;
    }
}
