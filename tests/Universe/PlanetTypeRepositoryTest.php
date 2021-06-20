<?php declare(strict_types=1);

namespace EtoA\Universe;

use EtoA\AbstractDbTestCase;

class PlanetTypeRepositoryTest extends AbstractDbTestCase
{
    private PlanetTypeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.universe.planet_type.repository'];
    }

    public function testGetPlanetTypeNames(): void
    {
        $names = $this->repository->getPlanetTypeNames();

        $this->assertNotEmpty($names);
    }

    public function testGetPlanetTypes(): void
    {
        $types = $this->repository->getPlanetTypes();

        $this->assertNotEmpty($types);
    }
}
