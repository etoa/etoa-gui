<?php

declare(strict_types=1);

namespace EtoA\User;

use BackendMessage;
use EtoA\Core\Configuration\ConfigurationService;
use Log;
use Mail;

class UserService
{
    private ConfigurationService $config;
    private UserRepository $userRepository;

    public function __construct(
        ConfigurationService $config,
        UserRepository $userRepository
    ) {
        $this->config = $config;
        $this->userRepository = $userRepository;
    }

    public function removeInactive(bool $manual = false): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);
        foreach ($inactiveUsers as $user) {
            $this->userRepository->remove($user->id);
        }

        Log::add(
            Log::F_SYSTEM,
            Log::INFO,
            count($inactiveUsers) . " inaktive User die seit " . date("d.m.Y H:i", $onlineTime) . " nicht mehr online waren oder seit " . date("d.m.Y H:i", $registerTime) . " keine Punkte haben wurden " . ($manual ? 'manuell' : '') . " gelöscht!"
        );

        return count($inactiveUsers);
    }

    public function informLongInactive(): void
    {
        $userInactiveTimeLong = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));
        $inactiveTime = time() - (24 * 3600 * $userInactiveTimeLong);

        $longInactive = $this->userRepository->findLongInactive($inactiveTime - (3600 * 24), $inactiveTime);
        foreach ($longInactive as $user) {
            $text = "Hallo " . $user->nick . "

Du hast dich seit mehr als " . $this->config->param2Int('user_inactive_days') . " Tage nicht mehr bei Escape to Andromeda (" . $this->config->get('roundname') . ") eingeloggt und
dein Account wurde deshalb als inaktiv markiert. Solltest du dich innerhalb von " . $this->config->getInt('user_inactive_days') . " Tage
nicht mehr einloggen wird der Account gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";

            $mail = new Mail('Inaktivität', $text);
            $mail->send($user->email);
        }
    }

    public function getNumInactive(): int
    {
        /** @var int $registerTime Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat */
        $registerTime = time() - (24 * 3600 * $this->config->param2Int('user_inactive_days'));

        /** @var int $onlineTime Zeit nach der ein User normalerweise gelöscht wird */
        $onlineTime = time() - (24 * 3600 * $this->config->param1Int('user_inactive_days'));

        $inactiveUsers = $this->userRepository->findInactive($registerTime, $onlineTime);

        return count($inactiveUsers);
    }

    public function removeDeleted(bool $manual = false): int
    {
        $deletedUsers = $this->userRepository->findDeleted();
        foreach ($deletedUsers as $user) {
            $this->userRepository->remove($user->id);
        }

        Log::add(
            Log::F_SYSTEM,
            Log::INFO,
            count($deletedUsers) . ' als gelöscht markierte User wurden ' . ($manual ? 'manuell' : '') . ' gelöscht!'
        );

        return count($deletedUsers);
    }

    public function setUmodToInactive(): int
    {
        $threshold = time() - ($this->config->param1Int('hmode_days') * 86400);
        $users = $this->userRepository->findInactiveInHolidayMode($threshold);
        foreach ($users as $user) {
            $hmodTime = time() - $user->hmodFrom;

            $this->userRepository->disableHolidayMode($user->id);

            $newLogoutTime = time() - ($this->config->param2Int('user_inactive_days') * 86400);
            $this->userRepository->setLogoutTime($user->id, $newLogoutTime);

            $bres = dbquery(
                "SELECT
                    buildlist_id,
                    buildlist_build_end_time,
                    buildlist_build_start_time,
                    buildlist_build_type
                FROM
                    buildlist
                WHERE
                    buildlist_build_start_time>0
                    AND buildlist_build_type>0
                    AND buildlist_user_id=" . $user->id
            );

            while ($barr = mysql_fetch_row($bres)) {
                dbquery(
                    "UPDATE
                        buildlist
                    SET
                        buildlist_build_type= 3,
                        buildlist_build_start_time=buildlist_build_start_time+" . $hmodTime . ",
                        buildlist_build_end_time=buildlist_build_end_time+" . $hmodTime . "
                    WHERE
                        buildlist_id=" . $barr[0]
                );
            }

            $tres = dbquery(
                "SELECT
                    techlist_id,
                    techlist_build_end_time,
                    techlist_build_start_time,
                    techlist_build_type
                FROM
                    techlist
                WHERE
                    techlist_build_start_time>0
                    AND techlist_build_type>0
                    AND techlist_user_id=" . $user->id
            );

            while ($tarr = mysql_fetch_row($tres)) {
                dbquery(
                    "UPDATE
                        techlist
                    SET
                        techlist_build_type=3,
                        techlist_build_start_time=techlist_build_start_time+" . $hmodTime . ",
                        techlist_build_end_time=techlist_build_end_time+" . $hmodTime . "
                    WHERE
                        techlist_id=" . $tarr[0]
                );
            }

            $sres = dbquery(
                "SELECT
                    queue_id,
                    queue_endtime,
                    queue_starttime
                FROM
                    ship_queue
                WHERE
                    queue_user_id = '" . $user->id . "'
                ORDER BY
                    queue_starttime ASC;"
            );

            while ($sarr = mysql_fetch_row($sres)) {
                dbquery(
                    "UPDATE
                        ship_queue
                    SET
                        queue_build_type = 0,
                        queue_starttime = queue_starttime + " . $hmodTime . ",
                        queue_endtime = queue_endtime + " . $hmodTime . "
                    WHERE
                        queue_id=" . $sarr[0] . ";"
                );
            }

            $dres = dbquery(
                "SELECT
                    queue_id,
                    queue_endtime,
                    queue_starttime
                FROM
                    def_queue
                WHERE
                    queue_user_id='" . $user->id . "'
                ORDER BY
                    queue_starttime ASC;"
            );

            while ($darr = mysql_fetch_row($dres)) {
                dbquery(
                    "UPDATE
                        def_queue
                    SET
                        queue_build_type=0,
                        queue_starttime=queue_starttime+" . $hmodTime . ",
                        queue_endtime=queue_endtime+" . $hmodTime . "
                    WHERE
                        queue_id=" . $darr[0] . ";"
                );
            }

            dbquery(
                "UPDATE
                    users
                SET
                    user_specialist_time=user_specialist_time+" . $hmodTime . "
                WHERE
                    user_specialist_id > 0
                    AND user_id=" . $user->id
            );

            dbquery(
                "UPDATE planets
                SET planet_last_updated=" . time() . "
                WHERE planet_user_id=" . $user->id
            );

            $pres = dbquery(
                "SELECT
                    id
                FROM
                    planets
                WHERE
                    planet_user_id=" . $user->id
            );

            while ($darr = mysql_fetch_row($pres)) {
                BackendMessage::updatePlanet($darr['id']);
            }
        }

        return mysql_affected_rows();
    }
}
