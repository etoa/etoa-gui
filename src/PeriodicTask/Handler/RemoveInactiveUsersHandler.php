<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveInactiveUsersTask;
use EtoA\User\UserService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveInactiveUsersHandler implements MessageHandlerInterface
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(RemoveInactiveUsersTask $task): SuccessResult
    {
        $nr = $this->userService->removeInactive();

        $this->userService->informLongInactive();

        return SuccessResult::create("$nr inaktive User gelöscht");
    }
}
