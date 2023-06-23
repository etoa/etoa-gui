<?php declare(strict_types=1);

namespace EtoA\PeriodicTask\Handler;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Message\ReportRepository;
use EtoA\PeriodicTask\Result\SuccessResult;
use EtoA\PeriodicTask\Task\RemoveOldReportsTask;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class RemoveOldReportsHandler implements MessageHandlerInterface
{
    private ConfigurationService $config;
    private ReportRepository $reportRepository;
    private LogRepository $logRepository;

    public function __construct(ConfigurationService $config, ReportRepository $reportRepository, LogRepository $logRepository)
    {
        $this->config = $config;
        $this->reportRepository = $reportRepository;
        $this->logRepository = $logRepository;
    }

    public function __invoke(RemoveOldReportsTask $task): SuccessResult
    {
        $time = time();

        $nr = 0;
        if (!$task->onlyDeleted) {
            // Normal old messages
            $timestamp = $task->threshold > 0
                ? $time - $task->threshold
                : $time - (24 * 3600 * $this->config->getInt('reports_threshold_days'));

            $nr = $this->reportRepository->removeUnarchivedread($timestamp);
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");
        }

        // Deleted
        $timestamp = $task->onlyDeleted > 0
            ? $time - $task->threshold
            : $time - (24 * 3600 * $this->config->param1Int('reports_threshold_days'));

        $nr += $this->reportRepository->removeDeleted($timestamp);
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return SuccessResult::create("$nr alte Berichte gelöscht");
    }
}
