<?PHP

use EtoA\Log\LogFacility;
use EtoA\Support\DB\DatabaseManagerRepository;
use Pimple\Container;

/**
 * Analyze tables
 */
class AnalyzeTablesTask implements IPeriodicTask
{
    private DatabaseManagerRepository $databaseManager;

    public function __construct(Container $app)
    {
        $this->databaseManager = $app[DatabaseManagerRepository::class];
    }

    function run()
    {
        $result = $this->databaseManager->analyzeTables();
        Log::add(LogFacility::SYSTEM, Log::INFO, count($result) . " Tabellen wurden analysiert!");
        return "Tabellen analysiert";
    }

    function getDescription()
    {
        return "Tabellen analysieren";
    }
}
