<?php declare(strict_types=1);

namespace EtoA\Alliance;

class AllianceWithMemberCount extends Alliance
{
    public int $memberCount;
    public int $averagePoints;

    public function __construct(array $data)
    {
        parent::__construct($data);

        $this->memberCount = (int) $data['member_count'];
        $this->averagePoints = (int) floor($this->points / $this->memberCount);
    }
}
