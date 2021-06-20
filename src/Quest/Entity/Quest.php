<?php declare(strict_types=1);

namespace EtoA\Quest\Entity;

use LittleCubicleGames\Quests\Entity\QuestInterface;
use LittleCubicleGames\Quests\Entity\TaskInterface;

class Quest implements QuestInterface
{
    private ?int $id;
    private int $questId;
    private int $userId;
    private string $slotId;
    private string $state;
    /** @var Task[] */
    private array $tasks;

    public function __construct(?int $id, int $questId, int $userId, string $slotId, string $state, array $tasks)
    {
        $this->id = $id;
        $this->questId = $questId;
        $this->userId = $userId;
        $this->slotId = $slotId;
        $this->state = $state;
        $this->tasks = $tasks;
    }

    public function getId(): int
    {
        if ($this->id === null) {
            throw new \RuntimeException('Quest not stored yet');
        }

        return $this->id;
    }

    public function setId(int $id): void
    {
        if (null === $this->id) {
            $this->id = $id;
        }
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getQuestId(): int
    {
        return $this->questId;
    }

    public function getUser(): int
    {
        return $this->userId;
    }

    public function getSlotId(): string
    {
        return $this->slotId;
    }

    public function getProgressMap(): array
    {
        $map = [];
        foreach ($this->tasks as $task) {
            $map[$task->getTaskId()] = $task->getProgress();
        }

        return $map;
    }

    public function getTask(int $taskId): TaskInterface
    {
        return $this->tasks[$taskId];
    }

    /**
     * @return Task[]
     */
    public function getTasks(): array
    {
        return $this->tasks;
    }
}
