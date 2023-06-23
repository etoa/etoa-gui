<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldBannsTask;
use EtoA\User\UserRepository;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldBannsHandler implements MessageHandlerInterface
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function __invoke(RemoveOldBannsTask $task): SuccessResult
    {
        $nr = $this->userRepository->removeOldBans();

        return SuccessResult::create($nr . " abgelaufene Sperren gelöscht");
    }
}
