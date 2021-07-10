<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserSurveillanceRepositoryTest extends AbstractDbTestCase
{
    private UserSurveillanceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserSurveillanceRepository::class];
    }

    public function testAddEntry(): void
    {
        $this->repository->addEntry(1, 'page', 'req', 'req-raw', 'post', 'fgdfg');

        $this->assertSame(1, (int) $this->connection->fetchOne('SELECT COUNT(*) FROM user_surveillance'));
    }
}
