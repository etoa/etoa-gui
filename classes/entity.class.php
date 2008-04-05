<?PHP

	/**
	* Abstract class for all space entities
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/ 
	abstract class Entity
	{
		/**
		* Private constructor
		* Prevents usage as object
		*/
		private function Entity() {}

		/**
		* Return entity-id
		*/
		public abstract function id();	
		
		/**
		* Return entity position in cell
		*/		
		public abstract function pos();	
		
		/**
		* Return entity name
		*/
		public abstract function name();	
		
		/**
		* Return entity-owner
		*/
		public abstract function owner();	
		
		/**
		* Return entity-owner id
		*/
		public abstract function ownerId();	
		
		/**
		* Return entity-code 
		*/
		public abstract function entityCode();	

		/**
		* Return entity-code string
		*/
		public abstract function entityCodeString();	
		
		/**
		* Some entities are dividet into special
		* types; return this type
		*/
		public abstract function type();	

		/**
		* Provies the current image path
		*/
		public abstract function imagePath($opt="");	
	
		/**
		* Return coordinates
		*/		
		public abstract function __toString();	
	     
	  /**
	  * Creates an instance of a child class
	  * using the factory design pattern
	  */ 
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