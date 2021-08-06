<?php declare(strict_types=1);

namespace EtoA\User;

class UserBattleRating
{
    public int $userId;
    public string $userNick;
    public string $raceName;
    public ?string $allianceTag;
    public int $rating;
    public int $battlesWon;
    public int $battlesLost;
    public int $battlesFought;

    public function __construct(array $data)
    {
        $this->userId = (int) $data['user_id'];
        $this->userNick = $data['user_nick'];
        $this->raceName = $data['race_name'];
        $this->allianceTag = $data['alliance_tag'];
        $this->rating = (int) $data['battle_rating'];
        $this->battlesWon = (int) $data['battles_won'];
        $this->battlesLost = (int) $data['battles_lost'];
        $this->battlesFought = (int) $data['battles_fought'];
    }
}
