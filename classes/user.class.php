<?PHP
	
	/**
	* Provides methods for accessing user information
	* and changing it.
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/
	class User
	{
		private $id;	// Database record id
		private $nick; // Unicke nickname
		private $setup; // Cheker if account is propperly setup
		private $valid; // Checker if class instance belongs to valid user
		private $maskMatrix; // Matrix for the "fog of war" effect in the space map
		
		/**
		* The constructor initializes and loads 
		* all importand data about this user
		*/
		function User($id)
		{
			$this->valid = false;
			$this->id = $id;

			$res = dbquery("
			SELECT 
				users.*,
				MD5(user_id) AS uid,
				MD5(user_logintime) AS lt,
				user_session_key AS sk
			FROM 
				users 
			WHERE 
				user_id='".$id."' 
			;");			
			if (mysql_num_rows($res)>0)		
			{
				$arr = mysql_fetch_assoc($res);

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
					race_id=".$arr['user_race_id']."			
				");
				if (mysql_num_rows($rres)>0)
				{
					$rarr = mysql_fetch_assoc($rres);
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
				}

				// Those are for session controll				
				$this->uid = $arr['uid'];
				$this->lt = $arr['lt'];
				$this->sk = $arr['sk'];
				
				$this->acttime = $arr['user_acttime'];
		    $this->race_id = $arr['user_race_id'];
		    $this->blocked_from = $arr['user_blocked_from'];
		    $this->blocked_to = $arr['user_blocked_to'];
		    $this->ban_reason = $arr['user_ban_reason'];
		    $this->ban_admin_id = $arr['user_ban_admin_id'];
		    $this->hmode_from = $arr['user_hmode_from'];
		    $this->hmode_to = $arr['user_hmode_to'];
		    $this->points = $arr['user_points'];
		    $this->deleted = $arr['user_deleted'];
		    $this->registered = $arr['user_registered'];
		    $this->show_adds = $arr['user_show_adds'];
		    $this->setup = $arr['user_setup']==1 ? true : false;
	

				$this->id=$arr['user_id'];
				$this->nick=$arr['user_nick'];
				$this->email=$arr['user_email'];
				$this->last_online=$arr['user_last_online'];
				$this->race_id=$arr['user_race_id'];
				$this->points=$arr['user_points'];
				$this->alliance_id=$arr['user_alliance_id'];
				$this->alliance_rank_id=$arr['user_alliance_rank_id'];
				$this->css_style=$arr['user_css_style'];
				$this->game_width=$arr['user_game_width'];
				$this->planet_circle_width=$arr['user_planet_circle_width'];
				$this->image_url=$arr['user_image_url'];
				$this->image_ext=$arr['user_image_ext'];
				$this->item_show=$arr['user_item_show'];
				$this->item_order_ship=$arr['user_item_order_ship'];
				$this->item_order_def=$arr['user_item_order_def'];
				$this->item_order_way=$arr['user_item_order_way'];
				$this->image_filter=$arr['user_image_filter'];
				$this->blocked_from=$arr['user_blocked_from'];
				$this->blocked_to=$arr['user_blocked_to'];
				$this->hmode_from=$arr['user_hmode_from'];
				$this->hmode_to=$arr['user_hmode_to'];
				$this->helpbox=$arr['user_helpbox'];
				$this->notebox=$arr['user_notebox'];
				 
				$this->admin=$arr['user_admin']==1 ? true : false;
				$this->ip=$_SERVER['REMOTE_ADDR'];
				$this->msg_preview=$arr['user_msg_preview'];
				$this->msgcreation_preview=$arr['user_msgcreation_preview'];
				$this->msgsignature=$arr['user_msgsignature'];
				$this->msg_copy=$arr['user_msg_copy'];
				$this->msg_blink=$arr['user_msg_blink'];
			
				$this->specialist_time=$arr['user_specialist_time'];
				$this->specialist_id=$arr['user_specialist_id'];
         
        $this->spyship_count=$arr['user_spyship_count'];
        $this->spyship_id=$arr['user_spyship_id'];
        $this->havenships_buttons=$arr['user_havenships_buttons'];
				 
				$this->valid=true;
				
		
			}
		}
		
		function isValid()
		{
			return $this->valid;
		}
		
		function isTimeout()
		{
			$cfg = Config::getInstance();
			return $this->acttime + $cfg->value('user_timeout') < time();			
		}
		
		function id()
		{
			return $this->id;
		}
		
		function isSetup()
		{
			return $this->setup;
		}		
		
		function setNotSetup()
		{
			$this->setup = false;
		}
		
		function nick()
		{
			return $this->nick;
		}
		
		function setRace($raceid)
		{
	    $sql = "
	    UPDATE
	    	users
	    SET
				user_race_id=".$raceid."
	    WHERE
	    	user_id='".$this->id."';";
	    dbquery($sql);					
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
		
		function raceName()
		{
			return $this->raceName;
		}

		/**
     * Adds a message to this users personal log
     * The message string is parsed for the users nickname
     *
     * @param string $zone
     * @param string $message
     * @param int $public
     * @return bool
     */
    public function addToUserLog($zone,$message,$public=1)
    {
			$search = array("{user}","{nick}");
			$replace = array($this->nick,$this->nick);
			$message = str_replace($search,$replace,$message);

      dbquery("
      INSERT INTO
				user_log
			(
				user_id,
				timestamp,
				zone,
				message,
				host,
				public
			)
			VALUES
			(
				".$this->id.",
				".time().",
				'".$zone."',
				'".$message."',
				'".gethostbyname($_SERVER['REMOTE_ADDR'])."',
				".intval($public)."
			);
      ");
			return true;
    }
	}

?>