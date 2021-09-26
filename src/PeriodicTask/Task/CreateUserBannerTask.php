<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class CreateUserBannerTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "User Banner erstellen";
    }

    public function getSchedule(): string
    {
        return "0 * * * *";
    }
}
