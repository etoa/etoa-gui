<?PHP
	
	/**
	* Provides methods for accessing user information
	* and changing it.
	*
	* @author Nicolas Perrenoud<mrcage@etoa.ch>
	*/
	class User
	{
		protected $id;	// Database record id
		protected $nick; // Unicke nickname
		protected $setup; // Cheker if account is propperly setup
		protected $valid; // Checker if class instance belongs to valid user
		protected $maskMatrix; // Matrix for the "fog of war" effect in the space map
		protected $raceId;
		
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

			
				$this->nick=$arr['user_nick'];
				$this->realName=$arr['user_name'];
				$this->email=$arr['user_email'];
				$this->emailFix=$arr['user_email_fix'];

				$this->last_online=$arr['user_last_online'];
				$this->acttime = $arr['user_acttime'];
				$this->points=$arr['user_points'];
		    $this->blocked_from = $arr['user_blocked_from'];
		    $this->blocked_to = $arr['user_blocked_to'];
		    $this->ban_reason = $arr['user_ban_reason'];
		    $this->ban_admin_id = $arr['user_ban_admin_id'];
		    $this->hmode_from = $arr['user_hmode_from'];
		    $this->hmode_to = $arr['user_hmode_to'];
		    $this->points = $arr['user_points'];
		    $this->deleted = $arr['user_deleted'];
		    $this->registered = $arr['user_registered'];
		    $this->setup = $arr['user_setup']==1 ? true : false;
				$this->admin=$arr['user_admin']==1 ? true : false;
				$this->ip=$_SERVER['REMOTE_ADDR'];
				$this->blocked_from=$arr['user_blocked_from'];
				$this->blocked_to=$arr['user_blocked_to'];
				$this->hmode_from=$arr['user_hmode_from'];
				$this->hmode_to=$arr['user_hmode_to'];
				$this->specialist_time=$arr['user_specialist_time'];
				$this->specialist_id=$arr['user_specialist_id'];
				$this->visits = $arr['user_visits'];
				
				$this->profileImage = $arr['user_profile_img'];
				$this->profileText = $arr['user_profile_text'];
				$this->profileBoardUrl = $arr['user_profile_board_url'];
				$this->signature = $arr['user_signature'];

				$this->allianceId = $arr['user_alliance_id'];
				$this->allianceRankId = $arr['user_alliance_rank_id'];
				$this->allianceName = "";
				$this->allianceRankName = "";				

				$this->rank = $arr['user_rank'];
				$this->rankHighest = $arr['user_rank_highest'];


		    $this->specialistId = $arr['user_specialist_id'];
		    $this->specialistTime = $arr['user_specialist_time'];


				// Todo: remove and add where it is needed
	    	$this->loadRaceData($arr['user_race_id']);	 				
	
				$this->rating = array();
	
				$this->valid=true;
			}
		}

		//
		// Getters
		// 		
		
		final public function isValid() { 	return $this->valid;	}
		final public function isSetup() 	{	return $this->setup; }		
		final public function id() { return $this->id; }
		final public function nick()	{ return $this->nick; }
		final public function raceId() { return $this->raceId; }
		final public function visits() { return $this->visits; }
		final public function profileImage() { return $this->profileImage; }
		final public function profileText() { return $this->profileText; }
		final public function profileBoardUrl() { return $this->profileBoardUrl; }
		final public function registered() { return $this->registered; }
		final public function points() { return $this->points; }
		final public function rank() { return $this->rank; }
		final public function rankHighest() { return $this->rankHighest; }
		final public function allianceId() { return $this->allianceId; }
		final public function allianceRankId() { return $this->allianceRankId; }
		final public function lastOnline() { return $this->last_online; }
		final public function signature() { return $this->signature; }
		final public function specialistId() { return $this->specialistId; }
		final public function specialistTime() { return $this->specialistTime; }
	

		final public function allianceName() 
		{ 
			if ($this->allianceName == "") 	{ $this->loadAllianceData(); }
			return $this->allianceName;
		}
		
		final public function allianceRankName() 
		{ 
			if ($this->allianceRankName == "") 	{ $this->loadAllianceData(); }
			return $this->allianceRankName;
		}

		public function raceName() 
		{ 			
			return $this->raceName; 
		}

		public function rating($field)
		{
			if (!isset($this->rating[$field]))
			{
				$res = dbquery("SELECT * FROM user_ratings WHERE id=".$this->id.";");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_array($res);
					$this->rating['battle_rating'] = $arr['battle_rating'];
					$this->rating['trade_rating'] = $arr['trade_rating'];
					$this->rating['diplomacy_rating'] = $arr['diplomacy_rating'];
				}
				else
				{
					$this->rating['battle_rating'] = 0;
					$this->rating['trade_rating'] = 0;
					$this->rating['diplomacy_rating'] = 0;
				}
			}
			return $this->rating[$field];			
		}

		public function setAllianceId($id) 
		{ 
			$this->allianceId = $id; 
			dbquery("
			UPDATE 
				users 
			SET 
				user_alliance_rank_id=0,
				user_alliance_id=".$id." 
			WHERE user_id='".$this->id()."';");
		}


		//
		// Methods
		//

		/**
		* Returns true if the users session has timed out
		*/
		final public function isTimeout()
		{
			$cfg = Config::getInstance();
			return $this->acttime + $cfg->value('user_timeout') < time();			
		}
		
		/**
		* Load alliance data
		*/
		function loadAllianceData()
		{
			if ($this->allianceId > 0)
			{
				$ares = dbquery("
				SELECT
					alliance_tag,
					alliance_name,
					alliance_founder_id
				FROM
					alliances
				WHERE
					alliance_id=".$this->allianceId.";
				");	
				if (mysql_num_rows($ares)>0)				
				{
					$aarr = mysql_fetch_row($ares);
					$this->allianceName = "[".$aarr[0]."] ".$aarr[1];
					
					if ($aarr[2] == $this->id)
					{
						$this->allianceRankName = "Gründer";
					}
					elseif ($this->allianceRankId > 0)
					{
						$ares = dbquery("
						SELECT
							rank_name
						FROM
							alliance_ranks
						WHERE
							rank_alliance_id=".$this->allianceId."
							AND rank_id=".$this->allianceRankId.";
						");	
						if (mysql_num_rows($ares)>0)				
						{
							$aarr = mysql_fetch_row($ares);
							$this->allianceRankName = $aarr[0];
						}					
					}					
				}					
			}				
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
				race_name
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
    final public function addToUserLog($zone,$message,$public=1)
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
    
		function increaseVisitorCounter()
		{
			dbquery("
			UPDATE 
				users 
			SET 
				user_visits=user_visits+1 
			WHERE 
				user_id='".intval($this->id)."';");
			$this->visits++;
		}


    
	}

?>