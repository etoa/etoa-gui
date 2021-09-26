<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CalculateRankingTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Punkte berechnen und Rangliste aktualisieren";
    }

    public function getSchedule(): string
    {
        return "0 * * * *";
    }
}
