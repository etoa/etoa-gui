<?PHP

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipRepository;
use Pimple\Container;

/**
 * Remove old ship build list records
 */
class CleanupShiplistTask implements IPeriodicTask
{
    private ShipRepository $shipRepository;
    private LogRepository $logRepository;

    public function __construct(Container $app)
    {
        $this->shipRepository = $app[ShipRepository::class];
        $this->logRepository = $app[LogRepository::class];
    }

    function run()
    {
        $nr = $this->shipRepository->cleanUp();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Schiffsdatensätze wurden gelöscht!");

        return "$nr alte Schiffseinträge gelöscht";
    }

    function getDescription()
    {
        return "Alte Schiffbaudatensätze löschen";
    }
}
