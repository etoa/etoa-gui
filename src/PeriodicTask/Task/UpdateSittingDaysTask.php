<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class UpdateSittingDaysTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Sitter-Tage aktualisieren";
    }

    public function getSchedule(): string
    {
        return "13 5 1 * *";
    }
}
