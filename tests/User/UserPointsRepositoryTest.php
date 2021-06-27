<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserPointsRepositoryTest extends AbstractDbTestCase
{
    private UserPointsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[UserPointsRepository::class];
    }

    public function testGetPoints(): void
    {
        $this->connection->executeQuery('INSERT INTO user_points (point_user_id, point_points) VALUE (1, 100)');

        $entries = $this->repository->getPoints(1, 1);

        $this->assertNotEmpty($entries);
    }
}
