<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveOldBannsTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Abgelaufene Sperren löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
