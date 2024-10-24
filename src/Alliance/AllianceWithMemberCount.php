<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\Entity\Alliance;

class AllianceWithMemberCount extends Alliance
{
    public int $memberCount;
    public int $averagePoints = 0;

    public function __construct(array $data)
    {
        $this->memberCount = (int) $data['member_count'];
        if ($this->memberCount > 0) {
            $this->averagePoints = (int) floor($this->getPoints() / $this->memberCount);
        }
    }
}
