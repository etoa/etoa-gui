<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class GenerateUserStatisticsTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "User Statistik aktualisieren";
    }

    public function getSchedule(): string
    {
        return "*/5 * * * *";
    }
}
