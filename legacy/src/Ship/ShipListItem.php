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
}
