<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveOldUserPointLogsTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Alte Benutzerpunkte-Logs löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
