<?php declare(strict_types=1);

namespace EtoA\Fleet;

class ForeignFleets
{
    /** @var Fleet[] */
    public array $visibleFleets = [];
    public int $userSpyLevel = 0;
    public int $aggressiveCount = 0;

    public function getAttitude(): string
    {
        if ($this->aggressiveCount === 0) {
            return "color:#0f0";
        }

        if ($this->aggressiveCount === count($this->visibleFleets)) {
            return "color:#f00";
        }

        return "color:orange";
    }
}
