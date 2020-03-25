<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Core\TokenContext;
use LittleCubicleGames\Quests\QuestAdvancer;
use LittleCubicleGames\Quests\Storage\QuestStorageInterface;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class QuestController
{
    /** @var QuestAdvancer */
    private $questAdvancer;
    /** @var QuestPresenter */
    private $presenter;
    /** @var QuestStorageInterface */
    private $questStorage;

    public function __construct(QuestAdvancer $questAdvancer, QuestPresenter $presenter, QuestStorageInterface $questStorage)
    {
        $this->questAdvancer = $questAdvancer;
        $this->presenter = $presenter;
        $this->questStorage = $questStorage;
    }

    public function advanceAction(TokenContext $context, int $questId, string $transition): JsonResponse
    {
        try {
            $quest = $this->questAdvancer->advanceQuest($questId, $context->getCurrentUser()->getId(), $transition);
        } catch (\Symfony\Component\Workflow\Exception\LogicException $e) {
            return new JsonResponse([
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 400);
        }

        if (in_array($quest->getState(), [QuestDefinitionInterface::STATE_FINISHED, QuestDefinitionInterface::STATE_REJECTED], true)) {
            $quests = $this->questStorage->getActiveQuests($quest->getUser());
            foreach ($quests as $activeQuest) {
                if ($activeQuest->getSlotId() === $quest->getSlotId()) {
                    $quest = $activeQuest;
                    break;
                }
            }
        }

        if ($quest->getState() === QuestDefinitionInterface::STATE_IN_PROGRESS) {
            try {
                $quest = $this->questAdvancer->advanceQuest($questId, $context->getCurrentUser()->getId(), QuestDefinitionInterface::TRANSITION_COMPLETE);
            } catch (\Symfony\Component\Workflow\Exception\LogicException $e) {
            }
        }

        return new JsonResponse([
            'quest' => $this->presenter->present($quest),
        ]);
    }
}
