<?PHP
	class Race
	{
		private $id;
		private $name;
		private $researchTime = 1;
		private $buildTime = 1;
		private $fleetTime = 1;
		private $metal = 1;
		private $crystal = 1;
		private $plastic = 1;
		private $fuel = 1;
		private $food;
		private $power;
		private $population;	  		
		
		public function Race($id=0)
		{
			if ($id > 0)
			{
				$rres = dbquery("
				SELECT
					*
				FROM
					races
				WHERE
					race_id=".$id."			
				");
				if (mysql_num_rows($rres)>0)
				{
					$rarr = mysql_fetch_assoc($rres);
			    $this->id = $id;
					$this->name = $rarr['race_name'];
					$this->researchTime = $rarr['race_f_researchtime'];
					$this->buildTime = $rarr['race_f_buildtime'];
					$this->fleetTime = $rarr['race_f_fleettime'];
					$this->metal = $rarr['race_f_metal'];
					$this->crystal = $rarr['race_f_crystal'];
					$this->plastic = $rarr['race_f_plastic'];
					$this->fuel = $rarr['race_f_fuel'];
					$this->food = $rarr['race_f_food'];
					$this->power = $rarr['race_f_power'];
					$this->population = $rarr['race_f_population'];		
					return;
				}
			}

	    $this->id = 0;
	    $this->name = "Keine Rasse";
			$this->researchTime = 1;
			$this->buildTime = 1;
			$this->fleetTime = 1;
			$this->metal = 1;
			$this->crystal = 1;
			$this->plastic = 1;
			$this->fuel = 1;
			$this->food = 1;
			$this->power = 1;
			$this->population = 1;	    
	    
		}
		
		public function __set($key, $val)
		{
			try
			{
				throw new EException("Properties der Klasse ".__CLASS__." sind read-only!");
				/*
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				$this->$key = $val;*/
			}
			catch (EException $e)
			{
				echo $e;
			}
		}
		
		public function __get($key)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in ".__CLASS__);
					
				if ($key == "speedFactor")
				{
					if ($this->fleetTime!=1)
					{
						return 2-$this->fleetTime;
					}
					else
					{
						return 1;
					}		
				}
					
				return $this->$key;
			}
			catch (EException $e)
			{
				echo $e;
				return null;
			}
		}		
		
		
		
	}

?>