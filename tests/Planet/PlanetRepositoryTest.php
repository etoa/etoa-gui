<?php

namespace EtoA\Planet;

use Doctrine\DBAL\Query\QueryBuilder;
use PHPUnit\Framework\TestCase;

class PlanetRepositoryTest extends TestCase
{
    /** @var PlanetRepository */
    private $repository;

    protected function setUp()
    {
        parent::setUp();

        $app = require dirname(dirname(__DIR__)).'/src/app.php';

        $this->repository = $app['etoa.planet.repository'];
        $this->connection = $app['db'];
    }

    public function testGetUserMainId()
    {
        $userId = 1;
        (new QueryBuilder($this->connection))->insert('planets')->values([
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
