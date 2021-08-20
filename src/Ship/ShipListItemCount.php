<?php declare(strict_types=1);

namespace EtoA\Ship;

class ShipListItemCount
{
    public int $shipId;
    public int $count;
    public int $bunkered;
    public int $exp;

    public function __construct(array $data)
    {
        $this->shipId = (int) $data['shiplist_ship_id'];
        $this->count = (int) $data['count'];
        $this->bunkered = (int) $data['bunkered'];
        $this->exp = (int) $data['shiplist_special_ship_exp'];
    }

    public function sum(): int
    {
        return $this->count + $this->bunkered;
    }
}
