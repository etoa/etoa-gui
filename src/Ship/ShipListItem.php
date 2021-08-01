<?php

declare(strict_types=1);

namespace EtoA\Ship;

class ShipListItem
{
    public int $id;
    public int $userId;
    public int $shipId;
    public int $entityId;
    public int $botId;
    public int $count;
    public int $bunkered;
    public bool $specialShip;
    public int $specialShipLevel;
    public int $specialShipExp;
    public int $specialShipBonusWeapon;
    public int $specialShipBonusStructure;
    public int $specialShipBonusShield;
    public int $specialShipBonusHeal;
    public int $specialShipBonusCapacity;
    public int $specialShipBonusSpeed;
    public int $specialShipBonusPilots;
    public int $specialShipBonusTarn;
    public int $specialShipBonusAnthrax;
    public int $specialShipBonusForSteal;
    public int $specialShipBonusBuildDestroy;
    public int $specialShipBonusAnthraxFood;
    public int $specialShipBonusDeactivate;
    public int $specialShipBonusReadiness;

    public function __construct(array $data)
    {
        $this->id = (int) $data['shiplist_id'];
        $this->userId = (int) $data['shiplist_user_id'];
        $this->shipId = (int) $data['shiplist_ship_id'];
        $this->entityId = (int) $data['shiplist_entity_id'];
        $this->botId = (int) $data['shiplist_bot_id'];
        $this->count = (int) $data['shiplist_count'];
        $this->bunkered = (int) $data['shiplist_bunkered'];
        $this->specialShip = (bool) $data['shiplist_special_ship'];
        $this->specialShipLevel = (int) $data['shiplist_special_ship_level'];
        $this->specialShipExp = (int) $data['shiplist_special_ship_exp'];
        $this->specialShipBonusWeapon = (int) $data['shiplist_special_ship_bonus_weapon'];
        $this->specialShipBonusStructure = (int) $data['shiplist_special_ship_bonus_structure'];
        $this->specialShipBonusShield = (int) $data['shiplist_special_ship_bonus_shield'];
        $this->specialShipBonusHeal = (int) $data['shiplist_special_ship_bonus_heal'];
        $this->specialShipBonusCapacity = (int) $data['shiplist_special_ship_bonus_capacity'];
        $this->specialShipBonusSpeed = (int) $data['shiplist_special_ship_bonus_speed'];
        $this->specialShipBonusPilots = (int) $data['shiplist_special_ship_bonus_pilots'];
        $this->specialShipBonusTarn = (int) $data['shiplist_special_ship_bonus_tarn'];
        $this->specialShipBonusAnthrax = (int) $data['shiplist_special_ship_bonus_antrax'];
        $this->specialShipBonusForSteal = (int) $data['shiplist_special_ship_bonus_forsteal'];
        $this->specialShipBonusBuildDestroy = (int) $data['shiplist_special_ship_bonus_build_destroy'];
        $this->specialShipBonusAnthraxFood = (int) $data['shiplist_special_ship_bonus_antrax_food'];
        $this->specialShipBonusDeactivate = (int) $data['shiplist_special_ship_bonus_deactivade'];
        $this->specialShipBonusReadiness = (int) $data['shiplist_special_ship_bonus_readiness'];
    }
}
