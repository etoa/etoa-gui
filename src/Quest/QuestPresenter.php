<?php

namespace EtoA\Quest;

use EtoA\Quest\Entity\Quest;
use LittleCubicleGames\Quests\Definition\Registry\RegistryInterface;
use LittleCubicleGames\Quests\Definition\Slot\Slot;
use LittleCubicleGames\Quests\Definition\Task\AndTask;
use LittleCubicleGames\Quests\Definition\Task\OrTask;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestPresenter
{
    /** @var RegistryInterface */
    private $registry;

    private $transitions = [
        QuestDefinitionInterface::STATE_AVAILABLE => [
            'transition' => QuestDefinitionInterface::TRANSITION_START,
            'name' => 'Starten',
        ],
        QuestDefinitionInterface::STATE_COMPLETED => [
            'transition' => QuestDefinitionInterface::TRANSITION_COLLECT_REWARD,
            'name' => 'Belohnung abholen',
        ],
    ];

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function present(Quest $quest, Slot $slot)
    {
        /** @var \LittleCubicleGames\Quests\Definition\Quest\Quest $questDefinition */
        $questDefinition = $this->registry->getQuest($quest->getQuestId());
        $questData = $questDefinition->getData();

        return [
            'id' => $quest->getId(),
            'questId' => $quest->getQuestId(),
            'canClose' => false,
            'user' => $quest->getUser(),
            'title' => $questData['title'],
            'description' => $questData['description'],
            'transition' => isset($this->transitions[$quest->getState()]) ? $this->transitions[$quest->getState()] : null,
            'taskDescription' => $questData['task']['description'],
            'taskProgress' => $this->buildProgress($quest->getProgressMap(), $questData['task']),
        ];
    }

    private function buildProgress(array $progressMap, array $taskData)
    {
        switch ($taskData['operator']) {
            case AndTask::TASK_NAME:
                $progress = [];
                foreach ($taskData['children'] as $child) {
                    $progress[] = $this->buildProgress($progressMap, $child);
                }

                return $progress;
            case OrTask::TASK_NAME:
                $progress = [];
                $currentMaxProgress = 0;
                foreach ($taskData['children'] as $child) {
                    $childProgressData = $this->buildProgress($progressMap, $child);
                    $childProgress = $childProgressData['progress'] / $childProgressData['maxProgress'];
                    if ($childProgress > $currentMaxProgress) {
                        $progress = [$childProgressData];
                        $currentMaxProgress = $childProgress;
                    }
                }

                return $progress;
            default:
                return [[
                    'maxProgress' => $taskData['value'],
                    'progress' => $progressMap[$taskData['id']],
                ]];
        }
    }
}
