<?PHP

use EtoA\Ranking\GameStatsGenerator;
use Pimple\Container;

/**
 * Generate and store game statistics
 */
class GenerateGameStatsTask implements IPeriodicTask
{
    private GameStatsGenerator $gameStatsGenerator;

    function __construct(Container $app)
    {
        $this->gameStatsGenerator = $app[GameStatsGenerator::class];
    }

    function run()
    {
        $this->gameStatsGenerator->generateAndSave(GAMESTATS_FILE);
        return "Spielstatistiken erstellt";
    }

    function getDescription()
    {
        return "Spielstatistiken generieren und speichern";
    }
}
