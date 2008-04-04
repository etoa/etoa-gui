<?PHP

	/**
	* Abstract class for all space entities
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/ 
	abstract class Entity
	{
		// Prevent usage as object
		private function Entity() {}

		// Some getters
		public abstract function owner();	
		public abstract function type();	
		public abstract function __toString();	
	     
	  // The factory design pattern
		static public function createFactory($type,$id=0)
		{
			switch ($type)
			{
				case 's':
					return new Star($id);
				case 'p':
					return new Planet($id);
				case 'a':
					return new AsteroidField($id);
				case 'n':
					return new Nebula($id);
				case 'w':
					return new Wormhole($id);
				default:
					return new UnknownEntity();
			}			
		}		
	}

?>