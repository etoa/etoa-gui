<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CleanupDeflistTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Alte Verteidigungsbaudatensätze löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
