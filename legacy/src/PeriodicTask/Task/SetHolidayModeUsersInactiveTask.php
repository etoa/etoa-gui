<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class SetHolidayModeUsersInactiveTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Benutzer aus Urlaub inaktiv setzen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
