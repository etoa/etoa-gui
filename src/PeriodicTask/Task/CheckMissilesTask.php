<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CheckMissilesTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Raketen-Aktionen berechnen";
    }

    public function getSchedule(): string
    {
        return "* * * * *";
    }
}
