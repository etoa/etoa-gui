<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveOldAlliancePointLogsTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Alte Allianzpunkte-Logs löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
