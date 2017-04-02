<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Core\TokenContext;
use LittleCubicleGames\Quests\QuestAdvancer;
use Symfony\Component\HttpFoundation\JsonResponse;

class QuestController
{
    /** @var QuestAdvancer */
    private $questAdvancer;

    public function __construct(QuestAdvancer $questAdvancer)
    {
        $this->questAdvancer = $questAdvancer;
    }

    public function advanceAction(TokenContext $context, $questId, $transition)
    {
        try {
            $quest = $this->questAdvancer->advanceQuest($questId, $context->getCurrentUser()->id, $transition);
        } catch (\Symfony\Component\Workflow\Exception\LogicException $e) {
            return new JsonResponse([
                'status' => 'error',
                'error' => $e->getMessage(),
            ]);
        }

        return new JsonResponse([
            'status' => 'ok',
            'state' => $quest->getState(),
        ]);
    }
}
