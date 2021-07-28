<?PHP

use EtoA\Defense\DefenseRepository;
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
        Log::add(Log::F_SYSTEM, Log::INFO, "$nr leere Verteidigungsdatensätze wurden gelöscht!");

        return "$nr alte Verteidigungseinträge gelöscht";
    }

    function getDescription()
    {
        return "Alte Verteidigungsbaudatensätze löschen";
    }
}
