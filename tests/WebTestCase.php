<?php declare(strict_types=1);

namespace EtoA;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Silex\Application;
use Symfony\Component\HttpKernel\Client;

abstract class WebTestCase extends TestCase
{
    use DbTestTrait;

    /** @var Connection */
    protected $connection;
    /** @var Application */
    protected $app;

    protected function setUp(): void
    {
        $this->app = $this->createApplication();
    }

    public function createApplication(): Application
    {
        include_once dirname(__DIR__) . '/htdocs/inc/mysqli_polyfill.php';
        $app = $this->setupApplication();
        $this->connection = $app['db'];
        \mysql_connect($this->connection->getHost(), $this->connection->getUsername(), $this->connection->getPassword(), $this->connection->getDatabase());

        require_once __DIR__ . '/../htdocs/inc/bootstrap.inc.php';
        \Config::restoreDefaults();

        return $app;
    }

    public function createClient(array $server = []): Client
    {
        if (!class_exists('Symfony\Component\BrowserKit\Client')) {
            throw new \LogicException('Component "symfony/browser-kit" is required by WebTestCase.'.PHP_EOL.'Run composer require symfony/browser-kit');
        }

        return new Client($this->app, $server);
    }

    public function loginUser(int $userId): void
    {
        $loginTime = time();

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

        $this->connection
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
        $this->connection
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
