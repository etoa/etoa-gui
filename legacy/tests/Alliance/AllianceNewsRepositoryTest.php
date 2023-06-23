<?php declare(strict_types=1);

namespace EtoA\Alliance;

use EtoA\SymfonyWebTestCase;

class AllianceNewsRepositoryTest extends SymfonyWebTestCase
{
    private AllianceNewsRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = self::getContainer()->get(AllianceNewsRepository::class);
    }

    public function testGetNewsEntries(): void
    {
        $this->repository->add(1, 2, 'Title', 'text', 3);

        $this->assertNotEmpty($this->repository->getNewsEntries(3));

        $this->repository->deleteAllianceEntries(3);
        $this->assertNotEmpty($this->repository->getNewsEntries(3));

        $this->repository->deleteAllianceEntries(2);
        $this->assertEmpty($this->repository->getNewsEntries(3));
    }

    public function testGetNewsId(): void
    {
        $this->repository->add(1, 2, 'Title', 'text', 3);

        $this->assertNotEmpty($this->repository->getNewsIds());
    }

    public function testGetEntry(): void
    {
        $newsId = $this->repository->add(1, 2, 'Title', 'text', 3);

        $this->assertNotNull($this->repository->getEntry($newsId));

        $this->repository->deleteEntry($newsId);

        $this->assertNull($this->repository->getEntry($newsId));
    }

    public function testCountNewEntriesSince(): void
    {
        $this->repository->add(1, 2, 'Title', 'text', 3);

        $this->assertNotEmpty($this->repository->countNewEntriesSince(3, 0));

        $this->repository->deleteOlderThan(time() + 10);

        $this->assertEmpty($this->repository->countNewEntriesSince(3, 0));
    }
}
