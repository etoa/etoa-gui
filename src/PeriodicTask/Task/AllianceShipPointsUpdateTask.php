<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class AllianceShipPointsUpdateTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Allianz-Schiffsteile berechnen";
    }

    public function getSchedule(): string
    {
        return "0 * * * *";
    }
}
