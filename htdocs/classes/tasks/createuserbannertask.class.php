<?PHP
	/**
	* Create user banners
	*/
	class CreateUserBannerTask implements IPeriodicTask 
	{		
		function run()
		{
			Ranking::createUserBanner();
			return "User Banner erstellt";
		}
		
		function getDescription() {
			return "User Banner erstellen";
		}
	}
?>