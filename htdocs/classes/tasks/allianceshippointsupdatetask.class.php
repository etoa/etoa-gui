<?PHP

/**
 * Calculate alliance ship pints
 */
class AllianceShipPointsUpdateTask implements IPeriodicTask
{
    function run()
    {
        Alliance::allianceShipPointsUpdate();
        return "Allianz-Schiffsteile berechnet";
    }

    function getDescription()
    {
        return "Allianz-Schiffsteile berechnen";
    }
}
