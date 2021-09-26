<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class WarPeaceUpdateTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Krieg/Frieden Status aktualisieren";
    }

    public function getSchedule(): string
    {
        return "* * * * *";
    }
}
