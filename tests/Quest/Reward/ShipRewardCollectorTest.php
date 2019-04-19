<?php declare(strict_types=1);

namespace EtoA\Quest\Reward;

use EtoA\Planet\PlanetRepository;
use EtoA\Ship\ShipRepository;
use LittleCubicleGames\Quests\Definition\Reward\Reward;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use PHPUnit\Framework\TestCase;

class ShipRewardCollectorTest extends TestCase
{
    /** @var ShipRewardCollector */
    private $collector;
    /** @var ShipRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $shipRepository;
    /** @var PlanetRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $planetRepository;

    protected function setUp(): void
    {
        $this->shipRepository = $this->getMockBuilder(ShipRepository::class)->disableOriginalConstructor()->getMock();
        $this->planetRepository = $this->getMockBuilder(PlanetRepository::class)->disableOriginalConstructor()->getMock();
        $this->collector = new ShipRewardCollector($this->shipRepository, $this->planetRepository);
    }

    public function testCollect(): void
    {
        $mainPlanetId = 33;
        $userId = 1;
        $shipId = 13;
        $amount = 5;

        $reward = new Reward([
            'type' => ShipRewardCollector::TYPE,
            'value' => $amount,
            'ship_id' => $shipId,
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

        $this->shipRepository
            ->expects($this->once())
            ->method('addShip')
            ->with($this->equalTo($shipId), $this->equalTo($amount), $this->equalTo($userId), $this->equalTo($mainPlanetId));

        $this->collector->collect($reward, $quest);
    }
}
