<?PHP
	
	/**
	* Star-Class
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class Star extends Entity
	{
		protected $id;
		protected $pos;
		private $name;
		private $typeId;
		protected $isValid;		
		protected $typeName;
		public $named;
		protected $coordsLoaded;
		protected $sx;
		protected $sy;
		protected $cx;
		protected $cy;				
		protected $cellId;
		
		/**
		* The constructor
		*/
		function Star($id=0)
		{
			$this->isValid=false;
			$this->coordsLoaded=false;
     		$this->isVisible = true;
						
			$res=dbquery("
			SELECT 
	    	stars.name,
	    	stars.type_id,
	    	entities.pos,
	    	sol_types.sol_type_name
			FROM 
	    	stars
	    INNER JOIN	
	    	entities
	    ON entities.id=stars.id
				AND	stars.id='".intval($id)."'
			INNER JOIN sol_types
				ON stars.type_id=sol_types.sol_type_id;");
			if (mysql_num_rows($res))	
			{
				$arr = mysql_fetch_row($res);
				$this->id=$id;
				
				if ($arr[0]!="")
				{
					$this->name = stripslashes($arr[0]);
				 	$this->named = true;
				}
				else
				{	
				 	$this->name = "Unbenannt";
				 	$this->named = false;
				}
				$this->typeId = $arr[1];
				$this->pos = $arr[2];
				$this->typeName = $arr[3];

				$this->isValid=true;
			}
		}
		
		public function typeData()
		{
				$res = dbquery("
				SELECT
					*
				FROM 
					sol_types
				WHERE
					sol_type_id=".$this->typeId."	
				;");
				$arr = mysql_fetch_assoc($res);
				$rtn = array(
				"metal" => $arr['sol_type_f_metal'],
				"crystal" => $arr['sol_type_f_crystal'],
				"plastic" => $arr['sol_type_f_plastic'],
				"fuel" => $arr['sol_type_f_fuel'],
				"food" => $arr['sol_type_f_food'],
				"power" => $arr['sol_type_f_power'],
				"population" => $arr['sol_type_f_population'],
				"buildtime" => $arr['sol_type_f_buildtime'],
				"researchtime" => $arr['sol_type_f_researchtime'],				
				"comment" => $arr['sol_type_comment']				
				);
				return $rtn;					
		}

    public function allowedFleetActions()
    {
    	return array("flight","explore");
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

		function ownerMain() { return false; }

	
		/**
		* Returns type string
		*/                        
		function entityCodeString() { return "Stern"; }      
	
		/**
		* Returns star type
		*/
		function type()
		{
			return $this->typeName;
		}							

		function imagePath($opt="")
		{
			if ($opt=="b")
			{
				return IMAGE_PATH."/stars/star".$this->typeId.".".IMAGE_EXT;
			}
			return IMAGE_PATH."/stars/star".$this->typeId."_small.".IMAGE_EXT;
		}

		/**
		* Returns type
		*/
		function entityCode() { return "s"; }	      
		
		/**
		* To-String function
		*/
		function __toString() 
		{ 
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->formatedCoords()." ".$this->name;			
		}
		
		function cellId()
		{
			if (!$this->coordsLoaded)
			{
				$this->loadCoords();
			}
			return $this->cellId;
		}		
		
		/**
		* Name star
		*/
		public function setNewName($name,$strict=1)
		{
			if ($strict == 0 || !$this->named)
			{
				dbquery("
				UPDATE
					stars
				SET
					name='".addslashes($name)."'
				WHERE
					id=".$this->id."
				");
				$this->name=$name;
				$this->named=true;
				return true;
			}
			return false;
		}
	}
?>
