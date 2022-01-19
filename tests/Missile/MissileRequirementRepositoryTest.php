<?php declare(strict_types=1);

namespace EtoA\Missile;

use EtoA\SymfonyWebTestCase;

class MissileRequirementRepositoryTest extends SymfonyWebTestCase
{
    private MissileRequirementRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(MissileRequirementRepository::class);
    }

    public function testGetAll(): void
    {
        $requirements = $this->repository->getAll()->getAll(1);

        $this->assertNotEmpty($requirements);
    }
}
