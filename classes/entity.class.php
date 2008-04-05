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
		* check if data could be loaded
		*/
		public function isValid()
		{
			return $this->isValid;
		}	

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
	  
	  public abstract function cellId();
	  
	  protected function loadCoords()
	  {
	  	$res = dbquery("
	  	SELECT
	  		sx,
	  		sy,
	  		cx,
	  		cy,
	  		pos,
	  		cells.id
	  	FROM	
	  		cells
	  	INNER JOIN
	  		entities 
	  		ON entities.cell_id=cells.id	  	
	  		AND entities.id=".$this->id."
	  	");
	  	if (mysql_num_rows($res)>0)
	  	{
	  		$arr=mysql_Fetch_row($res);
	  		$this->sx=$arr[0];
	  		$this->sy=$arr[1];
	  		$this->cx=$arr[2];
	  		$this->cy=$arr[3];
	  		$this->pos=$arr[4];
	  		$this->cellId=$arr[5];
	  		$this->coordsLoaded=true;
	  	}
	  }	  
	   
	  protected function formatedCoords()
	  {
	  	return $this->sx."/".$this->sy." : ".$this->cx."/".$this->cy." : ".$this->pos;
	  }
	     
	     
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
					return new UnknownEntity($id);
			}			
		}	
		
	  /**
	  * Creates an instance of a child class
	  * using the factory design pattern
	  */ 
		static public function createFactoryById($id)
		{
			$res=dbquery("
			SELECT
				code
			FROM
				entities
			WHERE
				id=".$id."
			");
			$arr = mysql_fetch_array($res);
			$type = $arr[0];
			
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
					return new UnknownEntity($id);
			}			
		}			
	}

?>