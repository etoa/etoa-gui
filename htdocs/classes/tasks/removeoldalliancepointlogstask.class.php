<?PHP

use EtoA\Alliance\AllianceRepository;
use Pimple\Container;

/**
 * Remove old appliance point logs
 */
class RemoveOldAlliancePointLogsTask implements IPeriodicTask
{
	private AllianceRepository $allianceRepository;

	function __construct(Container $app)
	{
		$this->allianceRepository = $app['etoa.alliance.repository'];
	}

	function run()
	{
		$nr = $this->allianceRepository->cleanUpPoints();
		return "$nr alte Allianzpunkte-Logs gelöscht";
	}

	function getDescription()
	{
		return "Alte Allianzpunkte-Logs löschen";
	}
}
