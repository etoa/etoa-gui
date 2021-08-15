<?PHP

use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\DB\DatabaseManagerRepository;
use Pimple\Container;

/**
 * Optimize tables
 */
class OptimizeTablesTask implements IPeriodicTask
{
    private DatabaseManagerRepository $databaseManager;
    private LogRepository $logRepository;

    public function __construct(Container $app)
    {
        $this->databaseManager = $app[DatabaseManagerRepository::class];
        $this->logRepository = $app[LogRepository::class];
    }

    function run()
    {
        $result = $this->databaseManager->optimizeTables();
        $this->logRepository->add(LogFacility::SYSTEM, LogSeverity::INFO, count($result) . " Tabellen wurden optimiert!");
        return "Tabellen optimiert";
    }

    function getDescription()
    {
        return "Tabellen optimieren";
    }
}
