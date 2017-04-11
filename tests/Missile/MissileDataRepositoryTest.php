<?php

namespace EtoA\Missile;

use EtoA\AbstractDbTestCase;

class MissileDataRepositoryTest extends AbstractDbTestCase
{
    /** @var MissileDataRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app['etoa.missile.datarepository'];
    }

    public function testGetMissileNames()
    {
        $names = $this->repository->getMissileNames();
        $this->assertNotEmpty($names);
    }
}
