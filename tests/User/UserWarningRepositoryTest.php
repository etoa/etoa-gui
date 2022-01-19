<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\SymfonyWebTestCase;

class UserWarningRepositoryTest extends SymfonyWebTestCase
{
    private UserWarningRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(UserWarningRepository::class);
    }

    public function testGetUserWarnings(): void
    {
        $this->repository->addEntry(1, 'test', 1);

        $this->assertNotEmpty($this->repository->getUserWarnings(1));
    }

    public function testGetCountAndLatestWarning(): void
    {
        $this->repository->addEntry(1, 'test', 1);

        $values = $this->repository->getCountAndLatestWarning(1);

        $this->assertSame(1, $values['count']);
    }
}
