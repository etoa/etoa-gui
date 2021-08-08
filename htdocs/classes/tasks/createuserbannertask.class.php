<?PHP

use EtoA\Ranking\RankingService;
use Pimple\Container;

/**
 * Create user banners
 */
class CreateUserBannerTask implements IPeriodicTask
{
    private RankingService $rankingService;

    function __construct(Container $app)
    {
        $this->rankingService = $app[RankingService::class];
    }

    function run()
    {
        $this->rankingService->createUserBanner();
        return "User Banner erstellt";
    }

    function getDescription()
    {
        return "User Banner erstellen";
    }
}
