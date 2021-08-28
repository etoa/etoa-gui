<?PHP

use EtoA\Alliance\AllianceShipPointsService;
use Pimple\Container;

/**
 * Calculate alliance ship pints
 */
class AllianceShipPointsUpdateTask implements IPeriodicTask
{
    private AllianceShipPointsService $service;

    public function __construct(Container $pimple)
    {
        $this->service = $pimple[AllianceShipPointsService::class];
    }

    function run()
    {
        $this->service->update();

        return "Allianz-Schiffsteile berechnet";
    }

    function getDescription()
    {
        return "Allianz-Schiffsteile berechnen";
    }
}
