<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\CreateUserBannerTask;
use EtoA\Ranking\UserBannerService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class CreateUserBannerHandler implements MessageHandlerInterface
{
    private UserBannerService $userBannerService;

    public function __construct(UserBannerService $userBannerService)
    {
        $this->userBannerService = $userBannerService;
    }

    public function __invoke(CreateUserBannerTask $task): SuccessResult
    {
        $this->userBannerService->createUserBanner();

        return SuccessResult::create("User Banner erstellt");
    }
}
