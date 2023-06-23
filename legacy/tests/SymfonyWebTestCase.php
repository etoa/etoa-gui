<?php declare(strict_types=1);

namespace EtoA;

use Doctrine\DBAL\Connection;
use EtoA\Admin\AdminSessionRepository;
use EtoA\Admin\AdminUser;
use EtoA\Admin\AdminUserRepository;
use EtoA\Security\Admin\CurrentAdmin;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

abstract class SymfonyWebTestCase extends \Symfony\Bundle\FrameworkBundle\Test\WebTestCase
{
    use DbTestTrait;

    /**
     * @param array<int|string, mixed> $options
     * @param array<int|string, mixed> $server
     */
    protected static function createClient(array $options = [], array $server = []): KernelBrowser
    {
        $client = parent::createClient($options, $server);

        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';

        require_once dirname(__DIR__) . '/htdocs/admin/inc/admin_functions.inc.php'; // @todo remove

        return $client;
    }

    public function loginUser(int $userId): void
    {
        $loginTime = time();

        $this->getConnection()
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
            ])->executeQuery();

        $this->getConnection()
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
            ])->executeQuery();

        $_SESSION = [];
        $_SESSION['user_id'] = 1;
        $_SESSION['time_login'] = $loginTime;
        $_SESSION['time_action'] = $loginTime;
        $userAgent = $_SERVER['HTTP_USER_AGENT'] = 'testing';
        $this->getConnection()
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
            ])->executeQuery();
    }

    /**
     * @param string[] $roles
     */
    public function loginAdmin(KernelBrowser $client, array $roles = ['master']): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->nick = 'Admin';
        $adminUser->name = 'Admin';
        $adminUser->email = 'admin@example.org';
        $adminUser->roles = $roles;
        $adminUser->passwordString = '';

        $adminUserRepository = self::getContainer()->get(AdminUserRepository::class);
        $adminUserRepository->save($adminUser);
        Assert::notNull($adminUser->id);
        $adminUser = $adminUserRepository->find($adminUser->id);
        Assert::notNull($adminUser);
        Assert::notNull($adminUser->id);

        $client->loginUser(new CurrentAdmin($adminUser), 'admin');

        $sessionCookie = $client->getCookieJar()->get('MOCKSESSID');
        if ($sessionCookie === null) {
            throw new \RuntimeException('Session cookie not found.');
        }

        $adminSessionRepository = self::getContainer()->get(AdminSessionRepository::class);
        $adminSessionRepository->create($sessionCookie->getValue(), $adminUser->id, '', 'Symfony BrowserKit', time());

        return $adminUser;
    }

    protected function getConnection(): Connection
    {
        return self::getContainer()->get(Connection::class);
    }

    protected function createUser(int $userId, int $specialistId = 0, int $allianceId = 0, int $points = 0, string $discoverMask = '', string $verificationKey = ''): void
    {
        $this->getConnection()
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
            ])->executeQuery();
    }

    protected function assertStatusCode(int $statusCode, Response $response): void
    {
        $this->assertNotFalse($response->getContent());
        $this->assertSame($statusCode, $response->getStatusCode());
    }
}
