<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use EtoA\Core\AbstractRepository;

class LogRepository extends AbstractRepository
{
    public function addToQueue(int $facility, int $severity, string $message, string $ip): void
    {
        $this->getConnection()
            ->executeStatement(
                "INSERT DELAYED INTO
                    logs_queue
                (
                    facility,
                    severity,
                    timestamp,
                    ip,
                    message
                )
                VALUES
                (
                    :facility,
                    :severity,
                    :timestamp
                    :ip,
                    :message
                );",
                [
                    'facility' => $facility,
                    'severity' => $severity,
                    'timestamp' => time(),
                    'ip' => $ip,
                    'message' => $message,
                ]
            );
    }

    public function addLogsFromQueue(): int
    {
        $this->getConnection()->beginTransaction();
        $numRecords = (int) $this->getConnection()
            ->executeStatement(
                "INSERT INTO
                    logs
                (
                    facility,
                    severity,
                    timestamp,
                    ip,
                    message
                )
                SELECT
                    facility,
                    severity,
                    timestamp,
                    ip,
                    message
                FROM
                    logs_queue;"
            );
        if ($numRecords > 0) {
            $this->getConnection()
                ->executeStatement(
                    "DELETE FROM
                        logs_queue
                    LIMIT
                        :num;",
                    [
                        'num' => $numRecords,
                    ]
                );
        }
        $this->getConnection()->commit();

        return $numRecords;
    }

    public function removeByTimestamp(int $threshold): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('logs')
            ->where('timestamp < :threshold')
            ->setParameters([
                'threshold' => $threshold,
            ])
            ->execute();
    }
}
