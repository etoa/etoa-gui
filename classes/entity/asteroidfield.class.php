<?PHP
	
	/**
	* Class for asteroidfield entity
	*/
	class AsteroidField extends Entity
	{
		protected $id;
		protected $coordsLoaded;
		protected $isValid;		
		public $pos;
		public $sx;
		public $sy;
		public $cx;
		public $cy;
		protected $cellId;
		private $name;	
		protected $exploreCode;	
		public $explore;
		
		/**
		* The constructor
		*/
		function AsteroidField($id=0,$explore='e')
		{
			$this->isValid = true;
			$this->id = $id;
			$this->pos = 0;
			$this->name = "Unbenannt";
			$this->coordsLoaded=false;			
     		$this->isVisible = true;
			$this->exploreCode = $explore;
			$this->explore = Explore::createFactory($this->exploreCode,$this->id);
		}

    public function allowedFleetActions()
    {
    	return array("collectmetal","analyze","flight","explore");
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
		* Returns type
		*/
		function type()
		{
			return "Asteroidenfeld";
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
		* Return Explore object
		*/
		function explore() { return $this->explore; }	      
		
		/**
		* To-String function
		*/
		function __toString() 
		{
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->formatedCoords();
		}
		
		/**
		* Returns the cell id
		*/
		function cellId()
		{
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->cellId;
		}
		
	}
?>
