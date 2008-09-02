<?PHP
	
	/**
	* Class for Pirates
	*/
	class Pirates extends Explore
	{
		protected $id;
		private $name;
		protected $description;	
		protected $type;	
		
		/**
		* The constructor
		*/
		function Pirates($id=0)
		{
			$this->isValid = true;
			$this->id = $id;			
     		$this->isVisible = false;
			
			$this->loadData();
			
		}

		public function allowedFleetActions()
    	{
    		return array("attack","flight","explore");
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
		function ownerId() { return 0; }      
	
		/**
		* Returns type string
		*/                        
		function exploreCodeString() { return "Piraten"; }      
	
	
		/**
		* Returns type
		*/
		function exploreCode() { return "p"; }	
		
		/**
		* Load explore data
		*/
		function loadData ()
		{
			$res = dbquery("SELECT
								*
							FROM
								bots
							WHERE
								id='".$this->id."'
							LIMIT
								1;");
			if (mysql_num_rows($res)>0)
		  	{
		  		$arr=mysql_fetch_assoc($res);
				
				$this->name = $arr["name"];
				$this->rank = rank($level,'p');
				$this->description = $this->name."<br style=\"clear:both\" />Rang: ".$this->rank."<br style=\"clear:both\" />";
			}
		}
		
		/**
		* Return Description
		*/
		function description() { return $this description; }
									
		
	}
?>
