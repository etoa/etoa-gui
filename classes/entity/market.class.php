<?PHP
	
	/**
	* Class for the market / trade planet
	*/
	class Market extends Entity
	{
		private $name;		
		protected $id;
		protected $coordsLoaded;
		protected $isValid;		
		public $pos;
		public $sx;
		public $sy;
		public $cx;
		public $cy;
		protected $cellId;
		
		/**
		* The constructor
		*/
		function Market($id=0)
		{
			$this->isValid = true;
			$this->id = $id;
			$this->pos = 0;
			$this->name = "Marktplatz";
			$this->coordsLoaded=false;
      $this->isVisible = true;
		}

    public function allowedFleetActions()
    {
    	return array("market");
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
		function entityCodeString() { return "Marktplatz"; }      

		function ownerMain() { return false; }

	
		/**
		* Returns type
		*/
		function type()
		{
			return "";
		}							

		function imagePath($opt="")
		{
			$r = mt_rand(1,10);
			return IMAGE_PATH."/space/space".$r."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() 
		{ 
			return "m"; 
		}	      
		
		/**
		* To-String function
		*/
		function __toString() 
		{
			/*if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->formatedCoords();*/
			return "";
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
		public function getFleetTargetForwarder()
		{
			return null;
		}		
	
	}
?>
