<?PHP

class QuestTransitionJsonResponder extends JsonResponder
{
    /** @var \LittleCubicleGames\Quests\QuestAdvancer */
    private $questAdvancer;

    public function setQuestAdvancer(\LittleCubicleGames\Quests\QuestAdvancer $questAdvancer)
    {
        $this->questAdvancer = $questAdvancer;
    }
    public function getRequiredParams() {
        return [
            'userId',
            'questId',
            'transition'
        ];
    }

    public function getResponse($params)
    {
        try {
            $quest = $this->questAdvancer->advanceQuest($params['questId'], $params['userId'], $params['transition']);
        } catch (\Symfony\Component\Workflow\Exception\LogicException $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }

        return [
            'status' => 'ok',
            'state' => $quest->getState(),
        ];
    }
}
