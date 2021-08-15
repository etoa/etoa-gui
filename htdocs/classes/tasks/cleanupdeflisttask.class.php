<?PHP

use EtoA\Defense\DefenseRepository;
use EtoA\Log\LogFacility;
use Pimple\Container;

/**
 * Remove old defense build list records
 */
class CleanupDeflistTask implements IPeriodicTask
{
    private DefenseRepository $defenseRepository;

    public function __construct(Container $app)
    {
        $this->defenseRepository = $app[DefenseRepository::class];
    }

    function run()
    {
        $nr = $this->defenseRepository->cleanUp();
        Log::add(LogFacility::SYSTEM, Log::INFO, "$nr leere Verteidigungsdatensätze wurden gelöscht!");

        return "$nr alte Verteidigungseinträge gelöscht";
    }

    function getDescription()
    {
        return "Alte Verteidigungsbaudatensätze löschen";
    }
}
