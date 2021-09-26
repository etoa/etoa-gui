<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveDeletedUsersTask;
use EtoA\User\UserService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveDeletedUsersHandler implements MessageHandlerInterface
{
    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function __invoke(RemoveDeletedUsersTask $task): SuccessResult
    {
        $nr = $this->userService->removeDeleted();

        return SuccessResult::create("$nr als gelöscht markierte User endgültig gelöscht");
    }
}
