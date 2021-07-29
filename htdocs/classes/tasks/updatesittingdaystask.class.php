<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserRepository;
use Pimple\Container;

/**
 * Update user sitting days
 */
class UpdateSittingDaysTask implements IPeriodicTask
{
    private ConfigurationService $config;
    private UserRepository $userRepository;

    public function __construct(Container $app)
    {
        $this->config = $app[ConfigurationService::class];
        $this->userRepository = $app[UserRepository::class];
    }

    function run()
    {
        $this->userRepository->addSittingDays($this->config->param1Int("user_sitting_days"));

        return "Sittertage aller User wurden aktualisiert";
    }

    function getDescription()
    {
        return "Sitter-Tage aktualisieren";
    }
}
