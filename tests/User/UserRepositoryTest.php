<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserRepositoryTest extends AbstractDbTestCase
{
    private UserRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app[UserRepository::class];
    }

    public function testGetDiscoverMask(): void
    {
        $userId = 10;
        $discoverMask = '000000000';

        $this->createUser($userId, 0, 0, 0, $discoverMask);

        $this->assertSame($discoverMask, $this->repository->getDiscoverMask($userId));
    }

    public function testGetPoints(): void
    {
        $userId = 10;
        $points = 100;

        $this->createUser($userId, 0, 0, $points);

        $this->assertSame($points, $this->repository->getPoints($userId));
    }

    public function testGetAllianceId(): void
    {
        $userId = 10;
        $allianceId = 100;
        $this->createUser($userId, 0, $allianceId);

        $this->assertSame($allianceId, $this->repository->getAllianceId($userId));
    }

    public function testGetSpecialistId(): void
    {
        $userId = 10;
        $specialistId = 3;

        $this->createUser($userId, $specialistId);

        $this->assertSame($specialistId, $this->repository->getSpecialistId($userId));
    }

    public function testGetUserIdByNick(): void
    {
        $this->createUser(1);

        $this->assertSame(1, $this->repository->getUserIdByNick('Nickname'));
    }

    /**
     * @dataProvider searchProvider
     */
    public function testSearchUserNicknames(UserSearch $search = null, array $expected): void
    {
        $this->createUser(1, 0, 1);
        $this->createUser(2);

        $this->assertSame($expected, $this->repository->searchUserNicknames($search));
    }

    public function searchProvider(): array
    {
        return [
            [null, [1 => 'Nickname', 2 => 'Nickname']],
            [UserSearch::create()->notUser(1), [2 => 'Nickname']],
            [UserSearch::create()->allianceId(1), [1 => 'Nickname']],
        ];
    }

    public function testGetUser(): void
    {
        $userId = 10;

        $this->createUser($userId);

        $this->assertNotNull($this->repository->getUser($userId));
    }

    public function testMarkVerifiedByVerificationKey(): void
    {
        $userId = 10;

        $this->createUser($userId, 0, 0, 0, '', 'verification-key');

        $this->assertTrue($this->repository->markVerifiedByVerificationKey('verification-key'));

        $this->assertFalse($this->repository->markVerifiedByVerificationKey('verification-key'));
    }
}
