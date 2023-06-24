<?php declare(strict_types=1);

namespace EtoA\Quest\Entity;

use LittleCubicleGames\Quests\Entity\TaskInterface;

class Task implements TaskInterface
{
    private ?int $id;
    private int $taskId;
    private int $progress;

    public function __construct(?int $id, int $taskId, int $progress)
    {
        $this->id = $id;
        $this->taskId = $taskId;
        $this->progress = $progress;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        if (null === $this->id) {
            $this->id = $id;
        }
    }

    public function updateProgress(int $progress): void
    {
        $this->progress = $progress;
    }

    public function getProgress(): int
    {
        return $this->progress;
    }

    public function getTaskId(): int
    {
        return $this->taskId;
    }
}
