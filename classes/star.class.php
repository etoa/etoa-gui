<?PHP
	
	/**
	* Class for stars
	*/
	class Star
	{
		/**
		* The constructor
		*/
		function Star($id=0)
		{
			
		}

		/**
		* Returns owner
		*/                        
		function owner() { return "Niemand"; }      
		
		/**
		* Returns type name
		*/
		function type() { return "Stern"; }	      
		
		/**
		* To-String function
		*/
		function __toString() { return ""; }
		
	}
?>
