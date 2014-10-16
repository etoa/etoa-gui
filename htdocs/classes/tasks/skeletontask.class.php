<?PHP
	/**
	* USE AS AN EXAMPLE
	*/
	class SkeletonTask implements IPeriodicTask 
	{		
		function run()
		{
			return "Done";
		}
		
		function getDescription() {
			return "";
		}
	}
?>