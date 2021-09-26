<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveDeletedUsersTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Zum Löschen markierte User löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
