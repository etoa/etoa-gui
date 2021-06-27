<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\AbstractDbTestCase;

class AllianceHistoryRepositoryTest extends AbstractDbTestCase
{
    private AllianceHistoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = $this->app[AllianceHistoryRepository::class];
    }

    public function testAddEntry(): void
    {
        $this->repository->addEntry(1, 'test');
    }
}
