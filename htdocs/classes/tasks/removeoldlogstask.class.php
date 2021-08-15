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

    function getDescription()
    {
        return "Alte Logs löschen";
    }
}
