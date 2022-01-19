<?php declare(strict_types=1);

namespace EtoA\Quest\Log;

use EtoA\Quest\Entity\Quest;
use EtoA\SymfonyWebTestCase;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestLogRepositoryTest extends SymfonyWebTestCase
{
    private QuestLogRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(QuestLogRepository::class);
    }

    public function testLog(): void
    {
        $quest = new Quest(1, 2, 3, 'merchant', QuestDefinitionInterface::STATE_IN_PROGRESS, []);
        $this->repository->log($quest, QuestDefinitionInterface::STATE_AVAILABLE, QuestDefinitionInterface::TRANSITION_START);

        $result = $this->getConnection()->fetchAllAssociative('SELECT * FROM quest_log');

        $this->assertCount(1, $result);
        $this->assertSame(1, (int)$result[0]['quest_id']);
        $this->assertSame(2, (int)$result[0]['quest_data_id']);
        $this->assertSame(3, (int)$result[0]['user_id']);
        $this->assertSame('merchant', $result[0]['slot_id']);
        $this->assertSame(QuestDefinitionInterface::TRANSITION_START, $result[0]['transition']);
        $this->assertSame(QuestDefinitionInterface::STATE_AVAILABLE, $result[0]['previous_state']);
    }
}
