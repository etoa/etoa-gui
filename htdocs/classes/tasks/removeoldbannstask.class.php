<?PHP

use EtoA\User\UserRepository;

/**
	* Remove old, outdated banns
	*/
	class RemoveOldBannsTask implements IPeriodicTask
	{
		function run()
		{
		    global $app;

		    /** @var UserRepository $userRepository */
		    $userRepository = $app[UserRepository::class];
            $userRepository->removeOldBans();

			return "Abgelaufene Sperren gelöscht";
		}

		function getDescription() {
			return "Abgelaufene Sperren löschen";
		}
	}
?>
