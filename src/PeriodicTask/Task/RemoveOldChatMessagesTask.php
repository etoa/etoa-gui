<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class RemoveOldChatMessagesTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Alte Chat-Nachrichten löschen";
    }

    public function getSchedule(): string
    {
        return "*/5 * * * *";
    }
}
