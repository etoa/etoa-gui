<?PHP
	
	/**
	* Class for unknown space entities
	*/
	class UnknownEntity
	{
		/**
		* The constructor
		*/
		function UnknownEntity($id=0)
		{
			
		}

		/**
		* Returns owner
		*/                        
		function owner() { return "Niemand"; }      
		
		/**
		* Returns type name
		*/
		function type() { return "Unbekannter Raum"; }	      
		
		/**
		* To-String function
		*/
		function __toString() return { "" };	
		
	}
?>
