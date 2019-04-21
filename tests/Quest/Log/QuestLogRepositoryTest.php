<?php declare(strict_types=1);

namespace EtoA\Quest\Log;

use Doctrine\DBAL\Query\QueryBuilder;
use EtoA\AbstractDbTestCase;
use EtoA\Quest\Entity\Quest;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;

class QuestLogRepositoryTest extends AbstractDbTestCase
{
    /** @var QuestLogRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.quest.log.repository'];
    }

    public function testLog(): void
    {
        $quest = new Quest(1, 2, 3, 'merchant', QuestDefinitionInterface::STATE_IN_PROGRESS, []);
        $this->repository->log($quest, QuestDefinitionInterface::STATE_AVAILABLE, QuestDefinitionInterface::TRANSITION_START);

        $result = (new QueryBuilder($this->connection))
            ->select('l.*')
            ->from('quest_log', 'l')
            ->execute()
            ->fetchAll();

        $this->assertCount(1, $result);
        $this->assertSame(1, (int)$result[0]['quest_id']);
        $this->assertSame(2, (int)$result[0]['quest_data_id']);
        $this->assertSame(3, (int)$result[0]['user_id']);
        $this->assertSame('merchant', $result[0]['slot_id']);
        $this->assertSame(QuestDefinitionInterface::TRANSITION_START, $result[0]['transition']);
        $this->assertSame(QuestDefinitionInterface::STATE_AVAILABLE, $result[0]['previous_state']);
    }
}
