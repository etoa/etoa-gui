<?PHP

use EtoA\User\UserService;
use Pimple\Container;

/**
 * Remove users marked as deleted
 */
class RemoveDeletedUsersTask implements IPeriodicTask
{
    private UserService $userService;

    function __construct(Container $app)
    {
        $this->userService = $app[UserService::class];
    }

    function run()
    {
        $nr = $this->userService->removeDeleted();
        return "$nr als gelöscht markierte User endgültig gelöscht";
    }

    function getDescription()
    {
        return "Zum Löschen markierte User löschen";
    }
}
