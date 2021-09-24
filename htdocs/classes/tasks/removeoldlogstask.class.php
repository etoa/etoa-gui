<?PHP

/**
 * Remove old logs
 */
class RemoveOldLogsTask implements IPeriodicTask
{
    function run()
    {
        $nr = BaseLog::removeOld();
        return "$nr alte Logs gelöscht";
    }

    public static function getDescription()
    {
        return "Alte Logs löschen";
    }
}
