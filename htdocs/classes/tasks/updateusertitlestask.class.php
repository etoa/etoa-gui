<?PHP

use EtoA\Ranking\UserTitlesService;
use Pimple\Container;

/**
 * Update user titles
 */
class UpdateUserTitlesTask implements IPeriodicTask
{
    private UserTitlesService $userTitlesService;

    function __construct(Container $app)
    {
        $this->userTitlesService = $app[UserTitlesService::class];
    }

    function run()
    {
        $this->userTitlesService->calcTitles();
        return "User Titel aktualisiert";
    }

    public static function getDescription()
    {
        return "Titel aktualisieren";
    }
}
