<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceSpend
{
    public int $id;
    public int $allianceId;
    public int $userId;
    public int $metal;
    public int $crystal;
    public int $plastic;
    public int $fuel;
    public int $food;
    public int $time;

    public function __construct(array $data)
    {
        $this->id = (int) $data['alliance_spend_id'];
        $this->allianceId = (int) $data['alliance_spend_alliance_id'];
        $this->userId = (int) $data['alliance_spend_user_id'];
        $this->metal = (int) $data['alliance_spend_metal'];
        $this->crystal = (int) $data['alliance_spend_crystal'];
        $this->plastic = (int) $data['alliance_spend_plastic'];
        $this->fuel = (int) $data['alliance_spend_fuel'];
        $this->food = (int) $data['alliance_spend_food'];
        $this->time = (int) $data['alliance_spend_time'];
    }
}
