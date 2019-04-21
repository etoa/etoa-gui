<?php declare(strict_types=1);

namespace EtoA\Technology;

use EtoA\AbstractDbTestCase;

class TechnologyRepositoryTest extends AbstractDbTestCase
{
    /** @var TechnologyRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.technology.repository'];
    }

    public function testGetUserLevelNoTechnology(): void
    {
        $this->assertSame(0, $this->repository->getTechnologyLevel(1, 1));
    }

    public function testGetUserLevel(): void
    {
        $userId = 1;
        $technologyId = 3;
        $this->connection->createQueryBuilder()
            ->insert('techlist')
            ->values([
                'techlist_user_id' => $userId,
                'techlist_current_level' => 6,
                'techlist_tech_id' => $technologyId,
            ])->execute();

        $this->assertSame(6, $this->repository->getTechnologyLevel($userId, $technologyId));
    }
}
