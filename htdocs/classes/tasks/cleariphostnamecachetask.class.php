<?PHP

use EtoA\HostCache\NetworkNameService;
use Pimple\Container;

/**
 * Remove old ip-hostname combos from cache
 */
class ClearIPHostnameCacheTask implements IPeriodicTask
{
    private NetworkNameService $networkNameService;

    public function __construct(Container $app)
    {
        $this->networkNameService = $app[NetworkNameService::class];
    }

    function run()
    {
        $this->networkNameService->clearCache();

        return "IP/Hostname Cache gelöscht";
    }

    function getDescription()
    {
        return "Alte IP/Hostnamen Mappings aus Cache löschen";
    }
}
