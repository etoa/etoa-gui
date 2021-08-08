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
        if (ENABLE_USERTITLES == 1) {
            $this->rankingService->calcTitles();
            return "User Titel aktualisiert";
        }
        return "User Titel nicht aktualisiert (deaktiviert)";
    }

    function getDescription()
    {
        return "Titel aktualisieren";
    }
}
