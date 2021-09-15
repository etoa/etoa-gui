<?php declare(strict_types=1);

namespace EtoA\Controller;

use EtoA\Quest\Entity\Quest;
use EtoA\Quest\Entity\Task;
use EtoA\SymfonyWebTestCase;
use LittleCubicleGames\Quests\Storage\QuestStorageInterface;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestControllerTest extends SymfonyWebTestCase
{
    public function testAdvance(): void
    {
        $userId = 1;

        $client = self::createClient();

        $quest = new Quest(null, 0, $userId, 'merchant', QuestDefinitionInterface::STATE_AVAILABLE, [new Task(null, 0, 0)]);
        static::getContainer()->get(QuestStorageInterface::class)->save($quest);

        $this->loginUser($userId);

        $client->request('PUT', sprintf('/api/quests/%s/advance/%s', $quest->getId(), QuestDefinitionInterface::TRANSITION_START));

        $this->assertSame(200, $client->getResponse()->getStatusCode(), $client->getResponse()->getContent());
        $data = json_decode($client->getResponse()->getContent(), true);
        $this->assertSame(QuestDefinitionInterface::STATE_IN_PROGRESS, $data['quest']['state']);
    }
}
