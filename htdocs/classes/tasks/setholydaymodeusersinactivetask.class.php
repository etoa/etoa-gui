<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserService;
use Pimple\Container;

/**
 * Set users in holyday mode to inactive after threshold has been passed
 */
class SetHolydayModeUsersInactiveTask implements IPeriodicTask
{
    private UserService $userService;
    private ConfigurationService $config;

    public function __construct(Container $app)
    {
        $this->config = $app[ConfigurationService::class];
        $this->userService = $app[UserService::class];
    }

    function run()
    {
        if ($this->config->param2Boolean('hmode_days')) {
            $count = $this->userService->setUmodToInactive();
            return "$count User aus Urlaubsmodus in Inaktivität gesetzt";
        }
        return null;
    }

    function getDescription()
    {
        return "Benutzer aus Urlaub inaktiv setzen";
    }
}
