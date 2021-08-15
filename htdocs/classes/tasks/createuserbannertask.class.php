<?PHP

use EtoA\Ranking\UserBannerService;
use Pimple\Container;

/**
 * Create user banners
 */
class CreateUserBannerTask implements IPeriodicTask
{
    private UserBannerService $userBannerService;

    function __construct(Container $app)
    {
        $this->userBannerService = $app[UserBannerService::class];
    }

    function run()
    {
        $this->userBannerService->createUserBanner();
        return "User Banner erstellt";
    }

    function getDescription()
    {
        return "User Banner erstellen";
    }
}
