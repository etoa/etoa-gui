<?php declare(strict_types=1);

namespace EtoA\Market;

class MarketRate
{
    public ?int $id = null;
    public int $timestamp = 0;
    public int $supply0 = 0;
    public int $supply1 = 0;
    public int $supply2 = 0;
    public int $supply3 = 0;
    public int $supply4 = 0;
    public int $supply5 = 0;
    public int $demand0 = 0;
    public int $demand1 = 0;
    public int $demand2 = 0;
    public int $demand3 = 0;
    public int $demand4 = 0;
    public int $demand5 = 0;
    public float $rate0 = 1;
    public float $rate1 = 1;
    public float $rate2 = 1;
    public float $rate3 = 1;
    public float $rate4 = 1;
    public float $rate5 = 1;

    public static function createFromArray(array $data): MarketRate
    {
        $rate = new MarketRate();
        $rate->id = (int) $data['id'];
        $rate->timestamp = (int) $data['timestamp'];
        $rate->supply0 = (int) $data['supply_0'];
        $rate->supply1 = (int) $data['supply_1'];
        $rate->supply2 = (int) $data['supply_2'];
        $rate->supply3 = (int) $data['supply_3'];
        $rate->supply4 = (int) $data['supply_4'];
        $rate->supply5 = (int) $data['supply_5'];
        $rate->demand0 = (int) $data['demand_0'];
        $rate->demand1 = (int) $data['demand_1'];
        $rate->demand2 = (int) $data['demand_2'];
        $rate->demand3 = (int) $data['demand_3'];
        $rate->demand4 = (int) $data['demand_4'];
        $rate->demand5 = (int) $data['demand_5'];
        $rate->rate0 = (float) $data['rate_0'];
        $rate->rate1 = (float) $data['rate_1'];
        $rate->rate2 = (float) $data['rate_2'];
        $rate->rate3 = (float) $data['rate_3'];
        $rate->rate4 = (float) $data['rate_4'];
        $rate->rate5 = (float) $data['rate_5'];

        return $rate;
    }
}
