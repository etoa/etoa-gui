<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use EtoA\Core\AbstractRepository;

class GameLogRepository extends AbstractRepository
{
    public function addToQueue(
        int $facility,
        int $severity,
        string $message,
        string $ip,
        int $userId,
        int $allianceId,
        int $entityId,
        int $objectId,
        int $status,
        int $level
    ): void {
        $this->getConnection()
            ->executeStatement(
                "INSERT DELAYED INTO
                    logs_game_queue
                (
                    facility,
                    severity,
                    timestamp,
                    message,
                    ip,
                    user_id,
                    alliance_id,
                    entity_id,
                    object_id,
                    status,
                    level
                )
                VALUES
                (
                    :facility,
                    :severity,
                    :timestamp,
                    :message,
                    :ip,
                    :user,
                    :alliance,
                    :entity,
                    :object,
                    :status,
                    :level
                );",
                [
                    'facility' => $facility,
                    'severity' => $severity,
                    'timestamp' => time(),
                    'message' => $message,
                    'ip' => $ip,
                    'user' => $userId,
                    'alliance' => $allianceId,
                    'entity' => $entityId,
                    'object' => $objectId,
                    'status' => $status,
                    'level' => $level,
                ]
            );
    }

    public function addLogsFromQueue(): int
    {
        $this->getConnection()->beginTransaction();
        $numRecords = $this->getConnection()
            ->executeStatement(
                "INSERT INTO
                    logs_game
                (
                    facility,
                    severity,
                    timestamp,
                    message,
                    ip,
                    user_id,
                    alliance_id,
                    entity_id,
                    object_id,
                    status,
                    level
                )
                SELECT
                    facility,
                    severity,
                    timestamp,
                    message,
                    ip,
                    user_id,
                    alliance_id,
                    entity_id,
                    object_id,
                    status,
                    level
                FROM
                    logs_game_queue
                ;"
            );
        if ($numRecords > 0) {
            $this->getConnection()
                ->executeStatement(
                    "DELETE FROM
                        logs_game_queue
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
            ->delete('logs_game')
            ->where('timestamp < :threshold')
            ->setParameters([
                'threshold' => $threshold,
            ])
            ->execute();
    }
}
