<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Quest\Entity\Quest;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class QuestResponseListener implements EventSubscriberInterface
{
    /** @var QuestPresenter */
    private $presenter;
    /** @var array[] */
    private $quests = [];

    public function __construct(QuestPresenter $presenter)
    {
        $this->presenter = $presenter;
    }

    public function getQuests(): array
    {
        return array_map(function (array $data): array {
            return $this->presenter->present($data['quest'], $data['slot']);
        }, $this->quests);
    }

    public function addQuest(\LittleCubicleGames\Quests\Initialization\Event\Event $event): void
    {
        /** @var Quest $quest */
        $quest = $event->getQuest();
        $this->quests[$quest->getId()] = [
            'quest' => $quest,
            'slot' => $event->getSlot(),
        ];
    }

    public function removeQuest(Event $event): void
    {
        /** @var Quest $quest */
        $quest = $event->getSubject();
        unset($this->quests[$quest->getId()]);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            \LittleCubicleGames\Quests\Initialization\Event\Event::QUEST_ACTIVE => 'addQuest',
            sprintf('workflow.%s.enter.%s', QuestDefinitionInterface::WORKFLOW_NAME, QuestDefinitionInterface::TRANSITION_ABORT) => 'removeQuest',
            sprintf('workflow.%s.enter.%s', QuestDefinitionInterface::WORKFLOW_NAME, QuestDefinitionInterface::TRANSITION_REJECT) => 'removeQuest',
            sprintf('workflow.%s.enter.%s', QuestDefinitionInterface::WORKFLOW_NAME, QuestDefinitionInterface::TRANSITION_COLLECT_REWARD) => 'removeQuest',
        ];
    }
}
