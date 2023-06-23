<?php declare(strict_types=1);

namespace EtoA\User;

class UserTradeRating extends UserRating
{
    public int $tradesBuy;
    public int $tradesSell;
    public int $battlesFought;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->rating = (int) $data['trade_rating'];
        $this->tradesBuy = (int) $data['trades_buy'];
        $this->tradesSell = (int) $data['trades_sell'];
    }
}
