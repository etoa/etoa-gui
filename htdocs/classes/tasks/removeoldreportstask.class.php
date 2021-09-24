<?PHP

/**
 * Remove old reports
 */
class RemoveOldReportsTask implements IPeriodicTask
{
    function run()
    {
        Report::removeOld();
        return "Alte Berichte gelöscht";
    }

    public static function getDescription()
    {
        return "Alte Berichte löschen";
    }
}
