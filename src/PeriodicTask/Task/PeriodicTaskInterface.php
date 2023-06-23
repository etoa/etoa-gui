<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Task;

interface PeriodicTaskInterface
{
    public function getDescription(): string;
    public function getSchedule(): string;
}
