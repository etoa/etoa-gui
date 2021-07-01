<?php declare(strict_types=1);

namespace EtoA\Universe\Planet;

use EtoA\AbstractDbTestCase;

class PlanetRepositoryTest extends AbstractDbTestCase
{
    private PlanetRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[PlanetRepository::class];
    }

    public function testGetUserMainId(): void
    {
        $userId = 1;
        $this->setupPlanet(1, $userId, true);

        $id = $this->repository->getUserMainId($userId);

        $this->assertEquals(1, $id);
    }

    public function testGetPlanetUserId(): void
    {
        $userId = 42;
        $this->setupPlanet(1, $userId, true);

        $planetUserId = $this->repository->getPlanetUserId(1);

        $this->assertEquals($userId, $planetUserId);
    }

    public function testGetPlanetCount(): void
    {
        $userId = 1;
        for ($i = 0; $i < 5; $i++) {
            $this->setupPlanet($i, $userId, false);
        }

        $this->assertSame(5, $this->repository->getPlanetCount($userId));
    }

    public function testGetPlanetCountNoPlanets(): void
    {
        $this->assertSame(0, $this->repository->getPlanetCount(1));
    }

    private function setupPlanet(int $planetId, int $userId, bool $isMain): void
    {
        $this->connection->createQueryBuilder()->insert('planets')->values([
            'id' => ':id',
            'planet_user_id' => ':userId',
            'planet_user_main' => ':isMain',
            'planet_name' => ':name',
            'planet_desc' => ':desc',
        ])->setParameters([
            'id' => $planetId,
            'userId' => $userId,
            'isMain' => (int) $isMain,
            'name' => 'Planet',
            'desc' => 'Description',
        ])->execute();
    }
}
