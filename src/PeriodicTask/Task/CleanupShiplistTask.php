<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CleanupShiplistTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Alte Schiffbaudatensätze löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
