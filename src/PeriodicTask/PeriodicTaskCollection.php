<?php declare(strict_types=1);

namespace EtoA\PeriodicTask;

use Cron\CronExpression;
use EtoA\PeriodicTask\Task\PeriodicTaskInterface;

class PeriodicTaskCollection
{
    /** @var iterable<PeriodicTaskInterface> */
    private iterable $tasks;

    /**
     * @param iterable<PeriodicTaskInterface> $tasks
     */
    public function __construct($tasks)
    {
        $this->tasks = $tasks;
    }

    /**
     * @return iterable<PeriodicTaskInterface>
     */
    public function getAllTasks(): iterable
    {
        return $this->tasks;
    }

    /**
     * @return \Generator<PeriodicTaskInterface>
     */
    public function getScheduledTasks(int $timestamp): iterable
    {
        foreach ($this->tasks as $task) {
            if ($this->shouldRun($task, $timestamp)) {
                yield $task;
            }
        }
    }

    public function getTask(string $taskName): ?PeriodicTaskInterface
    {
        foreach ($this->tasks as $task) {
            $reflection = new \ReflectionClass($task);
            if ($reflection->getShortName() === $taskName) {
                return $task;
            }
        }

        return null;
    }

    public function shouldRun(PeriodicTaskInterface $task, int $timestamp): bool
    {
        $cron = new CronExpression($task->getSchedule());

        return $cron->isDue(new \DateTimeImmutable('@' . $timestamp));
    }
}
