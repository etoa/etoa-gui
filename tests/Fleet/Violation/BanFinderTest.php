<?php declare(strict_types=1);

namespace EtoA\Fleet\Violation;

use EtoA\Fleet\Attack\BanFinder;
use EtoA\Log\BattleLog;
use PHPUnit\Framework\TestCase;

class BanFinderTest extends TestCase
{
    private BanFinder $finder;

    protected function setUp(): void
    {
        $this->finder = new BanFinder();
    }

    public function testWaveTooManyAttacks(): void
    {
        $logs = [
            $this->createLog(1, 2, time(), 3, false),
            $this->createLog(1, 2, time(), 3, false),
            $this->createLog(1, 2, time(), 3, false),
            $this->createLog(1, 2, time(), 3, false),
        ];

        $bans = $this->finder->find($logs);
        $this->assertNotEmpty($bans);
        $this->assertSame("Mehr als 3 Angriffe in einer Welle auf dem selben Ziel.<br />Anzahl Angriffe : 4<br />Dauer der Welle: <br /><br />", $bans[0]->banReason);
    }

    public function testAttacksNotInWave(): void
    {
        $logs = [
            $this->createLog(1, 2, time() - 3600, 3, false),
            $this->createLog(1, 2, time(), 3, false),
        ];

        $bans = $this->finder->find($logs);
        $this->assertNotEmpty($bans);
        $this->assertSame("Der Abstand zwischen 2 Angriffen/Wellen auf ein Ziel ist kleiner als 6 Stunden.<br />Dauer zwischen den beiden Angriffen: 1h <br /><br />", $bans[0]->banReason);
    }

    public function testTooManyAttacksSamePlanet(): void
    {
        $logs = [
            $this->createLog(1, 2, time() - 14 * 3600, 3, false),
            $this->createLog(1, 2, time() - 7 * 3600, 3, false),
            $this->createLog(1, 2, time(), 3, false),
        ];

        $bans = $this->finder->find($logs);
        $this->assertNotEmpty($bans);
        $this->assertSame("Mehr als 2 Angriffe/Wellen auf ein Ziel.<br />Anzahl:3<br /><br />", $bans[0]->banReason);
    }

    public function testTooManyAttacksDifferentPlanets(): void
    {
        $logs = [
            $this->createLog(1, 2, time(), 1, false),
            $this->createLog(1, 2, time(), 2, false),
            $this->createLog(1, 2, time(), 3, false),
            $this->createLog(1, 2, time(), 4, false),
            $this->createLog(1, 2, time(), 5, false),
            $this->createLog(1, 2, time(), 6, false),
        ];

        $bans = $this->finder->find($logs);
        $this->assertNotEmpty($bans);
        $this->assertSame("Mehr als 5 innerhalb von 24 Stunden.<br/>Anzahl: 6<br/><br/>", $bans[0]->banReason);
    }

    public function testFindEmpty(): void
    {
        $this->assertSame([], $this->finder->find([]));
    }

    private function createLog(int $userId, int $entityUserId, int $landTime, int $entityId, bool $war): BattleLog
    {
        return new BattleLog([
            'id' => 1,
            'user_id' => sprintf(',%s,', $userId),
            'entity_user_id' => sprintf(',%s,', $entityUserId),
            'landtime' => $landTime,
            'entity_id' => $entityId,
            'action' => '',
            'war' => $war,
        ]);
    }
}
