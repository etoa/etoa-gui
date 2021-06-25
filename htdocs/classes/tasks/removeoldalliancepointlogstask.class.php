<?PHP

use EtoA\Ranking\PointsService;
use Pimple\Container;

/**
 * Remove old appliance point logs
 */
class RemoveOldAlliancePointLogsTask implements IPeriodicTask
{
	private PointsService $pointsService;

	function __construct(Container $app)
	{
        $this->pointsService = $app['etoa.rankings.points.service'];
	}

	function run()
	{
		$nr = $this->pointsService->cleanupAlliancePoints();
		return "$nr alte Allianzpunkte-Logs gelöscht";
	}

	function getDescription()
	{
		return "Alte Allianzpunkte-Logs löschen";
	}
}
