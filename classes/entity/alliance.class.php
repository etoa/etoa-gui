<?PHP
	
	/**
	* Class for alliance space entities
	*/
	class Aliance extends Entity
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
			$this->name = "Allianz";
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
		function entityCodeString() { return "Allianz"; }      
	
		/**
		* Returns type
		*/
		function type()
		{
			return "Allianz";
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
			return "x"; 
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
		
	}
?>
