<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\SymfonyWebTestCase;

class ReportRepositoryTest extends SymfonyWebTestCase
{
    private ReportRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(ReportRepository::class);
    }

    public function testRemoveUnarchivedread(): void
    {
        $removed = $this->repository->removeUnarchivedread(time());

        $this->assertSame(0, $removed);
    }
}
