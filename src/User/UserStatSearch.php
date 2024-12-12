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
        return new UserStatSearch('q.shipPoints', 'q.rankShips', 'q.shiftShips');
    }

    public static function technologies(): UserStatSearch
    {
        return new UserStatSearch('q.techPoints', 'q.rankTech', 'q.shiftTechs');
    }

    public static function buildings(): UserStatSearch
    {
        return new UserStatSearch('q.buildingPoints', 'q.rankBuildings', 'q.shiftBuildings');
    }

    public static function exp(): UserStatSearch
    {
        return new UserStatSearch('q.expPoints', 'q.rankExp', 'q.shiftExp');
    }

    public static function points(): UserStatSearch
    {
        return new UserStatSearch('q.points', 'q.rank', 'q.shift');
    }

    public function nick(string $userNick): self
    {
        $this->parts[] = 'nick LIKE :nick';
        $this->parameters['nick'] = $userNick . '%';

        return $this;
    }

    public function allianceId(int $allianceId): self
    {
        $this->parts[] = 'users.alliance = :allianceId';
        $this->parameters['allianceId'] = $allianceId;

        return $this;
    }
}
