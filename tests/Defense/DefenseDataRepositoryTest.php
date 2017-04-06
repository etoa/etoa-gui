<?php

namespace EtoA\Defense;

use EtoA\AbstractDbTestCase;

class DefenseDataRepositoryTest extends AbstractDbTestCase
{
    /** @var DefenseDataRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app['etoa.defense.datarepository'];
    }

    public function testGetDefenseNames()
    {
        $names = $this->repository->getDefenseNames();
        $this->assertNotEmpty($names);
    }
}
