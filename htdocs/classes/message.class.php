<?PHP

use EtoA\Core\Configuration\ConfigurationService;

class Message
{
    /**
    * Check for new messages
    */
    static function checkNew($user_id)
    {
        $mres = dbquery("
            SELECT
                COUNT(message_id)
            FROM
                messages
            WHERE
                message_deleted='0'
                AND message_user_to='".$user_id."'
                AND message_read='0';
        ");
        $count=mysql_fetch_row($mres);
        return $count[0];
    }

    /**
    * Delete message with given id
    */
    static function delete($id)
    {
        dbquery("
            DELETE FROM
                messages
            WHERE
                message_id=".$id.";");
    }

    /**
    * Alte Nachrichten löschen
    */
    static function removeOld($threshold=0,$onlyDeleted=0)
    {
        // TODO
        global $app;

        /** @var ConfigurationService */
        $config = $app['etoa.config.service'];

        $nr = 0;
        if ($onlyDeleted==0)
        {
            // Normal old messages
            $timestamp = $threshold > 0
                 ? time() - $threshold
                 : time() - (24 * 3600 * $config->getInt('messages_threshold_days'));

            $res = dbquery("
                SELECT
                    message_id
                 FROM
                    messages
                WHERE
                    message_archived=0
                    AND message_read=1
                    AND message_timestamp<'".$timestamp."';
            ");
            if (mysql_num_rows($res)>0)
            {
                $ids = array();
                while ($arr=mysql_fetch_row($res))
                {
                    array_push($ids,$arr[0]);
                }

                dbquery("
                    DELETE FROM
                        message_data
                    WHERE
                        id IN (".implode(",",$ids),");
                ");
            }
            dbquery("
                DELETE FROM
                    messages
                WHERE
                    message_archived=0
                    AND message_read=1
                    AND message_timestamp<'".$timestamp."';
            ");
            $nr = mysql_affected_rows();
            Log::add(Log::F_SYSTEM, Log::INFO, "Unarchivierte Nachrichten die älter als ".date("d.m.Y H:i",$timestamp)." sind wurden gelöscht!");
        }

        // Deleted
        $timestamp = $threshold > 0
            ? time() - $threshold
            : time() - (24 * 3600 * $config->param1Int('messages_threshold_days'));

        $res = dbquery("
            SELECT
                message_id
             FROM
                messages
            WHERE
                message_deleted='1'
                AND message_timestamp<'".$timestamp."';
        ");
        if (mysql_num_rows($res)>0)
        {
            $ids = array();
            while ($arr=mysql_fetch_row($res))
            {
                array_push($ids,$arr[0]);
            }

            dbquery("
                DELETE FROM
                    message_data
                WHERE
                    id IN (".implode(",",$ids),");
            ");
        }
        $res = dbquery("
            DELETE FROM
                messages
            WHERE
                message_deleted='1'
                AND message_timestamp<'".$timestamp."';
        ");
        Log::add(Log::F_SYSTEM, Log::INFO, "Unarchivierte Nachrichten die älter als ".date("d.m.Y H:i",$timestamp)." sind wurden gelöscht!");
        $nr += mysql_affected_rows();
        return $nr;
    }
}
