<?php declare(strict_types=1);

namespace EtoA\User;

class UserBattleRating extends UserRating
{
    public int $battlesWon;
    public int $battlesLost;
    public int $battlesFought;
    public int $eloRating;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->rating = (int) $data['battle_rating'];
        $this->battlesWon = (int) $data['battles_won'];
        $this->battlesLost = (int) $data['battles_lost'];
        $this->battlesFought = (int) $data['battles_fought'];
        $this->eloRating = (int) $data['elorating'];
    }
}
