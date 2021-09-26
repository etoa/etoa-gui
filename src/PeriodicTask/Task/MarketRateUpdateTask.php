<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class MarketRateUpdateTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Markt-Ressourcen Verhältnisse aktualisieren";
    }

    public function getSchedule(): string
    {
        return "0,30 * * * *";
    }
}
