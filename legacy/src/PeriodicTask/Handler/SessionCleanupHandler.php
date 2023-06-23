<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Admin\AdminSessionManager;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\SessionCleanupTask;
use EtoA\User\UserSessionManager;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SessionCleanupHandler implements MessageHandlerInterface
{
    private UserSessionManager $userSessionManager;
    private AdminSessionManager $adminSessionManager;

    public function __construct(UserSessionManager $userSessionManager, AdminSessionManager $adminSessionManager)
    {
        $this->userSessionManager = $userSessionManager;
        $this->adminSessionManager = $adminSessionManager;
    }

    public function __invoke(SessionCleanupTask $task): SuccessResult
    {
        $this->userSessionManager->cleanup();
        $this->adminSessionManager->cleanup();

        return SuccessResult::create("Session cleanup");
    }
}
