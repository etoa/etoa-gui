<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class AnalyzeTablesTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Tabellen analysieren";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
