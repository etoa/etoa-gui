<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\UpdateUserTitlesTask;
use EtoA\Ranking\UserTitlesService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class UpdateUserTitlesHandler implements MessageHandlerInterface
{
    private UserTitlesService $userTitlesService;

    public function __construct(UserTitlesService $userTitlesService)
    {
        $this->userTitlesService = $userTitlesService;
    }

    public function __invoke(UpdateUserTitlesTask $task): SuccessResult
    {
        $this->userTitlesService->calcTitles();

        return SuccessResult::create("User Titel aktualisiert");
    }
}
