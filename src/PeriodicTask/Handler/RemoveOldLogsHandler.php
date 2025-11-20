<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Log\LogCleanup;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldLogsTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldLogsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly LogCleanup $logCleanup
    )
    {}

    public function __invoke(RemoveOldLogsTask $task): SuccessResult
    {
        $nr = $this->logCleanup->cleanup($task->threshold??0);

        return SuccessResult::create("$nr alte Logs gelöscht");
    }
}
