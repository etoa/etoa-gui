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
		private $raceId;
		
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

				// Those are for session controll				
				$this->uid = $arr['uid'];
				$this->lt = $arr['lt'];
				$this->sk = $arr['sk'];

	    	$this->loadRaceData($arr['user_race_id']);	 				
				
				$this->acttime = $arr['user_acttime'];
		    
		    // deprecated
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

		//
		// Getters
		// 		
		
		/** 
		* Return if user is valid 
		*/
		function isValid() { 	return $this->valid;	}

		/**
		* Return if user is finally set up (main planet, race)
		*/
		function isSetup() 	{	return $this->setup; }		

		/**
		* Returns the users id
		*/
		function id() { return $this->id; }

		/**
		* Returns the users nickname
		*/
		function nick()	{ return $this->nick; }

		/**
		* Returns the name of the users race
		*/ 
		function raceName() 
		{ 			
			return $this->raceName; 
		}

		/**
		* Returns the id of the users race
		*/ 
		function raceId() 
		{ 			
			return $this->raceId; 
		}


		//
		// Methods
		//

		/**
		* Returns true if the users session has timed out
		*/
		function isTimeout()
		{
			$cfg = Config::getInstance();
			return $this->acttime + $cfg->value('user_timeout') < time();			
		}
		
		
		/**
		* Set the users race
		*/
		public function setRace($raceId)
		{
	    if ($this->loadRaceData($raceId))
	    {
		    $sql = "
		    UPDATE
		    	users
		    SET
					user_race_id=".$raceid."
		    WHERE
		    	user_id='".$this->id."';";
		    dbquery($sql);		
		    return true;    	
	    }
	    return false;
		}
		
		/**
		* Loads data for the given race and sets it 
		* as the users race
		*/
		private function loadRaceData($raceId)
		{
			$rres = dbquery("
			SELECT
				race_name,
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
				return true;
			}

	    $this->raceId = 0;
	    $this->raceName = "Keine Rasse";
			return false;
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