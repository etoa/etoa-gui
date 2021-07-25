<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceRank
{
    public int $id;
    public int $level;
    public ?string $name;

    public function __construct(array $data)
    {
        $this->id = (int) $data['rank_id'];
        $this->level = (int) $data['rank_level'];
        $this->name = $data['rank_name'];
    }
}
