<?php declare(strict_types=1);

namespace EtoA\Quest\Reward;

use EtoA\Defense\DefenseRepository;
use EtoA\Planet\PlanetRepository;
use LittleCubicleGames\Quests\Definition\Reward\Reward;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use PHPUnit\Framework\TestCase;

class DefenseRewardCollectorTest extends TestCase
{
    /** @var DefenseRewardCollector */
    private $collector;
    /** @var DefenseRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $defenseRepository;
    /** @var PlanetRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $planetRepository;

    protected function setUp(): void
    {
        $this->defenseRepository = $this->getMockBuilder(DefenseRepository::class)->disableOriginalConstructor()->getMock();
        $this->planetRepository = $this->getMockBuilder(PlanetRepository::class)->disableOriginalConstructor()->getMock();
        $this->collector = new DefenseRewardCollector($this->defenseRepository, $this->planetRepository);
    }

    public function testCollect(): void
    {
        $mainPlanetId = 33;
        $userId = 1;
        $shipId = 13;
        $amount = 5;

        $reward = new Reward([
            'type' => DefenseRewardCollector::TYPE,
            'value' => $amount,
            'defense_id' => $shipId,
        ]);

        $quest = $this->getMockBuilder(QuestInterface::class)->disableOriginalConstructor()->getMock();
        $quest
            ->expects($this->any())
            ->method('getUser')
            ->willReturn($userId);

        $this->planetRepository
            ->expects($this->once())
            ->method('getUserMainId')
            ->with($this->equalTo($userId))
            ->willReturn($mainPlanetId);

        $this->defenseRepository
            ->expects($this->once())
            ->method('addDefense')
            ->with($this->equalTo($shipId), $this->equalTo($amount), $this->equalTo($userId), $this->equalTo($mainPlanetId));

        $this->collector->collect($reward, $quest);
    }
}
