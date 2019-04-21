<?php declare(strict_types=1);

namespace EtoA\Quest\Reward;

use EtoA\Missile\MissileRepository;
use EtoA\Planet\PlanetRepository;
use LittleCubicleGames\Quests\Definition\Reward\Reward;
use LittleCubicleGames\Quests\Entity\QuestInterface;
use PHPUnit\Framework\TestCase;

class MissileRewardCollectorTest extends TestCase
{
    /** @var MissileRewardCollector */
    private $collector;
    /** @var MissileRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $missileRepository;
    /** @var PlanetRepository&\PHPUnit\Framework\MockObject\MockObject */
    private $planetRepository;

    protected function setUp(): void
    {
        $this->missileRepository = $this->getMockBuilder(MissileRepository::class)->disableOriginalConstructor()->getMock();
        $this->planetRepository = $this->getMockBuilder(PlanetRepository::class)->disableOriginalConstructor()->getMock();
        $this->collector = new MissileRewardCollector($this->missileRepository, $this->planetRepository);
    }

    public function testCollect(): void
    {
        $mainPlanetId = 33;
        $userId = 1;
        $missileId = 13;
        $amount = 5;

        $reward = new Reward([
            'type' => MissileRewardCollector::TYPE,
            'value' => $amount,
            'missile_id' => $missileId,
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

        $this->missileRepository
            ->expects($this->once())
            ->method('addMissile')
            ->with($this->equalTo($missileId), $this->equalTo($amount), $this->equalTo($userId), $this->equalTo($mainPlanetId));

        $this->collector->collect($reward, $quest);
    }
}
