<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\AbstractDbTestCase;

class MissileDataRepositoryTest extends AbstractDbTestCase
{
    /** @var MissileDataRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.missile.datarepository'];
    }

    public function testGetMissileNames(): void
    {
        $names = $this->repository->getMissileNames();
        $this->assertNotEmpty($names);
    }

    public function testGetMissiles(): void
    {
        $missiles = $this->repository->getMissiles();

        $this->assertNotEmpty($missiles);
    }

    public function testGetMissile(): void
    {
        $missile = $this->repository->getMissile(1);

        $this->assertNotNull($missile);
        $this->assertSame(1, $missile->id);
    }
}
