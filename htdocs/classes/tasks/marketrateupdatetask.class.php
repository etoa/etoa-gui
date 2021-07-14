<?PHP

/**
 * Update market resource rates
 */
class MarketrateUpdateTask implements IPeriodicTask
{
    function run()
    {
        MarketHandler::updateRates();
        return "Rohstoff-Raten im Markt aktualisiert";
    }

    function getDescription()
    {
        return "Markt-Ressourcen Verhältnisse aktualisieren";
    }
}
