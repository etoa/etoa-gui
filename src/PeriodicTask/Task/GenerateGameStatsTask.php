<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class GenerateGameStatsTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Spielstatistiken generieren und speichern";
    }

    public function getSchedule(): string
    {
        return "3 * * * *";
    }
}
