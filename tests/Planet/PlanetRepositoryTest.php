<?php

namespace EtoA\Planet;

use EtoA\AbstractDbTestCase;

class PlanetRepositoryTest extends AbstractDbTestCase
{
    /** @var PlanetRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $this->repository = $this->app['etoa.planet.repository'];
    }

    public function testGetUserMainId()
    {
        $userId = 1;
        $this->connection->createQueryBuilder()->insert('planets')->values([
            'id' => ':id',
            'planet_user_id' => ':userId',
            'planet_user_main' => ':isMain',
        ])->setParameters([
            'id' => 1,
            'userId' => $userId,
            'isMain' => 1,
        ])->execute();

        $id = $this->repository->getUserMainId($userId);

        $this->assertEquals(1, $id);
    }
}
