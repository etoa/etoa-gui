<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveInactiveChatUsersTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Inaktive Chat-User entfernen";
    }

    public function getSchedule(): string
    {
        return "* * * * *";
    }
}
