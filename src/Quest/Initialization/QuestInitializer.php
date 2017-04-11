<?php

namespace EtoA\Quest\Initialization;

class QuestInitializer extends \LittleCubicleGames\Quests\Initialization\QuestInitializer
{
    private $isQuestSystemOn = true;

    public function setIsQuestSystemOn($isQuestSystemOn)
    {
        $this->isQuestSystemOn = $isQuestSystemOn;
    }

    public function initialize($userId)
    {
        if ($this->isQuestSystemOn) {
            parent::initialize($userId);
        }
    }
}
