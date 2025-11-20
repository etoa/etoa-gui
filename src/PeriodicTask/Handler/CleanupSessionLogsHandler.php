<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Admin\AdminSessionManager;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CleanupSessionLogsTask;
use EtoA\User\UserSessionManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CleanupSessionLogsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly UserSessionManager $userSessionManager,
        private readonly AdminSessionManager $adminSessionManager
    )
    {}

    public function __invoke(CleanupSessionLogsTask $task): SuccessResult
    {
        $userSessions = $this->userSessionManager->cleanupLogs();
        $adminSessions = $this->adminSessionManager->cleanupLogs();

        return SuccessResult::create("$userSessions alte Spieler Session-Logs gelöscht, $adminSessions alte Admin Session-Logs gelöscht");
    }
}
