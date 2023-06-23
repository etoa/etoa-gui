<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class UpdateUserTitlesTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Titel aktualisieren";
    }

    public function getSchedule(): string
    {
        return "0 * * * *";
    }
}
