<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\SymfonyWebTestCase;

class TechnologyDataRepositoryTest extends SymfonyWebTestCase
{
    private TechnologyDataRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(TechnologyDataRepository::class);
    }

    public function testGetTechnologyNames(): void
    {
        $names = $this->repository->getTechnologyNames();

        $this->assertNotEmpty($names);
    }

    public function testGetTechnologies(): void
    {
        $technologies = $this->repository->getTechnologies();

        $this->assertNotEmpty($technologies);
    }

    public function testGetTechnology(): void
    {
        $technology = $this->repository->getTechnology(4);

        $this->assertNotNull($technology);
        $this->assertSame(4, $technology->id);
    }

    public function testGetTechnologiesByTypes(): void
    {
        $technologies = $this->repository->getTechnologiesByType(2);

        $this->assertNotEmpty($technologies);
    }
}
