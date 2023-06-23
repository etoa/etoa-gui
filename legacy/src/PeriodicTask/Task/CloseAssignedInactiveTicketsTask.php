<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CloseAssignedInactiveTicketsTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Inaktive Tickets schliessen welche von einem Admin beantwortet wurden";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
