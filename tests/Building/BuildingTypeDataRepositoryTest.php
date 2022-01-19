<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\SymfonyWebTestCase;

class BuildingTypeDataRepositoryTest extends SymfonyWebTestCase
{
    private BuildingTypeDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(BuildingTypeDataRepository::class);
    }

    public function testGetTypeNames(): void
    {
        $names = $this->repository->getTypeNames();

        $this->assertNotEmpty($names);
    }
}
