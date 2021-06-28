<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use EtoA\Core\AbstractRepository;

class FleetLogRepository extends AbstractRepository
{
    public function addToQueue(FleetLogEntry $entry): void
    {
        $this->getConnection()
            ->executeStatement(
                "INSERT DELAYED INTO
                    logs_fleet_queue
                (
                    `fleet_id`,
                    `facility`,
                    `timestamp`,
                    `message`,
                    `user_id`,
                    `entity_user_id`,
                    `entity_from`,
                    `entity_to`,
                    `launchtime`,
                    `landtime`,
                    `action`,
                    `status`,
                    `fleet_res_start`,
                    `fleet_res_end`,
                    `fleet_ships_start`,
                    `fleet_ships_end`,
                    `entity_res_start`,
                    `entity_res_end`,
                    `entity_ships_start`,
                    `entity_ships_end`
                ) VALUES (
                    '" . $entry->fleetId . "',
                    '" . $entry->facility . "',
                    '" . time() . "',
                    '" . $entry->text . "',
                    '" . $entry->userId . "',
                    '" . $entry->userId . "',
                    '" . $entry->sourceId . "',
                    '" . $entry->targetId . "',
                    '" . $entry->launchtime . "',
                    '" . $entry->landtime . "',
                    '" . $entry->action . "',
                    '" . $entry->status . "',
                    '" . $entry->fleetResStart . "',
                    '" . $entry->fleetResEnd . "',
                    '" . $entry->fleetShipStart . "',
                    '" . $entry->fleetShipEnd . "',
                    '" . $entry->entityResStart . "',
                    '" . $entry->entityResEnd . "',
                    '" . $entry->entityShipStart . "',
                    '" . $entry->entityShipEnd . "'
                );",
                []
            );
    }

    public function addLogsFromQueue(): int
    {
        $this->getConnection()->beginTransaction();
        $numRecords = (int) $this->getConnection()
            ->executeStatement(
                "INSERT INTO
                    logs_fleet
                (
                    `fleet_id`,
                    `facility`,
                    `timestamp`,
                    `message`,
                    `user_id`,
                    `entity_user_id`,
                    `entity_from`,
                    `entity_to`,
                    `launchtime`,
                    `landtime`,
                    `action`,
                    `status`,
                    `fleet_res_start`,
                    `fleet_res_end`,
                    `fleet_ships_start`,
                    `fleet_ships_end`,
                    `entity_res_start`,
                    `entity_res_end`,
                    `entity_ships_start`,
                    `entity_ships_end`
                )
                SELECT
                    `fleet_id`,
                    `facility`,
                    `timestamp`,
                    `message`,
                    `user_id`,
                    `entity_user_id`,
                    `entity_from`,
                    `entity_to`,
                    `launchtime`,
                    `landtime`,
                    `action`,
                    `status`,
                    `fleet_res_start`,
                    `fleet_res_end`,
                    `fleet_ships_start`,
                    `fleet_ships_end`,
                    `entity_res_start`,
                    `entity_res_end`,
                    `entity_ships_start`,
                    `entity_ships_end`
                FROM
                    logs_fleet_queue"
            );
        if ($numRecords > 0) {
            $this->getConnection()
                ->executeStatement(
                    "DELETE FROM
                        logs_fleet_queue
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
            ->delete('logs_fleet')
            ->where('timestamp < :threshold')
            ->setParameters([
                'threshold' => $threshold,
            ])
            ->execute();
    }
}
