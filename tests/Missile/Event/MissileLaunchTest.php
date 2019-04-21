<?php declare(strict_types=1);

namespace EtoA\Missile\Event;

use PHPUnit\Framework\TestCase;

class MissileLaunchTest extends TestCase
{
    public function testGetMissileCount(): void
    {
        $event = new MissileLaunch([1 => 2, 2 => 10]);

        $this->assertSame(12, $event->getMissileCount());
    }
}
