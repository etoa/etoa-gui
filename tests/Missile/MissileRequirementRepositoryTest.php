<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\AbstractDbTestCase;

class MissileRequirementRepositoryTest extends AbstractDbTestCase
{
    private MissileRequirementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[MissileRequirementRepository::class];
    }

    public function testGetAll(): void
    {
        $requirements = $this->repository->getAll();

        $this->assertNotEmpty($requirements);
    }
}
