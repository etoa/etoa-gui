<?PHP
	
	/**
	* Star-Class
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class Star extends Entity
	{
		private $id;
		private $pos;
		private $name;
		private $typeId;
		private $isValid;		
		private $typeName;
		
		/**
		* The constructor
		*/
		function Star($id=0)
		{
			$this->isValid=false;
			
			$res=dbquery("
			SELECT 
	    	stars.name,
	    	stars.type_id,
	    	entities.pos,
	    	sol_types.type_name
			FROM 
	    	stars
	    INNER JOIN	
	    	entities
	    ON entities.id=stars.id
				AND	stars.id='".intval($id)."'
			INNER JOIN sol_types
				ON stars.type_id=sol_types.type_id;");
			if (mysql_num_rows($res))	
			{
				$arr = mysql_fetch_row($res);
				$this->id=$id;

				$this->name = $arr[0]!="" ? $arr[0] : "Unbenannt";
				$this->typeId = $arr[1];
				$this->pos = $arr[2];
				$this->typeName = $arr[3];

				$this->isValid=true;
			}
		}

		/**
		* Returns validity
		*/
		public function isValid()
		{
			return $this->isValid;
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
		function entityCodeString() { return "Stern"; }      
	
		/**
		* Returns owner
		*/                        
		function pos() { return $this->pos; }      
		
		/**
		* Returns star type
		*/
		function type()
		{
			return $this->typeName;
		}							

		function imagePath($opt="")
		{
			return IMAGE_PATH."/stars/star".$this->typeId."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() { return "s"; }	      
		
		/**
		* To-String function
		*/
		function __toString() { return ""; }
		
	}
?>
