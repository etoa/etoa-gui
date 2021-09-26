<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveInactiveUsersTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Inaktive User löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
