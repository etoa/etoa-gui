<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CleanupSessionLogsTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Alte Session-Logs löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
