<?PHP

use EtoA\Ranking\RankingService;
use Pimple\Container;

/**
 * Calculate points and update ranking
 */
class CalculateRankingTask implements IPeriodicTask
{
    private RankingService $rankingService;

    function __construct(Container $app)
    {
        $this->rankingService = $app[RankingService::class];
    }

    function run()
    {
        $result = $this->rankingService->calc();
        return "Die Punkte von " . $result->numberOfUsers . " Spielern wurden aktualisiert; ein Spieler hat durchschnittlich " . nf($result->getAveragePoints()) . " Punkte";
    }

    function getDescription()
    {
        return "Punkte berechnen und Rangliste aktualisieren";
    }
}
