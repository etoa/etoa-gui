<?PHP
	
	/**
	* Class for asteroidfield entity
	*/
	class AsteroidField extends Entity
	{
		private $id;
		private $pos;
		private $name;		
		
		/**
		* The constructor
		*/
		function AsteroidField($id=0)
		{
			$this->id = $id;
			$this->pos = 0;
			$this->name = "Unbenannt";
		}

		/**
		* Returns id
		*/                        
		function id() { return $this->id; }      

		/**
		* Returns id
		*/                        
		function name() { return $this->name; }      


		/**
		* Returns owner
		*/                        
		function owner() { return "Niemand"; }      

		/**
		* Returns owner
		*/                        
		function ownerId() { return 0; }      
	
		/**
		* Returns type string
		*/                        
		function entityCodeString() { return "Asteroidenfeld"; }      
	
		/**
		* Returns owner
		*/                        
		function pos() { return $this->pos; }      
		
		/**
		* Returns type
		*/
		function type()
		{
			return "";
		}							

		function imagePath($opt="")
		{
			$r = mt_rand(1,5);
			return IMAGE_PATH."/asteroids/asteroids".$r."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() { return "a"; }	      
		
		/**
		* To-String function
		*/
		function __toString() { return "-"; }
		
	}
?>
