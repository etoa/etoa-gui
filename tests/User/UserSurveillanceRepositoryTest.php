<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\SymfonyWebTestCase;

class UserSurveillanceRepositoryTest extends SymfonyWebTestCase
{
    private UserSurveillanceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = self::getContainer()->get(UserSurveillanceRepository::class);
    }

    public function testAddEntry(): void
    {
        $this->repository->addEntry(1, 'page', 'req', 'req-raw', 'post', 'fgdfg');

        $this->assertSame(1, (int) $this->getConnection()->fetchOne('SELECT COUNT(*) FROM user_surveillance'));
    }
}
