<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Admin\AdminSessionManager;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CleanupSessionLogsTask;
use EtoA\User\UserSessionManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CleanupSessionLogsHandler implements MessageHandlerInterface
{
    private UserSessionManager $userSessionManager;
    private AdminSessionManager $adminSessionManager;

    public function __construct(UserSessionManager $userSessionManager, AdminSessionManager $adminSessionManager)
    {
        $this->userSessionManager = $userSessionManager;
        $this->adminSessionManager = $adminSessionManager;
    }

    public function __invoke(CleanupSessionLogsTask $task): SuccessResult
    {
        $userSessions = $this->userSessionManager->cleanupLogs();
        $adminSessions = $this->adminSessionManager->cleanupLogs();

        return SuccessResult::create("$userSessions alte Spieler Session-Logs gelöscht, $adminSessions alte Admin Session-Logs gelöscht");
    }
}
