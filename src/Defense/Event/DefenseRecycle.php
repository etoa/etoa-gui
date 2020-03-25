<?php declare(strict_types=1);

namespace EtoA\Defense\Event;

use Symfony\Component\EventDispatcher\Event;

class DefenseRecycle extends Event
{
    public const RECYCLE_SUCCESS = 'defense.recycle.success';

    /** @var int */
    private $defenseId;
    /** @var int */
    private $count;

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
