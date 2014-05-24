<?PHP
	
	/**
	* Class for empty space entities
	*/
	class EmptySpace extends Entity
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
		function EmptySpace($id=0)
		{
			$this->isValid = true;
			$this->id = intval($id);
			$this->pos = 0;
			$this->name = "";
			$this->coordsLoaded=false;
    		$this->isVisible = true;
			
			
		}

    public function allowedFleetActions()
    {
    	return array("flight","explore");
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

		function ownerMain() { return false; }
	
		
		/**
		* Returns type string
		*/                        
		function entityCodeString() { return "Leerer Raum"; }      
	
		/**
		* Returns type
		*/
		function type()
		{
			return "";
		}							

		function imagePath($opt="")
		{
			defineImagePaths();
			$numImages = 10;
			$r = ($this->id % $numImages) + 1;
			return IMAGE_PATH."/space/space".$r."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() 
		{ 
			return "e"; 
		}	      
		
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
		public function getFleetTargetForwarder()
		{
			return null;
		}		
		
	}
?>
