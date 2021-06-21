<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use Pimple\Container;

/**
 * Set users in holyday mode to inactive after threshold has been passed
 */
class SetHolydayModeUsersInactiveTask implements IPeriodicTask
{
    private ConfigurationService $config;

    public function __construct(Container $app)
    {
        $this->config = $app['etoa.config.service'];
    }

    function run()
    {
        if ($this->config->param2Int('hmode_days')) {
            $nr = Users::setUmodToInactive();
            return "$nr User aus Urlaubsmodus in Inaktivität gesetzt";
        }
        return null;
    }

    function getDescription()
    {
        return "Benutzer aus Urlaub inaktiv setzen";
    }
}
