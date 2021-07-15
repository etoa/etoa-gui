<?PHP

/**
 * Analyze tables
 */
class AnalyzeTablesTask implements IPeriodicTask
{
    function run()
    {
        DBManager::getInstance()->analyzeTables();
        return "Tabellen analysiert";
    }

    function getDescription()
    {
        return "Tabellen analysieren";
    }
}
