<?php declare(strict_types=1);

namespace EtoA\Quest\Initialization;

class QuestInitializer extends \LittleCubicleGames\Quests\Initialization\QuestInitializer
{
    /** @var bool */
    private $isQuestSystemOn = true;

    public function setIsQuestSystemOn(bool $isQuestSystemOn): void
    {
        $this->isQuestSystemOn = $isQuestSystemOn;
    }

    public function initialize(int $userId): void
    {
        if ($this->isQuestSystemOn) {
            parent::initialize($userId);
        }
    }
}
