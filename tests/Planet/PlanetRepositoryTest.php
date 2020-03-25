<?php declare(strict_types=1);

namespace EtoA\Planet;

use EtoA\AbstractDbTestCase;

class PlanetRepositoryTest extends AbstractDbTestCase
{
    /** @var PlanetRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.planet.repository'];
    }

    public function testGetUserMainId(): void
    {
        $userId = 1;
        $this->connection->createQueryBuilder()->insert('planets')->values([
            'id' => ':id',
            'planet_user_id' => ':userId',
            'planet_user_main' => ':isMain',
            'planet_name' => ':name',
            'planet_desc' => ':desc',
        ])->setParameters([
            'id' => 1,
            'userId' => $userId,
            'isMain' => 1,
            'name' => 'Planet',
            'desc' => 'Description',
        ])->execute();

        $id = $this->repository->getUserMainId($userId);

        $this->assertEquals(1, $id);
    }

    public function testGetPlanetCount(): void
    {
        $userId = 1;
        for ($i = 0; $i < 5; $i++) {
            $this->connection->createQueryBuilder()->insert('planets')->values([
                'id' => ':id',
                'planet_user_id' => ':userId',
                'planet_user_main' => ':isMain',
                'planet_name' => ':name',
                'planet_desc' => ':desc',
            ])->setParameters([
                'id' => $i,
                'userId' => $userId,
                'isMain' => 0,
                'name' => 'Planet',
                'desc' => 'Description',
            ])->execute();
        }

        $this->assertSame(5, $this->repository->getPlanetCount($userId));
    }

    public function testGetPlanetCountNoPlanets(): void
    {
        $this->assertSame(0, $this->repository->getPlanetCount(1));
    }
}
