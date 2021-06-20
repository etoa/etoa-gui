<?PHP

use EtoA\User\UserRepository;
use Pimple\Container;

/**
 * Remove old user point log entries
 */
class RemoveOldUserPointLogsTask implements IPeriodicTask
{
	private UserRepository $userRepository;

	function __construct(Container $app)
	{
		$this->userRepository = $app['etoa.user.repository'];
	}

	function run()
	{
		$nr = $this->userRepository->cleanUpPoints();
		return "$nr alte Userpunkte-Logs gelöscht";
	}

	function getDescription()
	{
		return "Alte Benutzerpunkte-Logs löschen";
	}
}
