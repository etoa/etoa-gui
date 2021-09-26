<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class ClearIPHostnameCacheTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Alte IP/Hostnamen Mappings aus Cache löschen";
    }

    public function getSchedule(): string
    {
        return "13 3 * * *";
    }
}
