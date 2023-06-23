<?php

declare(strict_types=1);

namespace EtoA\Ranking;

class RankingCalculationResult
{
    public int $numberOfUsers;
    public float $totalPoints;

    public function __construct(int $numberOfUsers, float $totalPoints)
    {
        $this->numberOfUsers = $numberOfUsers;
        $this->totalPoints = $totalPoints;
    }

    public function getAveragePoints(): float
    {
        return $this->numberOfUsers > 0 ? $this->totalPoints / $this->numberOfUsers : 0;
    }
}
