<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Core\Database\AbstractSearch;

class AllianceDiplomacySearch extends AbstractSearch
{
    public static function create(): AllianceDiplomacySearch
    {
        return new AllianceDiplomacySearch();
    }

    public function level(int $level): self
    {
        $this->parts[] = 'q.level = :level';
        $this->parameters['level'] = $level;

        return $this;
    }

    public function dateBefore(int $timestamp): self
    {
        $this->parts[] = 'q.date < :dateBefore';
        $this->parameters['dateBefore'] = $timestamp;

        return $this;
    }

    public function pendingPoints(): self
    {
        $this->parts[] = 'q.points > 0';

        return $this;
    }
}
