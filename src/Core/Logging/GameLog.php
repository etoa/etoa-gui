<?php

declare(strict_types=1);

namespace EtoA\Core\Log;

class GameLog extends BaseLog
{
    // Facilities

    /**
     * Others
     */
    const F_OTHER = 0;

    /**
     * Buildings 1
     */
    const F_BUILD = 1;
    const F_TECH = 2;
    const F_SHIP = 3;
    const F_DEF = 4;
    const F_QUESTS = 5;

    static public $facilities = array(
        "Sonstiges",
        "Gebäude",
        "Forschungen",
        "Schiffe",
        "Verteidigungsanlagen",
        "Quests",
    );

    static function add($facility, $severity, $msg, $userId, $allianceId, $entityId, $objectId = 0, $status = 0, $level = 0)
    {
        if (!is_numeric($facility) || $facility < 0 || $facility > 5) {
            $facility = self::F_OTHER;
        }
        if (!is_numeric($severity) || $severity < 0 || $severity > 4) {
            $severity = self::INFO;
        }
        if ($severity > self::DEBUG || isDebugEnabled()) {
            //Speichert Log
            dbquery("
            INSERT DELAYED INTO
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
                " . $facility . ",
                " . $severity . ",
                '" . time() . "',
                '" . addslashes($msg) . "',
                '" . $_SERVER['REMOTE_ADDR'] . "',
                '" . intval($userId) . "',
                '" . intval($allianceId) . "',
                '" . intval($entityId) . "',
                '" . intval($objectId) . "',
                '" . intval($status) . "',
                '" . intval($level) . "'
            );");
        }
    }

    /**
     * Processes the log queue and stores
     * all items in the persistend log table
     */
    static function processQueue()
    {
        dbquery("
        INSERT INTO
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
        ;");
        $numRecords = mysql_affected_rows();
        if ($numRecords > 0) {
            dbquery("
            DELETE FROM
                logs_game_queue
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
                logs_game
            WHERE
                timestamp<'" . $threshold . "'
        ");
        return mysql_affected_rows();
    }
}
