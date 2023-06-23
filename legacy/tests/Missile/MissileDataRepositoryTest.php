<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\SymfonyWebTestCase;

class MissileDataRepositoryTest extends SymfonyWebTestCase
{
    private MissileDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(MissileDataRepository::class);
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
