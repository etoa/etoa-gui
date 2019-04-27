<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\AbstractDbTestCase;
use EtoA\Quest\Entity\Quest;
use EtoA\Quest\Entity\Task;
use LittleCubicleGames\Quests\Storage\QuestNotFoundException;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestRepositoryTest extends AbstractDbTestCase
{
    /** @var QuestRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.quest.repository'];
    }

    public function testSave(): void
    {
        $quest = new Quest(null, 1, 1, 'merchant', QuestDefinitionInterface::STATE_AVAILABLE, [
            1 => new Task(null, 1, 0),
            2 => new Task(null, 2, 0),
            3 => new Task(null, 3, 0),
        ]);
        $this->repository->save($quest);

        foreach ($quest->getTasks() as $task) {
            $this->assertNotNull($task->getId());
        }
    }

    public function testGetUserQuestEmpty(): void
    {
        $this->expectException(QuestNotFoundException::class);

        $this->repository->getUserQuest(1, 1);
    }

    public function testGetUserQuest(): void
    {
        $userId = 1;
        $quest = new Quest(null, 99, $userId, 'merchant', QuestDefinitionInterface::STATE_AVAILABLE, [
            1 => new Task(null, 1, 11),
            2 => new Task(null, 2, 22),
            3 => new Task(null, 3, 33),
        ]);
        $this->repository->save($quest);

        $quest = $this->repository->getUserQuest($userId, $quest->getId());
        $this->assertInstanceOf(Quest::class, $quest);
        $this->assertCount(3, $quest->getTasks());
    }

    public function testGetActiveQuestsEmpty(): void
    {
        $this->assertSame([], $this->repository->getActiveQuests(99));
    }

    public function testGetActiveQuestsNoTasks(): void
    {
        $userId = 1;
        $quest = new Quest(null, 99, $userId, 'merchant', QuestDefinitionInterface::STATE_AVAILABLE, []);
        $this->repository->save($quest);
        $this->repository->save($quest);

        $quests = $this->repository->getActiveQuests($userId);
        $this->assertCount(1, $quests);
        $this->assertInstanceOf(Quest::class, $quests[0]);
        $this->assertCount(0, $quests[0]->getTasks());
    }

    public function testGetActiveQuests(): void
    {
        $userId = 1;
        $quest = new Quest(null, 99, $userId, 'merchant', QuestDefinitionInterface::STATE_AVAILABLE, [
            1 => new Task(null, 1, 11),
            2 => new Task(null, 2, 22),
            3 => new Task(null, 3, 33),
        ]);
        $this->repository->save($quest);

        $quests = $this->repository->getActiveQuests($userId);
        $this->assertCount(1, $quests);
        $this->assertInstanceOf(Quest::class, $quests[0]);
        $quest = $quests[0];
        $this->assertSame('merchant', $quest->getSlotId());
        $this->assertSame(99, $quest->getQuestId());
        $this->assertSame($userId, $quest->getUser());
        $this->assertSame(QuestDefinitionInterface::STATE_AVAILABLE, $quest->getState());

        $this->assertSame(11, $quest->getTask(1)->getProgress());
        $this->assertSame(22, $quest->getTask(2)->getProgress());
        $this->assertSame(33, $quest->getTask(3)->getProgress());
    }
}
