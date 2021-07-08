<?PHP

use EtoA\Core\Configuration\ConfigurationService;

class Users
{
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
