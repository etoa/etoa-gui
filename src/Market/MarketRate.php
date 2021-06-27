<?php declare(strict_types=1);

namespace EtoA\Market;

class MarketRate
{
    public int $id;
    public int $timestamp;
    public int $supply0;
    public int $supply1;
    public int $supply2;
    public int $supply3;
    public int $supply4;
    public int $supply5;
    public int $demand0;
    public int $demand1;
    public int $demand2;
    public int $demand3;
    public int $demand4;
    public int $demand5;
    public float $rate0;
    public float $rate1;
    public float $rate2;
    public float $rate3;
    public float $rate4;
    public float $rate5;

    public function __construct(array $data)
    {
        $this->id = (int) $data['id'];
        $this->timestamp = (int) $data['timestamp'];
        $this->supply0 = (int) $data['supply_0'];
        $this->supply1 = (int) $data['supply_1'];
        $this->supply2 = (int) $data['supply_2'];
        $this->supply3 = (int) $data['supply_3'];
        $this->supply4 = (int) $data['supply_4'];
        $this->supply5 = (int) $data['supply_5'];
        $this->demand0 = (int) $data['demand_0'];
        $this->demand1 = (int) $data['demand_1'];
        $this->demand2 = (int) $data['demand_2'];
        $this->demand3 = (int) $data['demand_3'];
        $this->demand4 = (int) $data['demand_4'];
        $this->demand5 = (int) $data['demand_5'];
        $this->rate0 = (float) $data['rate_0'];
        $this->rate1 = (float) $data['rate_1'];
        $this->rate2 = (float) $data['rate_2'];
        $this->rate3 = (float) $data['rate_3'];
        $this->rate4 = (float) $data['rate_4'];
        $this->rate5 = (float) $data['rate_5'];
    }
}
