<?php declare(strict_types=1);

namespace EtoA\Quest;

use EtoA\Defense\DefenseDataRepository;
use EtoA\Missile\MissileDataRepository;
use EtoA\Ship\ShipDataRepository;
use LittleCubicleGames\Quests\Definition\Quest\Quest;
use LittleCubicleGames\Quests\Definition\Registry\RegistryInterface;
use LittleCubicleGames\Quests\Definition\Slot\Slot;
use LittleCubicleGames\Quests\Workflow\QuestDefinitionInterface;
use PHPUnit\Framework\TestCase;

class QuestPresenterTest extends TestCase
{
    /** @var QuestPresenter */
    private $presenter;
    /** @var RegistryInterface&\PHPUnit\Framework\MockObject\MockObject */
    private $registry;
    /** @var MissileDataRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $missileDataRepository;
    /** @var ShipDataRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $shipDataRepository;
    /** @var DefenseDataRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $defenseDataRepository;

    protected function setUp(): void
    {
        $this->registry = $this->getMockBuilder(RegistryInterface::class)->getMock();
        $this->missileDataRepository = $this->getMockBuilder(MissileDataRepository::class)->disableOriginalConstructor()->getMock();
        $this->shipDataRepository = $this->getMockBuilder(ShipDataRepository::class)->disableOriginalConstructor()->getMock();
        $this->defenseDataRepository = $this->getMockBuilder(DefenseDataRepository::class)->disableOriginalConstructor()->getMock();
        $this->presenter = new QuestPresenter(
            $this->registry,
            $this->missileDataRepository,
            $this->shipDataRepository,
            $this->defenseDataRepository
        );
    }

    public function testPresent(): void
    {
        $quest = $this->getMockBuilder(\EtoA\Quest\Entity\Quest::class)->disableOriginalConstructor()->getMock();
        $slot = $this->getMockBuilder(Slot::class)->disableOriginalConstructor()->getMock();
        $questDefinition = $this->getMockBuilder(Quest::class)->disableOriginalConstructor()->getMock();

        $questId = 1;

        $this->registry
            ->expects($this->once())
            ->method('getQuest')
            ->with($this->equalTo($questId))
            ->willReturn($questDefinition);

        $questDefinition
            ->expects($this->once())
            ->method('getData')
            ->willReturn([
                'title' => 'title',
                'description' => 'description',
                'task' => [
                    'description' => 'taskDescription',
                    'operator' => 'equal-to',
                    'value' => 10,
                    'id' => $taskId = 1,
                ],
            ]);

        $quest
            ->expects($this->any())
            ->method('getId')
            ->willReturn($id = 33);

        $quest
            ->expects($this->any())
            ->method('getState')
            ->willReturn(QuestDefinitionInterface::STATE_AVAILABLE);

        $quest
            ->expects($this->any())
            ->method('getQuestId')
            ->willReturn($questId);

        $quest
            ->expects($this->once())
            ->method('getUser')
            ->willReturn($userId = 12);

        $quest
            ->expects($this->once())
            ->method('getProgressMap')
            ->willReturn([
                $taskId => $progress = 2,
            ]);

        $expected = [
            'id' => $id,
            'canClose' => false,
            'questId' => $questId,
            'state' => QuestDefinitionInterface::STATE_AVAILABLE,
            'user' => $userId,
            'title' => 'title',
            'description' => 'description',
            'taskDescription' => 'taskDescription',
            'transition' => [
                'name' => 'Starten',
                'transition' => QuestDefinitionInterface::TRANSITION_START,
            ],
            'taskProgress' => [
                ['maxProgress' => 10, 'progress' => $progress],
            ],
            'rewards' => [
            ],
        ];
        $result = $this->presenter->present($quest, $slot);

        $this->assertEquals($expected, $result);
    }
}
