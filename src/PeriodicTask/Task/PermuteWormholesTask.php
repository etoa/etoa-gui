<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

class PermuteWormholesTask implements PeriodicTaskInterface
{
    public function getDescription(): string
    {
        return "Wurmlöcher vertauschen";
    }

    public function getSchedule(): string
    {
        return "0 * * * *";
    }
}
