<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\SymfonyWebTestCase;

class AlliancePointsRepositoryTest extends SymfonyWebTestCase
{
    private AlliancePointsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(AlliancePointsRepository::class);
    }

    public function testGetPoints(): void
    {
        $this->getConnection()->executeQuery('INSERT INTO alliance_points (point_alliance_id, point_points) VALUE (1, 100)');

        $entries = $this->repository->getPoints(1, 1);

        $this->assertNotEmpty($entries);
    }
}
