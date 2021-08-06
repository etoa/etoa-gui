<?php declare(strict_types=1);

namespace EtoA\User;

class UserTradeRating
{
    public int $userId;
    public string $userNick;
    public string $raceName;
    public ?string $allianceTag;
    public int $rating;
    public int $tradesBuy;
    public int $tradesSell;
    public int $battlesFought;

    public function __construct(array $data)
    {
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->raceName = $data['race_name'];
        $this->allianceTag = $data['alliance_tag'];
        $this->rating = (int) $data['trade_rating'];
        $this->tradesBuy = (int) $data['trades_buy'];
        $this->tradesSell = (int) $data['trades_sell'];
    }
}
