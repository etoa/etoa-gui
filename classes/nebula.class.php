<?PHP
	
	/**
	* Class for nebula entity
	*/
	class Nebula extends Entity
	{
		private $id;
		private $pos;
		private $name;		
		
		/**
		* The constructor
		*/
		function Nebula($id=0)
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
		function entityCodeString() { return "Interstellarer Gasnebel"; }      
	
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
			return IMAGE_PATH."/nebulas/nebula".$r."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() { return "n"; }	      
		
		/**
		* To-String function
		*/
		function __toString() { return "-"; }
		
	}
?>
