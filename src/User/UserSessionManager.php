<?php

declare(strict_types=1);

namespace EtoA\User;

use EtoA\Core\Configuration\ConfigurationService;
use Log;

class UserSessionManager
{
    private ConfigurationService $config;

    public function __construct(
        ConfigurationService $config
    ) {
        $this->config = $config;
    }

    public function unregisterSession(string $sessionId = null, bool $logoutPressed = true): void
    {
        if ($sessionId == null) {
            $sessionId = session_id();
        }

        $res = dbquery("
        SELECT
            *
        FROM
            `user_sessions`
        WHERE
            id='" . $sessionId . "'
        ;");
        if (mysql_num_rows($res) > 0) {
            $arr = mysql_fetch_assoc($res);
            dbquery("
            INSERT INTO
                `user_sessionlog`
            (
                `session_id` ,
                `user_id`,
                `ip_addr`,
                `user_agent`,
                `time_login`,
                `time_action`,
                `time_logout`
            )
            VALUES
            (
                '" . $arr['id'] . "',
                '" . $arr['user_id'] . "',
                '" . $arr['ip_addr'] . "',
                '" . $arr['user_agent'] . "',
                '" . $arr['time_login'] . "',
                '" . $arr['time_action'] . "',
                '" . ($logoutPressed == 1 ? time() : 0) . "'
            )
            ");
            dbquery("
            DELETE FROM
                `user_sessions`
            WHERE
                id='" . $sessionId . "'
            ;");

            dbquery("
                    UPDATE
                        users
                    SET
                        user_logouttime='" . time() . "'
                    WHERE
                        user_id='" . $arr['user_id'] . "'
                    LIMIT 1;");
        }
        if ($logoutPressed) {
            session_regenerate_id(true);
            session_destroy();
        }
    }

    public function cleanup(): void
    {
        $res = dbquery("
        SELECT
            id
        FROM
            `user_sessions`
        WHERE
            time_action+" . $this->config->getInt('user_timeout') . " < '" . time() . "'
        ;");
        if (mysql_num_rows($res) > 0) {
            while ($arr = mysql_fetch_row($res)) {
                $this->unregisterSession($arr[0], false);
            }
        }
    }

    public function cleanupLogs(int $threshold = 0): int
    {
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $this->config->param1Int('sessionlog_store_days'));

        dbquery("
        DELETE FROM
            `user_sessionlog`
        WHERE
            time_action < " . $timestamp . ";");
        $count = mysql_affected_rows();

        Log::add(Log::F_SYSTEM, Log::INFO, "$count Usersession-Logs die älter als " . date("d.m.Y, H:i", $timestamp) . " sind wurden gelöscht.");

        return $count;
    }

    public function kick(string $sessionId): void
    {
        $this->unregisterSession($sessionId, false);
    }
}
