<?php

declare(strict_types=1);

namespace EtoA\Fleet;

class FleetShip
{
    public int $id;
    public int $fleetId;
    public int $shipId;
    public int $count;
    public int $shipFaked;
    public bool $specialShip;
    public int $specialShipLevel;
    public int $specialShipExperience;
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
        $this->id = (int) $data['fs_id'];
        $this->fleetId = (int) $data['fs_fleet_id'];
        $this->shipId = (int) $data['fs_ship_id'];
        $this->count = (int) $data['fs_ship_cnt'];
        $this->shipFaked = (int) $data['fs_ship_faked'];
        $this->specialShip = (bool) $data['fs_special_ship'];
        $this->specialShipLevel = (int) $data['fs_special_ship_level'];
        $this->specialShipExperience = (int) $data['fs_special_ship_exp'];
        $this->specialShipBonusWeapon = (int) $data['fs_special_ship_bonus_weapon'];
        $this->specialShipBonusStructure = (int) $data['fs_special_ship_bonus_structure'];
        $this->specialShipBonusShield = (int) $data['fs_special_ship_bonus_shield'];
        $this->specialShipBonusHeal = (int) $data['fs_special_ship_bonus_heal'];
        $this->specialShipBonusCapacity = (int) $data['fs_special_ship_bonus_capacity'];
        $this->specialShipBonusSpeed = (int) $data['fs_special_ship_bonus_speed'];
        $this->specialShipBonusPilots = (int) $data['fs_special_ship_bonus_pilots'];
        $this->specialShipBonusTarn = (int) $data['fs_special_ship_bonus_tarn'];
        $this->specialShipBonusAnthrax = (int) $data['fs_special_ship_bonus_antrax'];
        $this->specialShipBonusForSteal = (int) $data['fs_special_ship_bonus_forsteal'];
        $this->specialShipBonusBuildDestroy = (int) $data['fs_special_ship_bonus_build_destroy'];
        $this->specialShipBonusAnthraxFood = (int) $data['fs_special_ship_bonus_antrax_food'];
        $this->specialShipBonusDeactivate = (int) $data['fs_special_ship_bonus_deactivade'];
        $this->specialShipBonusReadiness = (int) $data['fs_special_ship_bonus_readiness'];
    }
}
