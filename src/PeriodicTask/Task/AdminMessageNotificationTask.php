<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class AdminMessageNotificationTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Admin-Mailbenachrichtigungen versenden";
    }

    public function getSchedule(): string
    {
        return "0,30 * * * *";
    }
}
