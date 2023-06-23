<?php declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\SymfonyWebTestCase;

class PlanetTypeRepositoryTest extends SymfonyWebTestCase
{
    private PlanetTypeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(PlanetTypeRepository::class);
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
