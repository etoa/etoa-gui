<?php declare(strict_types=1);

namespace EtoA\Defense;

use EtoA\Core\Database\AbstractSearch;
use EtoA\Entity\Race;

class DefenseSearch extends AbstractSearch
{
    public static function create(): DefenseSearch
    {
        return new DefenseSearch();
    }

    /**
     * @param int[] $ids
     */
    public function ids(array $ids): self
    {
        $this->parts[] = 'def_id IN(:ids)';
        $this->stringArrayParameters['ids'] = $ids;

        return $this;
    }

    public function buildable(): self
    {
        $this->parts[] = 'def_buildable = 1';

        return $this;
    }

    public function show(): self
    {
        $this->parts[] = 'q.show = 1';

        return $this;
    }

    public function showOrBuildable(): self
    {
        $this->parts[] = 'q.show = 1 OR q.buildable = 1';

        return $this;
    }

    public function raceId(Race|int $raceId): self
    {
        $this->parts[] = 'q.race = :raceId';
        $this->parameters['raceId'] = $raceId;

        return $this;
    }

    public function raceOrNull(Race|int $raceId): self
    {
        $this->parts[] = 'q.race IS NULL OR q.race = :raceIdOrNull';
        $this->parameters['raceIdOrNull'] = $raceId;

        return $this;
    }
}
