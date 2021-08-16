<?PHP

use EtoA\Missile\MissileFlightRepository;
use EtoA\Missile\MissileFlightSearch;
use Pimple\Container;

/**
 * Checks and handles missile actions
 */
class CheckMissilesTask implements IPeriodicTask
{
    private MissileFlightRepository $missileFlightRepository;

    public function __construct(Container $pimple)
    {
        $this->missileFlightRepository = $pimple[MissileFlightRepository::class];
    }

    function run()
    {
        $flights = $this->missileFlightRepository->getFlights(MissileFlightSearch::create()->landed());
        $cnt = count($flights);
        foreach ($flights as $flight) {
            MissileBattleHandler::battle($flight);
            $cnt++;
        }
        return "$cnt Raketen-Aktionen berechnet";
    }

    function getDescription()
    {
        return "Raketen-Aktionen berechnen";
    }
}
