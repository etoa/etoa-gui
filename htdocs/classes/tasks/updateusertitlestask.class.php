<?PHP

use EtoA\Ranking\RankingService;
use Pimple\Container;

/**
 * Update user titles
 */
class UpdateUserTitlesTask implements IPeriodicTask
{
    private RankingService $rankingService;

    function __construct(Container $app)
    {
        $this->rankingService = $app[RankingService::class];
    }

    function run()
    {
        $this->rankingService->calcTitles();
        return "User Titel aktualisiert";
    }

    function getDescription()
    {
        return "Titel aktualisieren";
    }
}
