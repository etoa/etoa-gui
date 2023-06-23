<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveOldLogsTask implements PeriodicTaskInterface
{
    public ?int $threshold;

    public function __construct(int $threshold = null)
    {
        $this->threshold = $threshold;
    }

    public function getDescription(): string
    {
        return "Alte Logs löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
