<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class SessionCleanupTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Session Cleanup";
    }

    public function getSchedule(): string
    {
        return "*/5 * * * *";
    }
}
