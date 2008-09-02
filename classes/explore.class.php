<?PHP

	/**
	* Abstract class for all explore Types
	*
	* @author Stephan Vock <glaubinix@etoa.ch>
	*/ 
	abstract class Explore
	{
    protected $isVisible;
    
		/**
		* Private constructor
		* Prevents usage as object
		*/
		private function Explore() {}


		/**
		* Return explore-id
		*/
		public abstract function id();	
		
		
		/**
		* Return explore name
		*/
		public abstract function name();
		
		
		/**
		* Return explore description
		*/
		public abstract function description();	
		
			
		/**
		* Return explore-code 
		*/
		public abstract function exploreCode();	
		
		
		/**
		* Return explore-code string
		*/
		public abstract function exploreCodeString();	
		
		
	    /**
	    * Return if explore is visible in map
	    */
	    public function isVisible()
	    {
	      return $this->isVisible; 
    	}    
    
    
    	public abstract function allowedFleetActions();	
		
		
		/**
		* Load the exploredata
		**/
		public abstract function loadData();			  
   
    
		/**
		* check if data could be loaded
		*/
		public function isValid()
		{
			return $this->isValid;
		}	 
	  	       
	     
	  /**
	  * Creates an instance of a child class
	  * using the factory design pattern
	  */ 
		static public function createFactory($type,$id=0)
		{
			switch ($type)
			{
				case 'p':
					return new Pirates($id);
				case 'a':
					return new Aliens($id);
				case 'r':
					return new Resources($id);
				case 's':
					return new Ships($id);
				case 'k':
					return new Knowledge($id);
				default:
					return false;
			}		
			return false;	
		}	
		
	  /**
	  * Creates an instance of a child class
	  * using the factory design pattern
	  */ 
		static public function createFactoryById($id)
		{
			$res=dbquery("
			SELECT
				explore_code
			FROM
				entities
			WHERE
				id=".$id."
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				$type = $arr[0];
				
				switch ($type)
				{
					case 'p':
						return new Pirates($id);
					case 'a':
						return new Aliens($id);
					case 'r':
						return new Resources($id);
					case 's':
						return new ships($id);
					case 'k':
						return new Knowledge($id);
					default:
						return false;
				}			
			}
			return false;
			//die ("Ungültige ID");
		}				
	}

?>