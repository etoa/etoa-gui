<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Quest\Entity\Quest;
use EtoA\Quest\Entity\Task;
use EtoA\WebTestCase;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestControllerTest extends WebTestCase
{
    public function testAdvance(): void
    {
        $userId = 1;
        $quest = new Quest(null, 99, $userId, 'merchant', QuestDefinitionInterface::STATE_AVAILABLE, [new Task(null, 1, 0)]);
        $this->app[QuestRepository::class]->save($quest);
        $this->app['cubicle.quests.quests'] = [99 => ['id' => 99, 'title' => 'Test', 'description' => 'Test', 'task' => ['id' => 1, 'operator' => 'equal-to', 'value' => 10, 'type' => 'hire-specialist', 'description' => 'Test']]];

        $this->loginUser($userId);

        $client = $this->createClient();
        $client->request('PUT', sprintf('/api/quests/%s/advance/%s', $quest->getId(), QuestDefinitionInterface::TRANSITION_START));

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(QuestDefinitionInterface::STATE_IN_PROGRESS, $data['quest']['state']);
    }
}
