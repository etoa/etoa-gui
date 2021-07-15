<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;

/**
 * Update user sitting days
 */
class UpdateSittingDaysTask implements IPeriodicTask
{
    function run()
    {
        global $app;

        /** @var ConfigurationService */
        $config = $app[ConfigurationService::class];

        /** @var UserRepository $userRepository */
        $userRepository = $app[UserRepository::class];
        $userRepository->addSittingDays($config->param1Int("user_sitting_days"));

        return "Sittertage aller User wurden aktualisiert";
    }

    function getDescription()
    {
        return "Sitter-Tage aktualisieren";
    }
}
