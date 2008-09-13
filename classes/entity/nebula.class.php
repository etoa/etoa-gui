<?PHP
	
	/**
	* Class for nebula entity
	*/
	class Nebula extends Entity
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
		
		/**
		* The constructor
		*/
		function Nebula($id=0)
		{
			$this->isValid = true;
			$this->id = $id;
			$this->pos = 0;
			$this->name = "Unbenannt";
			$this->coordsLoaded=false;			
      		$this->isVisible = true;
		}

    public function allowedFleetActions()
    {
    	return array("collectcrystal","analyze","flight","explore");
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
		function entityCodeString() { return "Interstellarer Gasnebel"; }      
		
		/**
		* Returns type
		*/
		function type()
		{
			return "Interstellarer Nebel";
		}							

		function imagePath($opt="")
		{
			$r = mt_rand(1,9);
			return IMAGE_PATH."/nebulas/nebula".$r."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() { return "n"; }	      
		
		
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
