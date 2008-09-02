<?PHP
	
	/**
	* Class for Resources
	*/
	class Resources extends Explore
	{
		protected $id;
		private $name;
		protected $description;	
		protected $type;	
		
		/**
		* The constructor
		*/
		function Resources($id=0)
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
		function exploreCodeString() { return "Resourcen"; }      
	
	
		/**
		* Returns type
		*/
		function exploreCode() { return "r"; }	
		
		/**
		* Load explore data
		*/
		function loadData ()
		{
			$res = dbquery("SELECT
								*
							FROM
								resources
							WHERE
								id='".$this->id."'
							LIMIT
								1;");
			if (mysql_num_rows($res)>0)
		  	{

		  		$arr=mysql_fetch_assoc($res);
				
				$this->name = $arr["name"];
				$this->description = RES_ICON_METAL.nf($arr["res_metal"])." ".RES_METAL."<br style=\"clear:both\" />".RES_ICON_CRYSTAL.nf($arr["res_crystal"])." ".RES_CRYSTAL."<br style=\"clear:both\" />".RES_ICON_PLASTIC.nf($arr["res_plastic"])." ".RES_PLASTIC."<br style=\"clear:both\" />".RES_ICON_FUEL.nf($arr["res_fuel"])." ".RES_FOOD."<br style=\"clear:both\" />".RES_ICON_FOOD.nf($arr["res_food"])." ".RES_FOOD."<br style=\"clear:both\" />".RES_ICON_PEOPLE.nf($arr["res_people"])." Bewohner<br style=\"clear:both\" />".RES_ICON_POWER.nf($arr["res_power"])." ".RES_POWER."<br style=\"clear:both\" />";
			}
		}
		
		/**
		* Return Description
		*/
		function description () { return $this->description; }
									
		
	}
?>
