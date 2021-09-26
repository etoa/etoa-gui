<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\AbstractDbTestCase;

class ReportRepositoryTest extends AbstractDbTestCase
{
    private ReportRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[ReportRepository::class];
    }

    public function testRemoveUnarchivedread(): void
    {
        $removed = $this->repository->removeUnarchivedread(time());

        $this->assertSame(0, $removed);
    }
}
