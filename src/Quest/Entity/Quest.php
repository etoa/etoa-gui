<?php

namespace EtoA\Quest\Entity;

use LittleCubicleGames\Quests\Entity\QuestInterface;

class Quest implements QuestInterface
{
    /** @var int */
    private $id;
    /** @var int */
    private $questId;
    /** @var int */
    private $userId;
    /** @var string */
    private $slotId;
    /** @var string */
    private $state;
    /** @var Task[] */
    private $tasks;

    public function __construct($id, $questId, $userId, $slotId, $state, array $tasks)
    {
        $this->id = $id;
        $this->questId = (int)$questId;
        $this->userId = (int)$userId;
        $this->slotId = $slotId;
        $this->state = $state;
        $this->tasks = $tasks;
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

    public function setState($state)
    {
        $this->state = $state;
    }

    public function getState()
    {
        return $this->state;
    }

    public function getQuestId()
    {
        return $this->questId;
    }

    public function getUser()
    {
        return $this->userId;
    }

    public function getSlotId()
    {
        return $this->slotId;
    }

    public function getProgressMap()
    {
        $map = [];
        foreach ($this->tasks as $task) {
            $map[$task->getTaskId()] = $task->getProgress();
        }

        return $map;
    }

    public function getTask($taskId)
    {
        return $this->tasks[$taskId];
    }

    public function getTasks()
    {
        return $this->tasks;
    }
}
