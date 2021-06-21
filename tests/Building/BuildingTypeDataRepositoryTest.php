<?php declare(strict_types=1);

namespace EtoA\Building;

use EtoA\AbstractDbTestCase;

class BuildingTypeDataRepositoryTest extends AbstractDbTestCase
{
    private BuildingTypeDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.building_type.datarepository'];
    }

    public function testGetTypeNames(): void
    {
        $names = $this->repository->getTypeNames();

        $this->assertNotEmpty($names);
    }
}
