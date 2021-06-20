<?php declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\AbstractDbTestCase;

class SolarTypeRepositoryTest extends AbstractDbTestCase
{
    private SolarTypeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.universe.solar_type.repository'];
    }

    public function testGetSolarTypeNames(): void
    {
        $names = $this->repository->getSolarTypeNames();

        $this->assertNotEmpty($names);
    }

    public function testGetSolarTypes(): void
    {
        $types = $this->repository->getSolarTypes();

        $this->assertNotEmpty($types);
    }
}
