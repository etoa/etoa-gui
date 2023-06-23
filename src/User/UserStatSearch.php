<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Database\AbstractSearch;

class UserStatSearch extends AbstractSearch
{
    public string $field;
    public string $order;
    public string $shift;

    private function __construct(string $field, string $order, string $shift)
    {
        $this->field = $field;
        $this->order = $order;
        $this->shift = $shift;
    }

    public static function ships(): UserStatSearch
    {
        return new UserStatSearch('points_ships', 'rank_ships', 'rankshift_ships');
    }

    public static function technologies(): UserStatSearch
    {
        return new UserStatSearch('points_tech', 'rank_tech', 'rankshift_tech');
    }

    public static function buildings(): UserStatSearch
    {
        return new UserStatSearch('points_buildings', 'rank_buildings', 'rankshift_buildings');
    }

    public static function exp(): UserStatSearch
    {
        return new UserStatSearch('points_exp', 'rank_exp', 'rankshift_exp');
    }

    public static function points(): UserStatSearch
    {
        return new UserStatSearch('points', 'rank', 'rankshift');
    }

    public function nick(string $userNick): self
    {
        $this->parts[] = 'nick LIKE :nick';
        $this->parameters['nick'] = $userNick . '%';

        return $this;
    }

    public function allianceId(int $allianceId): self
    {
        $this->parts[] = 'users.user_alliance_id = :allianceId';
        $this->parameters['allianceId'] = $allianceId;

        return $this;
    }
}
