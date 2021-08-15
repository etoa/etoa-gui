<?PHP

use EtoA\Defense\DefenseRepository;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use Pimple\Container;

/**
 * Remove old defense build list records
 */
class CleanupDeflistTask implements IPeriodicTask
{
    private DefenseRepository $defenseRepository;
    private LogRepository $logRepository;

    public function __construct(Container $app)
    {
        $this->defenseRepository = $app[DefenseRepository::class];
        $this->logRepository = $app[LogRepository::class];
    }

    function run()
    {
        $nr = $this->defenseRepository->cleanUp();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, "$nr leere Verteidigungsdatensätze wurden gelöscht!");

        return "$nr alte Verteidigungseinträge gelöscht";
    }

    function getDescription()
    {
        return "Alte Verteidigungsbaudatensätze löschen";
    }
}
