<?php declare(strict_types=1);

namespace EtoA\User;

use EtoA\AbstractDbTestCase;

class UserRepositoryTest extends AbstractDbTestCase
{
    /** @var UserRepository */
    private $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = $this->app['etoa.user.repository'];
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

    private function createUser(int $userId, int $specialistId = 0, int $allianceId = 0, int $points = 0, string $discoverMask = ''): void
    {
        $this->connection
            ->createQueryBuilder()
            ->insert('users')
            ->values([
                'user_id' => ':userId',
                'user_setup' => ':setup',
                'user_name' => ':name',
                'user_nick' => ':nick',
                'user_password' => ':password',
                'user_password_temp' => ':password',
                'user_session_key' => ':session',
                'user_email' => ':email',
                'user_email_fix' => ':email',
                'user_ip' => ':empty',
                'user_hostname' => ':empty',
                'user_ban_reason' => ':empty',
                'user_profile_text' => ':empty',
                'user_avatar' => ':empty',
                'user_signature' => ':empty',
                'user_client' => ':empty',
                'user_profile_board_url' => ':empty',
                'user_profile_img' => ':empty',
                'user_observe' => ':empty',
                'discoverymask' => ':discoverymask',
                'dual_email' => ':empty',
                'dual_name' => ':empty',
                'user_specialist_id' => ':specialistId',
                'user_alliance_id' => ':allianceId',
                'user_points' => ':points',
            ])->setParameters([
                'userId' => $userId,
                'setup' => 1,
                'name' => 'User Name',
                'nick' => 'Nickname',
                'password' => 'password',
                'session' => 'session',
                'email' => 'test@etoa.net',
                'empty' => '',
                'specialistId' => $specialistId,
                'allianceId' => $allianceId,
                'points' => $points,
                'discoverymask' => $discoverMask,
            ])->execute();
    }
}
