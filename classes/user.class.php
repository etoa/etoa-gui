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
		protected $rating;
		
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
				$this->chatadmin=$arr['user_chatadmin']==1 ? true : false;
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
				$this->allianceTag = "";
				$this->allianceRankName = "";				

				$this->rank = $arr['user_rank'];
				$this->rankHighest = $arr['user_rank_highest'];


		    $this->specialistId = $arr['user_specialist_id'];
		    $this->specialistTime = $arr['user_specialist_time'];


				// Todo: remove and add where it is needed
				$this->raceId = $arr['user_race_id'];
	
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
		
		final public function allianceTag() 
		{ 
			if ($this->allianceTag == "") 	{ $this->loadAllianceData(); }
			return $this->allianceTag;
		}
		
		final public function allianceRankName() 
		{ 
			if ($this->allianceRankName == "") 	{ $this->loadAllianceData(); }
			return $this->allianceRankName;
		}

		public function raceName() 
		{
			if (!$this->raceName)
				$this->loadRaceData($this->raceId());
			return $this->raceName; 
		}

		
		/**
		* Gets the users different rating statistics
		*/
		public function rating($field)
		{
			if (!isset($this->rating[$field]))
			{
				$res = dbquery("
				SELECT
					* 
				FROM 
					user_ratings 
				WHERE 
					id=".$this->id.";");
				if (mysql_num_rows($res)>0)
				{
					$arr = mysql_fetch_assoc($res);
					$this->rating['battle_rating'] = $arr['battle_rating'];
					$this->rating['trade_rating'] = $arr['trade_rating'];
					$this->rating['diplomacy_rating'] = $arr['diplomacy_rating'];
					$this->rating['battles_fought'] = $arr['battles_fought'];
					$this->rating['battles_won'] = $arr['battles_won'];
					$this->rating['battles_lost'] = $arr['battles_lost'];
					$this->rating['trades_sell'] = $arr['trades_sell'];
					$this->rating['trades_buy'] = $arr['trades_buy'];
				}
				else
				{
					dbquery("
					INSERT INTO 
						user_ratings
					(id)
					VALUES
					(".$this->id.")
					");
					$this->rating['battle_rating'] = 0;
					$this->rating['trade_rating'] = 0;
					$this->rating['diplomacy_rating'] = 0;
					$this->rating['battles_fought'] = 0;
					$this->rating['battles_won'] = 0;
					$this->rating['battles_lost'] = 0;
					$this->rating['trades_sell'] = 0;
					$this->rating['trades_buy'] = 0;
				}
			}
			return $this->rating[$field];			
		}

		/**
		* Add battle rating
		*/
		function addBattleRating($rating,$reason="")
		{
			if ($points!=0)
			{
				dbquery("
				UPDATE
					user_ratings
				SET
					battle_rating=battle_rating+".$rating."
				WHERE
					id=".$this->id.";");
				if ($reason!="")
					add_log(17,"Der Spieler ".$this->id." erhält ".$rating." Kampfpunkte. Grund: ".$reason);
			}
		}
		
		/**
		* Add trade rating
		*/
		function addTradeRating($rating,$reason="")
		{
			dbquery("
			UPDATE
				user_ratings
			SET
				trade_rating=trade_rating+".$rating."
			WHERE
				id=".$this->id.";");			
			if ($reason!="")
				add_log(17,"Der Spieler ".$this->id." erhält ".$rating." Handelspunkte. Grund: ".$reason);
		}
		
		/**
		* Add diplomacy rating
		*/
		function addDiplomacyRating($rating,$reason="")
		{
			dbquery("
			UPDATE
				user_ratings
			SET
				  	diplomacy_rating=  	diplomacy_rating+".$rating."
			WHERE
				id=".$this->id.";");			
			if ($reason!="")
			add_log(17,"Der Spieler ".$this->id." erhält ".$rating." Diplomatiepunke. Grund: ".$reason);
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
					$this->allianceTag = $aarr[0];
					
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
					user_race_id=".$raceId."
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

	/**
	* Benutzer löschen
	*
	*/
	function delete($self=false,$from="")
	{
  	global $conf;
   	define(FLEET_ACTION_RESS,$conf['market_ship_action_ress']['v']); // Ressourcen
   	define(FLEET_ACTION_SHIP,$conf['market_ship_action_ship']['v']); // Schiffe

		$utx = new userToXml($this->id);
		if ($xmlfile = $utx->toCacheFile())
		{

			//
			//Flotten und deren Schiffe löschen
			//
			$fres=dbquery("
				SELECT
					id
				FROM
					fleet
				WHERE
					user_id='".$this->id."';
			");
			if (mysql_num_rows($fres)>0)
			{
				while ($farr=mysql_fetch_assoc($fres))
				{
					// Flotten-Schiffe löschen
					dbquery("
						DELETE FROM
							fleet_ships
						WHERE
							fs_fleet_id='".$farr['id']."';
					");
				}
			}
			// Flotten löschen
			dbquery("
				DELETE FROM
					fleet
				WHERE
					user_id=".$this->id.";
			");


			//
			//Planeten Reseten und Handelschiffe die auf dem Weg zu einem Planeten sind löschen
			//
			$pres=dbquery("
				SELECT
					id,
					planet_name,
					planet_res_metal,
					planet_res_crystal,
					planet_res_plastic,
					planet_res_fuel,
					planet_res_food
				FROM
					planets
				WHERE
					planet_user_id='".$this->id."';
			");
			if (mysql_num_rows($pres)>0)
			{
				while ($parr=mysql_fetch_assoc($pres))
				{
					//löscht alle markt-handelschiffe die auf dem weg zu dem user sind
                    $fres2=dbquery("
						SELECT
							id
						FROM
							fleet
						WHERE
							entity_to='".$parr['id']."'
							AND (action='".FLEET_ACTION_RESS."' OR action='".FLEET_ACTION_SHIP."');
					");
                    if (mysql_num_rows($fres2)>0)
                    {
                        while ($farr2=mysql_fetch_assoc($fres2))
                        {
                        	// Flotten-Schiffe löschen
                            dbquery("
								DELETE FROM
									fleet_ships
								WHERE
									fs_fleet_id='".$farr2['id']."';
							");
                        }
                    }
					//Setzt Planet zurück
					reset_planet($parr['id']);
				}
			}


			//
			// Allianz löschen (falls alleine) oder einen Nachfolger bestimmen
			//
			if ($this->allianceId > 0)
			{
				$ares=dbquery("
					SELECT
						u.user_id,
						a.alliance_founder_id
					FROM
						users AS u
						INNER JOIN
						alliances AS a
						ON u.user_alliance_id = a.alliance_id
						AND a.alliance_id=".$this->allianceId."
						AND u.user_id!='".$this->id."'
					GROUP BY
						u.user_id
					ORDER BY
						u.user_points DESC;
				");
				if (mysql_num_rows($ares)>0)
				{
					$aarr=mysql_fetch_assoc($ares);

					 // Wenn der User der Gründer der Allianz ist wird der User mit den höchsten Punkten zum neuen Allianzgründer
					if ($this->id==$aarr['alliance_founder_id'])
					{
						dbquery("
							UPDATE
								alliances
							SET
								alliance_founder_id='".$aarr['user_id']."'
							WHERE
								alliance_id='".$this->allianceId."';
						");
					}
				}
				else
				{
					// Wenn der User das einzige/letzte Mitglied der Allianz ist wird sie aufgelöst
					delete_alliance($this->allianceId);
				}
			}



			//
			//Rest löschen
			//

			dbquery("DELETE FROM alliance_applications WHERE user_id='".$this->id."';");


			//Baulisten löschen
			dbquery("DELETE FROM shiplist WHERE shiplist_user_id='".$this->id."';");		// Schiffe löschen
			dbquery("DELETE FROM deflist WHERE deflist_user_id='".$this->id."';");			// Verteidigung löschen
			dbquery("DELETE FROM techlist WHERE techlist_user_id='".$this->id."';");		// Forschung löschen
			dbquery("DELETE FROM buildlist WHERE buildlist_user_id='".$this->id."';");		// Gebäude löschen

			//Buddyliste löschen
			dbquery("DELETE FROM buddylist WHERE bl_user_id='".$this->id."' OR bl_buddy_id='".$this->id."';");

			//Markt Angebote löschen
			dbquery("DELETE FROM market_ressource WHERE user_id='".$this->id."' AND ressource_buyable='1';"); 	// Rohstoff Angebot
			dbquery("DELETE FROM market_ship WHERE user_id='".$this->id."' AND ship_buyable='1';"); 				// Schiff Angebot
			dbquery("DELETE FROM market_auction WHERE auction_user_id='".$this->id."' AND auction_buyable='1';"); // Auktionen

			//Notitzen löschen
			$np = new Notepad($this->id);
			$numNotes = $np->deleteAll();
			unset($np);

			//Gespeicherte Koordinaten löschen
			dbquery("DELETE FROM bookmarks WHERE user_id='".$this->id."';");
			dbquery("DELETE FROM fleet_bookmarks WHERE user_id='".$this->id."';");

			//'user' Info löschen
			//dbquery("DELETE FROM user_log WHERE log_user_id='".$this->id."';"); 			//Log löschen
			dbquery("DELETE FROM user_multi WHERE user_multi_user_id='".$this->id."' OR user_multi_multi_user_id='".$this->id."';"); //Multiliste löschen
			dbquery("DELETE FROM user_points WHERE point_user_id='".$this->id."';"); 					//Punkte löschen
			dbquery("DELETE FROM user_requests WHERE request_user_id='".$this->id."';"); 				//Nickänderungsanträge löschen
			dbquery("DELETE FROM user_sitting WHERE user_sitting_user_id='".$this->id."';"); 			//Sitting löschen
			dbquery("DELETE FROM user_sitting_date WHERE user_sitting_date_user_id='".$this->id."';"); //Sitting Daten löschen
			// Todo: clean tickets

			//
			//Benutzer löschen
			//
			dbquery("DELETE FROM users WHERE user_id='".$this->id."';");




			//Log schreiben
			if($self)
				add_log("3","Der Benutzer ".$this->nick." hat sich selbst gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());
			elseif(!$self && $from!="")
				add_log("3","Der Benutzer ".$this->nick." wurde von ".$from." gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());
			else
				add_log("3","Der Benutzer ".$this->nick." wurde gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());

			$text ="Hallo ".$this->nick."
			
Dein Accouont bei EtoA: Escape to Andromeda ( http://www.etoa.ch ) wurde auf Grund von Inaktivität 
oder auf eigenem Wunsch nun gelöscht.

Mit freundlichen Grüßen,
die Spielleitung";
			send_mail('',$this->email,'Accountlöschung bei Escape to Andromeda',$text,'','');
			
			return true;

		}
		else
		{
			error_msg("Konnte UserXML für ".$this->id." nicht exportieren, User nicht gelöscht!");
		}		
	}



		/**
		* Returns the total number of users
		*/
		static public function count()
		{
			$ures = dbquery("SELECT COUNT(user_id) FROM users;");
			$uarr = mysql_fetch_row($ures);
			return $uarr[0];			
		}
	
		/**
		* Registers a new user
		*/
		static public function register($data, &$errorCode, $welcomeMail=1)
		{
			$time = time();
			
			if ($data['name']=="" || $data['nick']=="" || $data['email']=="")
			{
				$errorCode = "Nicht alle Felder sind ausgef&uuml;llt!";
				return false;
			}

			$nick=str_replace(' ','',trim($data['nick']));
			
			if (!checkValidNick($nick) || !checkValidName($data['name']))
			{			
      	$errorCode = "Du hast ein unerlaubtes Zeichen im Benutzernamen oder im vollst&auml;ndigen Namen!";
				return false;			
			}
			
			$nick_length=strlen(utf8_decode($nick));
			if($nick=='')
			{
      	$errorCode = "Dein Nickname darf nicht nur aus Leerzeichen bestehen!";
				return false;
			}

			if($nick_length<NICK_MINLENGHT || $nick_length>NICK_MAXLENGHT)
			{						
				$errorCode = "Dein Nickname muss mindestens ".NICK_MINLENGHT." Zeichen und maximum ".NICK_MAXLENGHT." Zeichen haben!";
				return false;
			}			

    	if (!checkEmail($data['email']))
    	{
    		$errorCode = "Diese E-Mail-Adresse scheint ung&uuml;ltig zu sein. Pr&uuml;fe nach, ob dein E-Mail-Server online ist und die Adresse im korrekten Format vorliegt!";
    		return false;
    	}

  	  $res = mysql_query("
  	  SELECT 
  	  	user_id 
  	  FROM 
  	  	users 
  	  WHERE 
  	  	user_nick='".$nick."' 
  	  	OR user_email_fix='".$data['email']."';");
  	  if (mysql_num_rows($res)>0)
  	  {
      	$errorCode = "Der Benutzer mit diesem Nicknamen oder dieser E-Mail-Adresse existiert bereits!";
      	return false;
      }  	  	  
  	  
  	  $pw = (isset($data['password']) && $data['password']!="") ? $data['password'] : mt_rand(100000000,9999999999);
      if (dbquery("
      INSERT INTO
      users (
          user_name,
          user_nick,
          user_password,
          user_email,
          user_email_fix,
          user_race_id,
          user_ghost,
          user_registered
          )
      VALUES
          ('".$data['name']."',
          '".$nick."',
          '".pw_salt($pw,$time)."',
          '".$data['email']."',
          '".$data['email']."',
          '".(isset($data['race']) ? $data['race'] : 0)."',
          '".(isset($data['ghost']) ? $data['ghost'] : 0)."',
          '".$time."');"))
      {
      	$errorCode = mysql_insert_id();
				dbquery("
				INSERT INTO 
					user_properties
				(id)
				VALUES
				(".$errorCode.")
				");      
				dbquery("
				INSERT INTO 
					user_ratings
				(id)
				VALUES
				(".$errorCode.")
				");      
				
				if (!isset($data['password']))
        	add_log(3,"Der Benutzer ".$nick." (".$data['name'].", ".$data['email'].") hat sich registriert!");
        else
        	add_log(3,"Der Benutzer ".$nick." (".$data['name'].", ".$data['email'].") wurde registriert!");
				
				if ($welcomeMail == 1)
				{
		      $email_text = "Hallo ".$data['nick']."\n\nDu hast dich erfolgreich beim Sci-Fi Browsergame Escape to Andromeda registriert.\nHier nochmals deine Daten:\n\n";
		      $email_text.= "Universum: ".GAMEROUND_NAME."\n";
		      $email_text.= "Name: ".$data['name']."\n";
		      $email_text.= "E-Mail: ".$data['email']."\n\n";
		      $email_text.= "Nick: ".$data['nick']."\n";
		      $email_text.= "Passwort: ".$pw."\n\n";
		      $email_text.= "WICHTIG: Gib das Passwort an niemanden weiter. Gib dein Passwort auch auf keiner Seite ausser der Login- und der Einstellungs-Seite ein. Ein Game-Admin oder Entwickler wird dich auch nie nach dem Passwort fragen!\n";
		      $email_text.= "Desweiteren solltest du dich mit den Regeln (".LOGINSERVER_URL."?page=regeln) bekannt machen, da ein Regelverstoss eine (zeitweilige) Sperrung deines Accounts zur Folge haben kann!\n\n";
		      $email_text.= "Viel Spass beim Spielen!\nDas EtoA-Team";
		
		      send_mail(0,$data['email'],"EtoA Registrierung",$email_text,"","left",1);			
				}
					  	
	      	return true;
	    }	
	
			$errorCode = "Ein unbekannter Fehler trat auf!";
      return false;
		}
		

    
	}

?>