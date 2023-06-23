<?php

declare(strict_types=1);

namespace EtoA\Fleet;

class Fleet
{
    public int $id;
    public int $userId;
    public int $leaderId;
    public int $entityFrom;
    public int $entityTo;
    public int $nextId;
    public int $launchTime;
    public int $landTime;
    public int $nextActionTime;
    public string $action;
    public int $status;
    public int $pilots;
    public int $usageFuel;
    public int $usageFood;
    public int $usagePower;
    public int $supportUsageFuel;
    public int $supportUsageFood;
    public int $resMetal;
    public int $resCrystal;
    public int $resPlastic;
    public int $resFuel;
    public int $resFood;
    public int $resPower;
    public int $resPeople;
    public int $fetchMetal;
    public int $fetchCrystal;
    public int $fetchPlastic;
    public int $fetchFuel;
    public int $fetchFood;
    public int $fetchPower;
    public int $fetchPeople;
    public int $flag;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->userId = (int) $data['user_id'];
        $this->leaderId = (int) $data['leader_id'];
        $this->entityFrom = (int) $data['entity_from'];
        $this->entityTo = (int) $data['entity_to'];
        $this->nextId = (int) $data['next_id'];
        $this->launchTime = (int) $data['launchtime'];
        $this->landTime = (int) $data['landtime'];
        $this->nextActionTime = (int) $data['nextactiontime'];
        $this->action = $data['action'];
        $this->status = (int) $data['status'];
        $this->pilots = (int) $data['pilots'];
        $this->usageFuel = (int) $data['usage_fuel'];
        $this->usageFood = (int) $data['usage_food'];
        $this->usagePower = (int) $data['usage_power'];
        $this->supportUsageFuel = (int) $data['support_usage_fuel'];
        $this->supportUsageFood = (int) $data['support_usage_food'];
        $this->resMetal = (int) $data['res_metal'];
        $this->resCrystal = (int) $data['res_crystal'];
        $this->resPlastic = (int) $data['res_plastic'];
        $this->resFuel = (int) $data['res_fuel'];
        $this->resFood = (int) $data['res_food'];
        $this->resPower = (int) $data['res_power'];
        $this->resPeople = (int) $data['res_people'];
        $this->fetchMetal = (int) $data['fetch_metal'];
        $this->fetchCrystal = (int) $data['fetch_crystal'];
        $this->fetchPlastic = (int) $data['fetch_plastic'];
        $this->fetchFuel = (int) $data['fetch_fuel'];
        $this->fetchFood = (int) $data['fetch_food'];
        $this->fetchPower = (int) $data['fetch_power'];
        $this->fetchPeople = (int) $data['fetch_people'];
        $this->flag = (int) $data['flag'];
    }

    public function getRemainingTime(): int
    {
        return max(0, $this->landTime - time());
    }

    public function isStatusDeparture(): bool
    {
        return $this->status === FleetStatus::DEPARTURE;
    }

    public static function empty(): Fleet
    {
        return new Fleet([
            'id' => 0,
            'user_id' => 0,
            'leader_id' => 0,
            'entity_from' => 0,
            'entity_to' => 0,
            'next_id' => 0,
            'launchtime' => time(),
            'landtime' => time(),
            'nextactiontime' => 0,
            'action' => '',
            'status' => 0,
            'pilots' => 0,
            'usage_fuel' => 0,
            'usage_food' => 0,
            'usage_power' => 0,
            'support_usage_fuel' => 0,
            'support_usage_food' => 0,
            'res_metal' => 0,
            'res_crystal' => 0,
            'res_plastic' => 0,
            'res_fuel' => 0,
            'res_food' => 0,
            'res_power' => 0,
            'res_people' => 0,
            'fetch_metal' => 0,
            'fetch_crystal' => 0,
            'fetch_plastic' => 0,
            'fetch_fuel' => 0,
            'fetch_food' => 0,
            'fetch_power' => 0,
            'fetch_people' => 0,
            'flag' => 0,
        ]);
    }
}
