<?php

declare(strict_types=1);

namespace EtoA\Universe\Star;

use EtoA\AbstractDbTestCase;

class SolarTypeRepositoryTest extends AbstractDbTestCase
{
    private SolarTypeRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[SolarTypeRepository::class];
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
