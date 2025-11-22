<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\ReportCleanup;
use EtoA\Message\ReportRepository;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldReportsTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldReportsHandler implements MessageHandlerInterface
{
    public function __construct(
        private readonly ReportCleanup $reportCleanup
    )
    {}

    public function __invoke(RemoveOldReportsTask $task): SuccessResult
    {
        $nr = $this->reportCleanup->cleanup($task->threshold??0,$task->onlyDeleted);

        return SuccessResult::create("$nr alte Berichte gelöscht");
    }
}
