<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use EtoA\SymfonyWebTestCase;

class SolarTypeRepositoryTest extends SymfonyWebTestCase
{
    private SolarTypeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(SolarTypeRepository::class);
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
