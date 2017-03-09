<?PHP
/**
 * Give Rewards
 */
class GiveRewards implements IPeriodicTask
{
    function run()
    {
        Rewards::giveRewards();
        return "Alle Belohnungen erfolgreich berechnet";
    }

    function getDescription() {
        return "Belohnungen für Kontrollpunkte";
    }
}
