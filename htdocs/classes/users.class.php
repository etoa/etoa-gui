<?PHP

use EtoA\Core\Configuration\ConfigurationService;

class Users
{
    /**
    * Remove inactive users
    */
    static function removeInactive($manual=false)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $now = time();

        $register_time = $now - (24 * 3600 * $config->param2Int('user_inactive_days'));        // Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat
        $online_time = $now - (24 * 3600 * $config->param1Int('user_inactive_days'));    // Zeit nach der ein User normalerweise gelöscht wird
        $user_inactive_time_long = time() - (24 * 3600 * $config->param2Int('user_inactive_days'));
        $inactive_time = $now - (24 * 3600 * $user_inactive_time_long);

        $res =	dbquery("
            SELECT
                user_id
            FROM
                users
            WHERE
                user_ghost='0'
                AND admin=0
                AND `user_blocked_to`<'".$now."'
                AND ((user_registered<'".$register_time."' AND user_points='0')
                OR (user_logouttime<'".$online_time."' AND user_logouttime>0 AND user_hmode_from='0'));
        ");
        $nr = mysql_num_rows($res);
        if ($nr>0)
        {
            while ($arr=mysql_fetch_assoc($res))
            {
                $usr = new User($arr['user_id']);
                $usr->delete();
            }
        }
        if ($manual)
            Log::add("4", Log::INFO, mysql_num_rows($res)." inaktive User die seit ".date("d.m.Y H:i",$online_time)." nicht mehr online waren oder seit ".date("d.m.Y H:i",$register_time)." keine Punkte haben wurden manuell gelöscht!");
        else
            Log::add("4", Log::INFO, mysql_num_rows($res)." inaktive User die seit ".date("d.m.Y H:i",$online_time)." nicht mehr online waren oder seit ".date("d.m.Y H:i",$register_time)." keine Punkte haben wurden gelöscht!");

        // Nachricht an lange inaktive
        $res =	dbquery("
            SELECT
                user_id,
                user_nick,
                user_email
            FROM
                users
            WHERE
                user_ghost='0'
                AND admin=0
                AND `user_blocked_to`<'".$now."'
                AND user_logouttime<'".$inactive_time."'
                AND user_logouttime>'".($inactive_time-3600*24)."'
                AND user_hmode_from='0';
        ");
        if (mysql_num_rows($res)>0)
        {
            while ($arr=mysql_fetch_assoc($res))
            {
                $text ="Hallo ".$arr['user_nick']."

Du hast dich seit mehr als ".USER_INACTIVE_LONG." Tage nicht mehr bei Escape to Andromeda (".$config->get('roundname').") eingeloggt und
dein Account wurde deshalb als inaktiv markiert. Solltest du dich innerhalb von ".USER_INACTIVE_SHOW." Tage
nicht mehr einloggen wird der Account gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";
                $mail = new Mail('Inaktivität',$text);
                $mail->send($arr['user_email']);
            }
        }

        return $nr;
    }

    /**
    * Gets the number of inactive users
    */
    static function getNumInactive()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $now = time();

        $register_time = time() - (24 * 3600 * $config->param2Int('user_inactive_days'));        // Zeit nach der ein User gelöscht wird wenn er noch 0 Punkte hat
        $online_time = time() - (24 * 3600 * $config->param1Int('user_inactive_days'));    // Zeit nach der ein User normalerweise gelöscht wird

        $res =	dbquery("
            SELECT
                COUNT(user_id)
            FROM
                users
            WHERE
                user_ghost='0'
                AND admin=0
                AND `user_blocked_to`<'".$now."'
                AND ((user_registered<'".$register_time."' AND user_points='0')
                OR (user_logouttime<'".$online_time."' AND user_logouttime>0 AND user_hmode_from='0'));
        ");
        $arr = mysql_fetch_row($res);
        return $arr[0];
    }

    /**
    * Delete user marked as delete
    */
    static function removeDeleted($manual=false)
    {
        $res =	dbquery("
            SELECT
                user_id
            FROM
                users
            WHERE
                user_deleted>0 && user_deleted<".time()."
        ");
        if (mysql_num_rows($res)>0)
        {
            while ($arr=mysql_fetch_assoc($res))
            {
                $usr = new User($arr['user_id']);
                $usr->delete();
            }
        }
        if ($manual)
            Log::add("4", Log::INFO, mysql_num_rows($res)." als gelöscht markierte User wurden manuell gelöscht!");
        else
            Log::add("4", Log::INFO, mysql_num_rows($res)." als gelöscht markierte User wurden gelöscht!");
        return mysql_num_rows($res);
    }

    /**
    * Abgelaufene Sperren löschen
    *
    */
    static function removeOldBanns()
    {
        dbquery("
            UPDATE
                users
            SET
                user_blocked_from='0',
                user_blocked_to='0',
                user_ban_reason='',
                user_ban_admin_id='0'
            WHERE
                user_blocked_to<'".time()."';
        ");
    }

    /**
    * Spionageangriffscounter auf 0 setzen
    * @deprecated altes Balancing
    */
    static function resetSpyattacks()
    {
        dbquery("
                UPDATE
                    users
                SET
                    spyattack_counter='0';
        ");
    }

    static function getArray()
    {
        $res = dbquery("SELECT user_id,user_nick FROM users;");
        $rtn = array();
        while ($arr=mysql_fetch_row($res))
        {
            $rtn[$arr[0]] = $arr[1];
        }
        return $rtn;
    }

    static function addSittingDays(int $days = 0)
    {
        if ($days == 0)
        {
            // TODO
            global $app;

            /** @var ConfigurationService */
            $config = $app[ConfigurationService::class];

            $days = $config->param1Int("user_sitting_days");
        }

        dbquery("UPDATE `users` SET `user_sitting_days`=`user_sitting_days`+'".$days."';");
    }

    // check for $conf['hmode_days']['p2'] BEFORE calling this function
    static function setUmodToInactive()
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        $now = time();

        // set all users who are inactive

        $res = dbquery("SELECT
                            user_id,
                            user_hmode_from
                        FROM
                            users
                        WHERE
                                user_ghost=0
                        AND
                            admin=0
                        AND
                            user_blocked_to <".$now
                        ." AND
                            user_hmode_from > 0
                        AND
                            user_hmode_from<".(time()-$config->param1Int('hmode_days')*86400));

        while ($arr=mysql_fetch_row($res))
        {  	$hmodTime = time() - $arr[1];
            dbquery("UPDATE
                            users
                    SET
                        user_hmode_from=0,
                        user_hmode_to=0,
                        user_logouttime=".(time()-USER_INACTIVE_LONG*86400)."
                    WHERE
                        user_id=".$arr[0]);


            $bres = dbquery("
                            SELECT
                                buildlist_id,
                                buildlist_build_end_time,
                                buildlist_build_start_time,
                                buildlist_build_type
                            FROM
                                buildlist
                            WHERE
                                buildlist_build_start_time>0
                                AND buildlist_build_type>0
                                AND buildlist_user_id=".$arr[0]);

            while ($barr=mysql_fetch_row($bres))
            {
                dbquery("UPDATE
                            buildlist
                        SET
                                buildlist_build_type= 3,
                            buildlist_build_start_time=buildlist_build_start_time+".$hmodTime
                            .",buildlist_build_end_time=buildlist_build_end_time+".$hmodTime
                        ." WHERE
                            buildlist_id=".$barr[0]);
                }

            $tres = dbquery("
                            SELECT
                                techlist_id,
                                techlist_build_end_time,
                                techlist_build_start_time,
                                techlist_build_type
                            FROM
                                techlist
                            WHERE
                                techlist_build_start_time>0
                                AND techlist_build_type>0
                                AND techlist_user_id=".$arr[0]);

            while ($tarr=mysql_fetch_row($tres))
            {
                dbquery("UPDATE
                            techlist
                        SET
                            techlist_build_type=3,
                            techlist_build_start_time=techlist_build_start_time+".$hmodTime
                            .",techlist_build_end_time=techlist_build_end_time+".$hmodTime
                        ." WHERE
                            techlist_id=".$tarr[0]);
            }

            $sres = dbquery("SELECT
                                queue_id,
                                queue_endtime,
                                queue_starttime
                                FROM
                                    ship_queue
                            WHERE
                                queue_user_id='".$tarr[0]."'
                            ORDER BY
                                queue_starttime ASC;");

            while ($sarr=mysql_fetch_row($sres))
            {
                dbquery("UPDATE
                            ship_queue
                        SET
                            queue_build_type=0,
                            queue_starttime=queue_starttime+".$hmodTime
                            .",queue_endtime=queue_endtime+".$hmodTime
                        ." WHERE
                            queue_id=".$sarr[0].";");
            }

            $dres = dbquery("SELECT
                                    queue_id,
                                    queue_endtime,
                                    queue_starttime
                                    FROM
                                        def_queue
                                WHERE
                                    queue_user_id='".$tarr[0]."'
                                ORDER BY
                                    queue_starttime ASC;");

            while ($darr=mysql_fetch_row($dres))
            {
                dbquery("UPDATE
                            def_queue
                        SET
                            queue_build_type=0,
                            queue_starttime=queue_starttime+".$hmodTime
                            .",queue_endtime=queue_endtime+".$hmodTime
                        ." WHERE
                            queue_id=".$darr[0].";");
            }

            dbquery("
                UPDATE
                        users
                SET
                        user_specialist_time=user_specialist_time+".$hmodTime
                ." WHERE
                        user_specialist_id > 0
                    AND user_id=".$arr[0]);

            dbquery ("UPDATE planets SET planet_last_updated=".time()." WHERE planet_user_id=".$arr[0]);

            $pres = dbquery("SELECT
                                    id
                                    FROM
                                        planets
                                WHERE
                                    planet_user_id=".$arr[0]);

            while ($darr=mysql_fetch_row($pres))
            {
                BackendMessage::updatePlanet($darr['id']);
            }
            };

        return mysql_affected_rows();
    }
}
