<?PHP
	
	/**
	* Provides methods for accessing 
	* the current logged in user
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/
	class CurrentUser extends User
	{


		
		/**
		* Set setup status to false
		*/
		public function setNotSetup()
		{
			$this->setup = false;
		}
		
		/**
		* Loads data for the given race and sets it 
		* as the users race
		* THIS FUNCTION OVERLOADS THE DEFAULT
		*/
		private function loadRaceData($raceId)
		{
			$rres = dbquery("
			SELECT
				race_name,
		  	race_f_researchtime,
				race_f_buildtime,
				race_f_fleettime,
				race_f_metal,
				race_f_crystal,
				race_f_plastic,
				race_f_fuel,
				race_f_food,
				race_f_power,
				race_f_population		
			FROM
				races
			WHERE
				race_id=".$raceId."			
			");
			if (mysql_num_rows($rres)>0)
			{
				$rarr = mysql_fetch_assoc($rres);
		    $this->raceId = $raceId;
				$this->raceName = $rarr['race_name'];
				$this->raceResearchtime = $rarr['race_f_researchtime'];
				$this->raceBuildtime = $rarr['race_f_buildtime'];
				$this->raceFleettime = $rarr['race_f_fleettime'];
				$this->raceMetal = $rarr['race_f_metal'];
				$this->raceCrystal = $rarr['race_f_crystal'];
				$this->racePlastic = $rarr['race_f_plastic'];
				$this->raceFuel = $rarr['race_f_fuel'];
				$this->raceFood = $rarr['race_f_food'];
				$this->racePower = $rarr['race_f_power'];
				$this->racePopulation = $rarr['race_f_population'];
				return true;
			}

	    $this->raceId = 0;
	    $this->raceName = "Keine Rasse";
			$this->raceResearchtime = 1;
			$this->raceBuildtime = 1;
			$this->raceFleettime = 1;
			$this->raceMetal = 1;
			$this->raceCrystal = 1;
			$this->racePlastic = 1;
			$this->raceFuel = 1;
			$this->raceFood = 1;
			$this->racePower = 1;
			$this->racePopulation = 1;
			return false;
		}

		function setSetupFinished()
		{
	    $sql = "
	    UPDATE
	    	users
	    SET
				user_setup=1
	    WHERE
	    	user_id='".$this->id."';";
	    dbquery($sql);
	    $this->setup=true;					
		}

		function loadDiscoveryMask()
		{
			$res = dbquery("
			SELECT
				discoverymask
			FROM				
				users
			WHERE
				user_id=".$this->id()."
			");
			$this->dmask = '';
			$arr = mysql_fetch_row($res);
			if ($arr[0]=='')
			{
				for ($x=1;$x<=30;$x++)
				{
					for ($y=1;$y<=30;$y++)
					{
						$this->dmask.= '0';
					}
				}
			}
			else
			{
				$this->dmask=$arr[0];
			}			
		}

		function discovered($absX,$absY)
		{
			$cfg = Config::getInstance();
			$sy_num=$cfg->param2('num_of_sectors');
			$cy_num=$cfg->param2('num_of_cells');
			
			if (!isset($this->dmask))
			{
				$this->loadDiscoveryMask();
			}	
			
			$pos = $absX + ($cy_num*$sy_num)*($absY-1)-1;
			return ($this->dmask{$pos}%4);		
		}
		
		function setDiscovered($absX,$absY,$owner=1,$save=1)
		{
			$cfg = Config::getInstance();
			$sx_num=$cfg->param1('num_of_sectors');
			$cx_num=$cfg->param1('num_of_cells');
			$sy_num=$cfg->param2('num_of_sectors');
			$cy_num=$cfg->param2('num_of_cells');
			
			for ($x=$absX-1; $x<=$absX+1; $x++)
			{
				for ($y=$absY-1; $y<=$absY+1; $y++)
				{
					$pos = $x + ($cy_num*$sy_num)*($y-1)-1;
					if ($pos>= 0 && $pos <= $sx_num*$sy_num*$cx_num*$cy_num)
					{
						if ($owner==1)
						{
							$this->dmask{$pos} = '5';				
						}
						else
						{
							$this->dmask{$pos} = '1';
						}
					}
				}
			}	
			
			if ($save==1)
			{
				$this->saveDiscoveryMask();
			}			
		}	

		function saveDiscoveryMask()
		{
			dbquery("
			UPDATE
				users
			SET
				discoverymask='".$this->dmask."'
			WHERE
				user_id=".$this->id()."
			");
		}
		
		function raceSpeedFactor()
		{
			if ($this->raceFleettime!=1)
			{
				return 2-$this->raceFleettime;
			}
			else
			{
				return 1;
			}		
		}
	
	}

?>