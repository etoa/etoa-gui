<?php declare(strict_types=1);

namespace EtoA\Log;

use EtoA\Core\AbstractRepository;

class GameLogRepository extends AbstractRepository
{
    /**
     * @return GameLog[]
     */
    public function searchLogs(GameLogSearch $search, int $limit = null, int $offset = null): array
    {
        $data = $this->applySearchSortLimit($this->createQueryBuilder(), $search, null, $limit, $offset)
            ->select('logs_game.*')
            ->from('logs_game')
            ->orderBy('timestamp', 'DESC')
            ->execute()
            ->fetchAllAssociative();

        return array_map(fn (array $row) => new GameLog($row), $data);
    }

    public function add(int $facility, int $severity, string $message, int $userId, int $allianceId, int $entityId, int $objectId = 0, int $status = 0, int $level = 0): void
    {
        $this->getConnection()->executeQuery('INSERT DELAYED INTO logs_game (
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
			) VALUES (
                :facility,
                :severity,
                :timestamp,
                :message,
                :ip,
                :userId,
                :allianceId,
                :entityId,
                :objectId,
                :status,
                :level
            )', [
            'facility' => $facility,
            'severity' => $severity,
            'timestamp' => time(),
            'message' => $message,
            'ip' => $_SERVER['REMOTE_ADDR'],
            'userId' => $userId,
            'allianceId' => $allianceId,
            'entityId' => $entityId,
            'objectId' => $objectId,
            'status' => $status,
            'level' => $level,
        ]);
    }

    public function cleanup(int $threshold): int
    {
        return (int) $this->createQueryBuilder()
            ->delete('logs_game')
            ->where('timestamp < :threshold')
            ->setParameter('threshold', $threshold)
            ->execute();
    }

    public function count(GameLogSearch $search = null): int
    {
        return (int) $this->applySearchSortLimit($this->createQueryBuilder(), $search)
            ->select('COUNT(*)')
            ->from('logs_game')
            ->execute()
            ->fetchOne();
    }
}
