<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSearch;

class DefenseSearch extends AbstractSearch
{
    public static function create(): DefenseSearch
    {
        return new DefenseSearch();
    }

    public function buildable(): self
    {
        $this->parts[] = 'def_buildable = 1';

        return $this;
    }

    public function showOrBuildable(): self
    {
        $this->parts[] = 'def_show = 1 OR def_buildable = 1';

        return $this;
    }

    public function raceId(int $raceId): self
    {
        $this->parts[] = 'def_race_id = :raceId';
        $this->parameters['raceId'] = $raceId;

        return $this;
    }

    public function raceOrNull(int $raceId): self
    {
        $this->parts[] = 'def_race_id = 0 OR def_race_id = :raceIdOrNull';
        $this->parameters['raceIdOrNull'] = $raceId;

        return $this;
    }
}
