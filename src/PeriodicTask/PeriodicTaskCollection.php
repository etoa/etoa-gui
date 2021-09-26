<?php declare(strict_types=1);

namespace EtoA\PeriodicTask;

use Cron\CronExpression;
use EtoA\PeriodicTask\Task\PeriodicTaskInterface;

class PeriodicTaskCollection
{
    private const CRON_ALIASES = [
        '@hourly' => '0 * * * *',
        '@daily' => '0 0 * * *',
        '@weekly' => '0 0 * * 0',
        '@monthly' => '0 0 1 * *',
        '@yearly' => '0 0 1 1 *',
    ];

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
