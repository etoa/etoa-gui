<?PHP

use EtoA\User\UserRepository;
use Pimple\Container;

/**
 * Remove old, outdated banns
 */
class RemoveOldBannsTask implements IPeriodicTask
{
    private UserRepository $userRepository;

    public function __construct(Container $app)
    {
        $this->userRepository = $app[UserRepository::class];
    }

    function run()
    {
        $this->userRepository->removeOldBans();

        return "Abgelaufene Sperren gelöscht";
    }

    function getDescription()
    {
        return "Abgelaufene Sperren löschen";
    }
}
