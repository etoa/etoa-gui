<?php

namespace EtoA\Quest\Entity;

use LittleCubicleGames\Quests\Entity\TaskInterface;

class Task implements TaskInterface
{
    /** @var int */
    private $id;
    /** @var int */
    private $taskId;
    /** @var int */
    private $progress;

    public function __construct($id, $taskId, $progress)
    {
        $this->id = $id;
        $this->taskId = (int)$taskId;
        $this->progress = (int)$progress;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        if (null === $this->id) {
            $this->id = $id;
        }
    }

    public function updateProgress($progress)
    {
        $this->progress = $progress;
    }

    public function getProgress()
    {
        return $this->progress;
    }

    public function getTaskId()
    {
        return $this->taskId;
    }
}
