<?php

declare(strict_types=1);

namespace EtoA\Core\Log;

class BattleLog extends BaseLog
{
    /**
     * Processes the log queue and stores
     * all items in the persistend log table
     */
    static function processQueue()
    {
        dbquery("
        INSERT INTO
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
            logs_battle_queue
        ;");
        $numRecords = mysql_affected_rows();
        if ($numRecords > 0) {
            dbquery("
            DELETE FROM
                logs_battle_queue
            LIMIT
                " . $numRecords . ";");
        }
        return $numRecords;
    }

    /**
     * Removes up old logs from the persistend log table
     *
     * @param int|string $threshold All items older than this time threshold will be deleted
     */
    static function cleanup($threshold)
    {
        dbquery("
            DELETE FROM
                logs_battle
            WHERE
                timestamp<'" . $threshold . "'
        ");
        return mysql_affected_rows();
    }
}
