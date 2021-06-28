<?php

declare(strict_types=1);

namespace EtoA\Core\Logging;

use EtoA\Core\AbstractRepository;

class BattleLogRepository extends AbstractRepository
{
    public function addLogsFromQueue(): int
    {
        $this->getConnection()->beginTransaction();
        $numRecords = (int) $this->getConnection()
            ->executeStatement(
                "INSERT INTO
                    logs_battle
                (
                    `facility`,
                    `severity`,
                    `fleet_id`,
                    `user_id`,
                    `entity_user_id`,
                    `user_alliance_id`,
                    `entity_user_alliance_id`,
                    `war`,
                    `entity_id`,
                    `action`,
                    `landtime`,
                    `result`,
                    `fleet_ships_cnt`,
                    `entity_ships_cnt`,
                    `entity_defs_cnt`,
                    `fleet_weapon`,
                    `fleet_shield`,
                    `fleet_structure`,
                    `fleet_weapon_bonus`,
                    `fleet_shield_bonus`,
                    `fleet_structure_bonus`,
                    `entity_weapon`,
                    `entity_shield`,
                    `entity_structure`,
                    `entity_weapon_bonus`,
                    `entity_shield_bonus`,
                    `entity_structure_bonus`,
                    `fleet_win_exp`,
                    `entity_win_exp`,
                    `win_metal`,
                    `win_crystal`,
                    `win_pvc`,
                    `win_tritium`,
                    `win_food`,
                    `tf_metal`,
                    `tf_crystal`,
                    `tf_pvc`,
                    `timestamp`
                )
                SELECT
                    `facility`,
                    `severity`,
                    `fleet_id`,
                    `user_id`,
                    `entity_user_id`,
                    `user_alliance_id`,
                    `entity_user_alliance_id`,
                    `war`,
                    `entity_id`,
                    `action`,
                    `landtime`,
                    `result`,
                    `fleet_ships_cnt`,
                    `entity_ships_cnt`,
                    `entity_defs_cnt`,
                    `fleet_weapon`,
                    `fleet_shield`,
                    `fleet_structure`,
                    `fleet_weapon_bonus`,
                    `fleet_shield_bonus`,
                    `fleet_structure_bonus`,
                    `entity_weapon`,
                    `entity_shield`,
                    `entity_structure`,
                    `entity_weapon_bonus`,
                    `entity_shield_bonus`,
                    `entity_structure_bonus`,
                    `fleet_win_exp`,
                    `entity_win_exp`,
                    `win_metal`,
                    `win_crystal`,
                    `win_pvc`,
                    `win_tritium`,
                    `win_food`,
                    `tf_metal`,
                    `tf_crystal`,
                    `tf_pvc`,
                    `timestamp`
                FROM
                    logs_battle_queue;"
            );
        if ($numRecords > 0) {
            $this->getConnection()
                ->executeStatement(
                    "DELETE FROM
                        logs_battle_queue
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
            ->delete('logs_battle')
            ->where('timestamp < :threshold')
            ->setParameters([
                'threshold' => $threshold,
            ])
            ->execute();
    }
}
