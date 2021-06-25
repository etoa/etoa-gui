<?PHP

use EtoA\Ranking\PointsService;
use Pimple\Container;

/**
 * Remove old user point log entries
 */
class RemoveOldUserPointLogsTask implements IPeriodicTask
{
	private PointsService $pointsService;

	function __construct(Container $app)
	{
        $this->pointsService = $app['etoa.rankings.points.service'];
	}

	function run()
	{
		$nr = $this->pointsService->cleanupUserPoints();
		return "$nr alte Userpunkte-Logs gelöscht";
	}

	function getDescription()
	{
		return "Alte Benutzerpunkte-Logs löschen";
	}
}
