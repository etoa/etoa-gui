<?php declare(strict_types=1);

namespace EtoA;

use Doctrine\DBAL\Connection;

abstract class SymfonyWebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    use DbTestTrait;

    protected static Connection $staticConnection;

    protected static function createClient(array $options = [], array $server = [])
    {
        $client = parent::createClient($options, $server);

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        self::$staticConnection = $client->getContainer()->get(Connection::class);

        return $client;
    }

    public function loginUser(int $userId): void
    {
        $loginTime = time();

        self::$staticConnection
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
                'discoverymask' => ':empty',
                'dual_email' => ':empty',
                'dual_name' => ':empty',
            ])->setParameters([
                'userId' => $userId,
                'setup' => 1,
                'name' => 'User Name',
                'nick' => 'Nickname',
                'password' => 'password',
                'session' => 'session',
                'email' => 'test@etoa.net',
                'empty' => '',
            ])->execute();

        self::$staticConnection
            ->createQueryBuilder()
            ->insert('tutorial_user_progress')
            ->values([
                'tup_user_id' => ':userId',
                'tup_tutorial_id' => ':tutorialId',
                'tup_closed' => ':closed',
            ])->setParameters([
                'userId' => $userId,
                'tutorialId' => 2,
                'closed' => 1,
            ])->execute();

        $_SESSION = [];
        $_SESSION['user_id'] = 1;
        $_SESSION['time_login'] = $loginTime;
        $_SESSION['time_action'] = $loginTime;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] = 'testing';
        self::$staticConnection
            ->createQueryBuilder()
            ->insert('user_sessions')
            ->values([
                'id' => ':sessionId',
                'user_id' => ':userId',
                'time_login' => ':loginTime',
                'user_agent' => ':userAgent',
                'ip_addr' => ':ip',
            ])->setParameters([
                'sessionId' => session_id(),
                'userId' => $userId,
                'loginTime' => $loginTime,
                'userAgent' => $userAgent,
                'ip' => '',
            ])->execute();
    }
}
