<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\SymfonyWebTestCase;

class UserMultiRepositoryTest extends SymfonyWebTestCase
{
    private UserMultiRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(UserMultiRepository::class);
    }

    public function testGetUserEntries(): void
    {
        $this->repository->addOrUpdateEntry(1, 2, 'test');

        $this->assertNotEmpty($this->repository->getUserEntries(1));
        $this->assertNotEmpty($this->repository->getUserEntries(1, true));
        $this->assertEmpty($this->repository->getUserEntries(1, false));
    }

    public function testExistsEntryWith(): void
    {
        $this->repository->addOrUpdateEntry(1, 2, 'test');

        $this->assertTrue($this->repository->existsEntryWith(1, 2));
        $this->assertTrue($this->repository->existsEntryWith(2, 1));

        $this->assertFalse($this->repository->existsEntryWith(3, 1));
    }

    public function testDeactivate(): void
    {
        $this->repository->addOrUpdateEntry(1, 2, 'test');

        $this->assertEmpty($this->repository->getUserEntries(1, false));

        $this->repository->deactivate(1, 2);

        $this->assertNotEmpty($this->repository->getUserEntries(1, false));
    }
}
