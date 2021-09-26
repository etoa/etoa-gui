<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveOldReportsTask implements PeriodicTaskInterface
{
    public ?int $threshold;
    public bool $onlyDeleted;

    public function __construct(int $threshold = null, bool $onlyDeleted = false)
    {
        $this->threshold = $threshold;
        $this->onlyDeleted = $onlyDeleted;
    }

    public function getDescription(): string
    {
        return "Alte Berichte löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
