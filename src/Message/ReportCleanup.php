<?php declare(strict_types=1);

namespace EtoA\Message;

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;

class ReportCleanup
{
    public function __construct(
        private ConfigurationService $config,
        private ReportRepository $reportRepository,
        private LogRepository $logRepository,
    ) {
    }

    public function cleanup(int $threshold = 0, bool $onlyDeleted = false): int
    {
        $nr = 0;
        if (!$onlyDeleted) {
            // Normal old messages
            $timestamp = $threshold > 0
                ? time() - $threshold
                : time() - (24 * 3600 * $this->config->getInt('reports_threshold_days'));

            $nr = $this->reportRepository->removeUnarchivedread($timestamp);
            $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");
        }

        // Deleted
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->param1Int('reports_threshold_days'));

        $nr += $this->reportRepository->removeDeleted($timestamp);
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "Unarchivierte Berichte die älter als " . date("d.m.Y H:i", $timestamp) . " sind wurden gelöscht!");

        return $nr;
    }
}
