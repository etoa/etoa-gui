<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\Logging\Log;

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
    * Sends a message from an user to another user
    */
    static function sendFromUserToUser($senderId,$receiverId,$subject,$text,$cat=0,$fleetId=0)
    {
        try {
            if ($cat == 0) {
                $cat = USER_MSG_CAT_ID;
            }
            startTransaction();
            dbquery("INSERT INTO
                messages
            (
                message_user_from,
                message_user_to,
                message_timestamp,
                message_cat_id
            )
            VALUES
            (
                '".intval($senderId)."',
                '".intval($receiverId)."',
                ".time().",
                ".intval($cat)."
            );");
            dbquery("
                INSERT INTO
                    message_data
                (
                    id,
                    subject,
                    text,
                    fleet_id
                )
                VALUES
                (
                    ".mysql_insert_id().",
                '".mysql_real_escape_string($subject)."',
                '".mysql_real_escape_string($text)."',
                '".$fleetId."'
                );
            ");
            commitTransaction();
        } catch (Exception $e) {
            rollbackTransaction();
        }
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

        /** @var Log */
        $log = $app['etoa.log.service'];

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
            $log->add(Log::F_SYSTEM, Log::INFO, "Unarchivierte Nachrichten die älter als ".date("d.m.Y H:i",$timestamp)." sind wurden gelöscht!");
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
        $log->add(Log::F_SYSTEM, Log::INFO, "Unarchivierte Nachrichten die älter als ".date("d.m.Y H:i",$timestamp)." sind wurden gelöscht!");
        $nr += mysql_affected_rows();
        return $nr;
    }
}
