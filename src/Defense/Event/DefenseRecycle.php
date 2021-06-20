<?php declare(strict_types=1);

namespace EtoA\Defense\Event;

use Symfony\Contracts\EventDispatcher\Event;

class DefenseRecycle extends Event
{
    public const RECYCLE_SUCCESS = 'defense.recycle.success';

    private int $defenseId;
    private int $count;

    public function __construct(int $defenseId, int $count)
    {
        $this->defenseId = $defenseId;
        $this->count = $count;
    }

    public function getCount(): int
    {
        return $this->count;
    }
}
