<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class BackendCheckTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Backend-Check";
    }

    public function getSchedule(): string
    {
        return "*/5 * * * *";
    }
}
