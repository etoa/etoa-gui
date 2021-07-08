<?PHP

use EtoA\User\UserService;
use Pimple\Psr11\Container;

/**
 * Remove inactive users
 */
class RemoveInactiveUsersTask implements IPeriodicTask
{
    private UserService $userService;

    function __construct(Container $app)
    {
        $this->userService = $app[UserService::class];
    }

    function run()
    {
        $nr = $this->userService->removeInactive();

        $this->userService->informLongInactive();

        return "$nr inaktive User gelöscht";
    }

    function getDescription()
    {
        return "Inaktive User löschen";
    }
}
