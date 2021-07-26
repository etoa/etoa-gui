<?PHP

use EtoA\Support\DatabaseManagerRepository;
use Pimple\Container;

/**
 * Optimize tables
 */
class OptimizeTablesTask implements IPeriodicTask
{
    private DatabaseManagerRepository $databaseManager;

    public function __construct(Container $app)
    {
        $this->databaseManager = $app[DatabaseManagerRepository::class];
    }

    function run()
    {
        $result = $this->databaseManager->optimizeTables();
        Log::add(Log::F_SYSTEM, Log::INFO, count($result) . " Tabellen wurden optimiert!");
        return "Tabellen optimiert";
    }

    function getDescription()
    {
        return "Tabellen optimieren";
    }
}
