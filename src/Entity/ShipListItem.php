<?php

declare(strict_types=1);

namespace EtoA\Entity;

use Doctrine\ORM\Mapping as ORM;
use EtoA\Ship\ShipRepository;

#[ORM\Entity(repositoryClass: ShipRepository::class)]
#[ORM\Table(name: 'shiplist')]
class ShipListItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "AUTO")]
    #[ORM\Column(name: "shiplist_id", type: "integer")]
    private int $id;

    #[ORM\Column(name: "shiplist_user_id", type: "integer")]
    private int $userId;

    #[ORM\Column(name: "shiplist_ship_id", type: "integer")]
    private int $shipId;

    #[ORM\Column(name: "shiplist_entity_id", type: "integer")]
    private int $entityId;

    #[ORM\Column(name: "shiplist_bot_id", type: "integer")]
    private int $botId;

    #[ORM\Column(name: "shiplist_count", type: "integer")]
    private int $count;

    #[ORM\Column(name: "shiplist_bunkered", type: "integer")]
    private int $bunkered;

    #[ORM\Column(name: "shiplist_special_ship", type: "integer")]
    private bool $specialShip;

    #[ORM\Column(name: "shiplist_special_ship_level", type: "integer")]
    private int $specialShipLevel;

    #[ORM\Column(name: "shiplist_special_ship_exp", type: "integer")]
    private int $specialShipExp;

    #[ORM\Column(name: "shiplist_special_ship_bonus_weapon", type: "integer")]
    private int $specialShipBonusWeapon;

    #[ORM\Column(name: "shiplist_special_ship_bonus_structure", type: "integer")]
    private int $specialShipBonusStructure;

    #[ORM\Column(name: "shiplist_special_ship_bonus_shield", type: "integer")]
    private int $specialShipBonusShield;

    #[ORM\Column(name: "shiplist_special_ship_bonus_heal", type: "integer")]
    private int $specialShipBonusHeal;

    #[ORM\Column(name: "shiplist_special_ship_bonus_capacity", type: "integer")]
    private int $specialShipBonusCapacity;

    #[ORM\Column(name: "shiplist_special_ship_bonus_speed", type: "integer")]
    private int $specialShipBonusSpeed;

    #[ORM\Column(name: "shiplist_special_ship_bonus_pilots", type: "integer")]
    private int $specialShipBonusPilots;

    #[ORM\Column(name: "shiplist_special_ship_bonus_tarn", type: "integer")]
    private int $specialShipBonusTarn;

    #[ORM\Column(name: "shiplist_special_ship_bonus_anthrax", type: "integer")]
    private int $specialShipBonusAnthrax;

    #[ORM\Column(name: "shiplist_special_ship_bonus_for_steal", type: "integer")]
    private int $specialShipBonusForSteal;

    #[ORM\Column(name: "shiplist_special_ship_bonus_build_destroy", type: "integer")]
    private int $specialShipBonusBuildDestroy;

    #[ORM\Column(name: "shiplist_special_ship_bonus_anthrax_food", type: "integer")]
    private int $specialShipBonusAnthraxFood;

    #[ORM\Column(name: "shiplist_special_ship_bonus_deactivate", type: "integer")]
    private int $specialShipBonusDeactivate;

    #[ORM\Column(name: "shiplist_special_ship_bonus_readiness", type: "integer")]
    private int $specialShipBonusReadiness;

    public static function createFromData(array $data): ShipListItem
    {
        $item = new ShipListItem();
        $item->id = (int) $data['shiplist_id'];
        $item->userId = (int) $data['shiplist_user_id'];
        $item->shipId = (int) $data['shiplist_ship_id'];
        $item->entityId = (int) $data['shiplist_entity_id'];
        $item->botId = (int) $data['shiplist_bot_id'];
        $item->count = (int) $data['shiplist_count'];
        $item->bunkered = (int) $data['shiplist_bunkered'];
        $item->specialShip = (bool) $data['shiplist_special_ship'];
        $item->specialShipLevel = (int) $data['shiplist_special_ship_level'];
        $item->specialShipExp = (int) $data['shiplist_special_ship_exp'];
        $item->specialShipBonusWeapon = (int) $data['shiplist_special_ship_bonus_weapon'];
        $item->specialShipBonusStructure = (int) $data['shiplist_special_ship_bonus_structure'];
        $item->specialShipBonusShield = (int) $data['shiplist_special_ship_bonus_shield'];
        $item->specialShipBonusHeal = (int) $data['shiplist_special_ship_bonus_heal'];
        $item->specialShipBonusCapacity = (int) $data['shiplist_special_ship_bonus_capacity'];
        $item->specialShipBonusSpeed = (int) $data['shiplist_special_ship_bonus_speed'];
        $item->specialShipBonusPilots = (int) $data['shiplist_special_ship_bonus_pilots'];
        $item->specialShipBonusTarn = (int) $data['shiplist_special_ship_bonus_tarn'];
        $item->specialShipBonusAnthrax = (int) $data['shiplist_special_ship_bonus_antrax'];
        $item->specialShipBonusForSteal = (int) $data['shiplist_special_ship_bonus_forsteal'];
        $item->specialShipBonusBuildDestroy = (int) $data['shiplist_special_ship_bonus_build_destroy'];
        $item->specialShipBonusAnthraxFood = (int) $data['shiplist_special_ship_bonus_antrax_food'];
        $item->specialShipBonusDeactivate = (int) $data['shiplist_special_ship_bonus_deactivade'];
        $item->specialShipBonusReadiness = (int) $data['shiplist_special_ship_bonus_readiness'];

        return $item;
    }

    public static function empty(): ShipListItem
    {
        $item = new ShipListItem();
        $item->id = 0;
        $item->userId = 0;
        $item->shipId = 0;
        $item->entityId = 0;
        $item->botId = 0;
        $item->count = 0;
        $item->bunkered = 0;
        $item->specialShip = false;

        return $item;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getShipId(): ?int
    {
        return $this->shipId;
    }

    public function setShipId(int $shipId): static
    {
        $this->shipId = $shipId;

        return $this;
    }

    public function getEntityId(): ?int
    {
        return $this->entityId;
    }

    public function setEntityId(int $entityId): static
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getBotId(): ?int
    {
        return $this->botId;
    }

    public function setBotId(int $botId): static
    {
        $this->botId = $botId;

        return $this;
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

    public function getBunkered(): ?int
    {
        return $this->bunkered;
    }

    public function setBunkered(int $bunkered): static
    {
        $this->bunkered = $bunkered;

        return $this;
    }

    public function getSpecialShip(): ?int
    {
        return $this->specialShip;
    }

    public function setSpecialShip(int $specialShip): static
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

    public function getSpecialShipExp(): ?int
    {
        return $this->specialShipExp;
    }

    public function setSpecialShipExp(int $specialShipExp): static
    {
        $this->specialShipExp = $specialShipExp;

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
}
