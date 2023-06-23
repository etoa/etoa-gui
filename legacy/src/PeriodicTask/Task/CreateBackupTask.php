<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CreateBackupTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Backup erstellen";
    }

    public function getSchedule(): string
    {
        return "47 0,6,12,18 * * *";
    }
}
