<?php declare(strict_types=1);

namespace EtoA;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Pimple\Container;

class AbstractDbTestCase extends TestCase
{
    private static ?Connection $staticConnection = null;
    use DbTestTrait;

    protected Container $app;
    protected Connection $connection;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->setupApplication();

        if (null === self::$staticConnection) {
            self::$staticConnection = $this->connection = $this->app['db'];
        } else {
            $this->connection = $this->app['db'] = self::$staticConnection;
        }
    }

    protected function createUser(int $userId, int $specialistId = 0, int $allianceId = 0, int $points = 0, string $discoverMask = '', string $verificationKey = ''): void
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
                'verification_key' => ':verificationKey',
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
                'verificationKey' => $verificationKey,
            ])->execute();
    }
}
