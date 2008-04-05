<?PHP
	
	/**
	* Class for nebula entity
	*/
	class Wormhole extends Entity
	{
		private $id;
		private $pos;
		private $name;		
		
		/**
		* The constructor
		*/
		function Wormhole($id=0)
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
		function entityCodeString() { return "Wurmloch"; }      
	
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
			$r = mt_rand(1,9);
			return IMAGE_PATH."/wormholes/wormhole1_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() { return "w"; }	      
		
		/**
		* To-String function
		*/
		function __toString() { return "-"; }
		
	}
?>
