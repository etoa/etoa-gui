<?PHP
	/**
	* Permute wormholes
	*/
	class PermuteWormholesTask implements IPeriodicTask 
	{		
		function run()
		{
			Wormhole::randomize();
			return "Wurml&ouml;cher vertauscht";
		}
		
		function getDescription() {
			return "Wurmlöcher vertauschen";
		}
	}
?>