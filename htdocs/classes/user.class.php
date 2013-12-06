<?PHP
//////////////////////////////////////////////////////
// The Andromeda-Project-Browsergame                //
// Ein Massive-Multiplayer-Online-Spiel             //
// Programmiert von Nicolas Perrenoud<mail@nicu.ch> //
// als Maturaarbeit '04 am Gymnasium Oberaargau	    //
//////////////////////////////////////////////////////
//////////////////////////////////////////////////////

/**
* Provides methods for accessing user information
* and changing it.
*
* @author Nicolas Perrenoud<mrcage@etoa.ch>
*/
class User
{
	const tableName = "users";

	// Fields
	protected $id;	// Database record id
	protected $nick; // Unicke nickname
	protected $setup; // Cheker if account is propperly setup
	protected $isValid; // Checker if class instance belongs to valid user
	protected $maskMatrix; // Matrix for the "fog of war" effect in the space map
	protected $realName;
	protected $email;
	protected $emailFix;
	protected $lastOnline;
	protected $acttime;
	protected $points;
	protected $blocked_from;
	protected $blocked_to;
	protected $ban_reason;
	protected $ban_admin_id;
	protected $hmode_from;
	protected $hmode_to;
	protected $holiday=null;
	protected $locked=null;
	protected $deleted;
	protected $monitored;
	protected $registered;
	protected $chatadmin;
	protected $admin;
	protected $ip;
	protected $visits;
	protected $profileImage;
	protected $profileText;
	protected $profileBoardUrl;
	protected $signature;
	protected $avatar;
	protected $allianceRankId;
	protected $allianceName;
	protected $allianceTag;
	protected $allianceRankName;
	protected $rank;
	protected $rankHighest;
	protected $specialistId;
	protected $specialistTime;
	protected $boostBonusProduction;
	protected $boostBonusBuilding;
	protected $specialist = null;
	protected $ghost;
	protected $lastInvasion;
	protected $allianceShippoints;

	protected $sittingDays;

	// Sub-objects and their id's
	protected $raceId;
	protected $race = null;
	protected $allianceId;
	protected $alliance = null;
	protected $rating = null;
	protected $properties = null;
	protected $buddylist = null;
	protected $changedFields;

	/**
	* The constructor initializes and loads
	* all importand data about this user
	*/
	function User($id)
	{
		$this->isValid = false;
		$this->id = $id;

		$res = dbquery("
		SELECT
			".self::tableName.".*
		FROM
			".self::tableName."
		WHERE
			user_id='".$id."'
		LIMIT 1
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);

			$this->nick=$arr['user_nick'];
			$this->realName=$arr['user_name'];
			$this->email=$arr['user_email'];
			$this->emailFix=$arr['user_email_fix'];

			$this->lastOnline=$arr['user_logouttime'];
			$this->acttime = null;
			$this->points=$arr['user_points'];

			$this->blocked_from = $arr['user_blocked_from'];
			$this->blocked_to = $arr['user_blocked_to'];
			$this->ban_reason = $arr['user_ban_reason'];
			$this->ban_admin_id = $arr['user_ban_admin_id'];

			$this->hmode_from = $arr['user_hmode_from'];
			$this->hmode_to = $arr['user_hmode_to'];

			$this->deleted = $arr['user_deleted'];

				//$this->monitored = ($arr['user_observe']!="" && stristr($arr['user_observe'],"bug")) ? true : false;
				$this->monitored = ($arr['user_observe']!="") ? true : false;

			$this->registered = $arr['user_registered'];
			$this->setup = $arr['user_setup']==1 ? true : false;
			$this->chatadmin=$arr['user_chatadmin']==1 ? true : false;
			$this->admin=$arr['admin']==1 ? true : false;
			$this->ghost=$arr['user_ghost']==1 ? true : false;

			$this->ip=$_SERVER['REMOTE_ADDR'];

			$this->visits = $arr['user_visits'];

			$this->profileImage = $arr['user_profile_img'];
			$this->profileText = $arr['user_profile_text'];
			$this->profileBoardUrl = $arr['user_profile_board_url'];
			$this->signature = $arr['user_signature'];
			$this->avatar = $arr['user_avatar'];


			$this->allianceId = $arr['user_alliance_id'];
			$this->allianceRankId = $arr['user_alliance_rank_id'];

			$this->sittingDays = $arr['user_sitting_days'];

			$this->allianceName = "";
			$this->allianceTag = "";
			$this->allianceRankName = "";

			$this->rank = $arr['user_rank'];
			$this->rankHighest = $arr['user_rank_highest'];

			$this->specialistId = $arr['user_specialist_id'];
			$this->specialistTime = $arr['user_specialist_time'];
			
			$this->boostBonusProduction = $arr['boost_bonus_production'];
			$this->boostBonusBuilding = $arr['boost_bonus_building'];
			
			$this->lastInvasion = $arr['lastinvasion'];

			$this->raceId = $arr['user_race_id'];
			
			$this->allianceShippoints = $arr['user_alliace_shippoints'];

			$this->changedFields = array();

			$this->isValid=true;
		}
		else
		{
			$this->nick = "Niemand";

			$this->points = 0;
			$this->acttime = time();
			$this->blocked_from = 0;
			$this->blocked_to = 0;
			$this->hmode_from = 0;
			$this->hmode_to = 0;
			$this->deleted = 0;
			$this->allianceId = 0;

			$this->allianceName = "";
			$this->allianceTag = "";
			$this->allianceRankName = "";

			$this->rank = 0;
			$this->rankHighest = 0;

			$this->specialistId = 0;
			$this->specialistTime = 0;
			
			$this->lastInvasion = 0;

			$this->raceId = 0;

			$this->isValid=false;
		}
	}

	/**
	 * The destructor saves pedning changes
	 */	
	function __destruct()
	{
		$cnt = count($this->changedFields);
		if ($cnt > 0)
		{
			$sql = "UPDATE
				".self::tableName."
			SET ";
			foreach ($this->changedFields as $k=>$v)
			{
				if ($k=="race")
				$sql.= " user_race_id=".$this->raceId.",";
				elseif ($k=="alliance")
				$sql.= " user_alliance_id=".$this->allianceId.",";
				elseif ($k=="allianceRankId")
				$sql.= " user_alliance_rank_id=".$this->allianceRankId.",";
				elseif ($k=="specialistId")
				$sql.= " user_specialist_id=".$this->specialistId.",";
				elseif ($k=="specialistTime")
				$sql.= " user_specialist_time=".$this->specialistTime.",";
				elseif ($k=="visits")
				$sql.= " user_visits=".$this->visits.",";
				elseif ($k=="email")
				$sql.= " user_email='".$this->email."',";
				elseif ($k=="profileText")
				$sql.= " user_profile_text='".$this->profileText."',";
				elseif ($k=="signature")
				$sql.= " user_signature='".$this->signature."',";
				elseif ($k=="profileBoardUrl")
				$sql.= " user_profile_board_url='".$this->profileBoardUrl."',";
				elseif ($k == "profileImage")
				{
					if ($this->profileImage == "")
						$sql.= " user_profile_img='',user_profile_img_check=0,";
					else
						$sql.= " user_profile_img='".$this->profileImage."',user_profile_img_check=1,";
				}
				elseif ($k == "avatar")
				{
				$sql.= " user_avatar='".$this->avatar."',";
				}
				elseif ($k == "hmode_from") {
					$sql.= " user_hmode_from=".$this->hmode_from.",";
				}
				elseif ($k == "hmode_to") {
					$sql.= " user_hmode_to=".$this->hmode_to.",";
				}

				else
					echo " $k has no valid UPDATE query!<br/>";
			}
			$sql.=" user_id=user_id WHERE
					user_id=".$this->id.";";
			dbquery($sql);
		}
		unset($this->changedFields);

	}

	/**
	 * Setter
	 */
	public function __set($key, $val)
	{
		try
		{
			if (!property_exists($this,$key))
				throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);

			if ($key == "race")
			{
				$this->$key = $val;
				$this->raceId = ($this->race == null) ? 0 : $this->race->id;
				$this->changedFields[$key] = true;
				return true;
			}
			if ($key == "alliance")
			{
				$tmpAlly = $this->$key;
				$this->$key = $val;
				if ($this->alliance == null)
				{
					$this->allianceId = 0;
					if ($tmpAlly!=null)
						$this->addToUserLog("alliance","{nick} ist nun kein Mitglied mehr der Allianz [b]".$tmpAlly."[/b].");
				}
				else
				{
					if ($tmpAlly!=null)
						$this->addToUserLog("alliance","{nick} ist nun kein Mitglied mehr der Allianz ".$tmpAlly.".");
					$this->addToUserLog("alliance","{nick} ist nun Mitglied der Allianz ".$this->alliance.".");
					$this->allianceId = $this->alliance->id;
				}
				unset($tmpAlly);


				$this->allianceRankId = 0;
				$this->changedFields[$key] = true;
				$this->changedFields["allianceRankId"] = 0;
				return true;
			}
			if ($key == "race")
			{
				$this->$key = $val;
				$this->raceId = $this->race->id;
				$this->changedFields[$key] = true;
				return true;
			}
			elseif ($key == "visits")
			{
				$this->$key = intval($val);
				$this->changedFields[$key] = true;
				return true;
			}
			elseif ($key == "profileImage")
			{
				if (is_file(PROFILE_IMG_DIR."/".$this->profileImage))
	  {
		unlink(PROFILE_IMG_DIR."/".$this->profileImage);
				}
				$this->$key = $val;
				$this->changedFields[$key] = true;
				return true;
			}
			elseif ($key == "avatar")
			{
				if (is_file(BOARD_AVATAR_DIR."/".$this->avatar))
	  {
		unlink(BOARD_AVATAR_DIR."/".$this->avatar);
				}
				$this->$key = $val;
				$this->changedFields[$key] = true;
				return true;
			}
			elseif ($key == "rating")
			{
				throw new EException("Property $key der Klasse  ".__CLASS__." ist nicht änderbar!");
				return false;
			}
			elseif ($key == "raceId")
			{
				throw new EException("Property $key der Klasse  ".__CLASS__." ist nicht änderbar!");
				return false;
			}
			elseif ($key == "allianceId")
			{
				throw new EException("Property $key der Klasse  ".__CLASS__." ist nicht änderbar!");
				return false;
			}
			elseif ($key == "email")
			{
				if ($val!=$this->$key)
				{
					if (checkEmail($val))
					{
						$mail = new Mail("Änderung deiner E-Mail-Adresse","Die E-Mail-Adresse deines Accounts ".$this->nick." wurde von ".$this->email." auf ".$val ." geändert!");
						$mail->send($this->email);
						if ($this->emailFix!=$this->email)
							$mail->send($this->emailFix);
						$this->$key = $val;
						$this->changedFields[$key] = true;
					}
					else
					{
						err_msg("Ungültige Mail-Adresse!");
					}
				}
			}
			else
			{
				$this->$key = $val;
				$this->changedFields[$key] = true;
				return true;
			}

		}
		catch (EException $e)
		{
			echo $e;
		}
	}

	/**
	 * Getter
	 */
	public function __get($key)
	{
		try
		{
			if (!property_exists($this,$key))
				throw new EException("Property $key existiert nicht in ".__CLASS__);

			if ($key == "race" && $this->race==null && $this->raceId > 0)
			{
				$this->race = new Race($this->raceId);
			}
			if ($key == "alliance" && $this->alliance==null && $this->allianceId > 0)
			{
				$this->alliance = new Alliance($this->allianceId);
			}
			if ($key == "rating" && $this->rating==null)
			{
				$this->rating = new UserRating($this->id);
			}
			if ($key == "properties" && $this->properties==null)
			{
				$this->properties = new UserProperties($this->id);
			}
			if ($key == "specialist" && $this->specialist==null)
			{
				$this->specialist = new Specialist($this->specialistId,$this->specialistTime);
			}
			if ($key=="holiday" && $this->holiday==null)
			{
				$this->holiday = ($this->hmode_from!=0 && $this->hmode_to!=0) ? true : false;
			}
			if ($key=="locked" && $this->locked==null)
			{
				$this->locked = ($this->blocked_from< time() && $this->blocked_to > time()) ? true : false;
			}
			if ($key=="acttime" && $this->acttime==null)
			{
				$this->acttime = $this->loadLastAction();
			}
							if ($key == 'buddylist' && $this->buddylist == null)
							{
								$this->buddylist = new Buddylist($this->id);
							}
			return $this->$key;
		}
		catch (EException $e)
		{
			echo $e;
			return null;
		}
	}

	/**
	 * toString Function
	 */
	function __toString()
	{
		return $this->nick;
	}

	final public function isSetup() {
		return $this->setup; 
	}

	final public function allianceId()
	{
		return $this->allianceId;
	}

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

	public function setAllianceId($id)
	{
		$this->allianceId = $id;
		dbquery("
		UPDATE
			".self::tableName."
		SET
			user_alliance_rank_id=0,
			user_alliance_id=".$id."
		WHERE user_id='".$this->id."';");
	}
	
	public function isInactiv()
	{
		if (!$this->admin)
		{
			if (!$this->holiday)
			{
				if ($this->lastOnline < time() - USER_INACTIVE_SHOW * 86400)
				{
					return true;
				}
			}
		}
		return false;
	}

	public function isInactivLong()
	{
		if (!$this->admin)
		{
			if (!$this->holiday)
			{
				if ($this->lastOnline < time() - USER_INACTIVE_LONG * 86400)
				{
					return true;
				}
			}
		}
		return false;
	}
	
	//
	// Methods
	//

	function loadLastAction()
	{
		$res = dbquery("
			SELECT
				time_action
			FROM
				user_sessions
			WHERE
				user_id='".$this->id."'
			LIMIT 1;");
		if (mysql_num_rows($res))
		{
			$arr = mysql_fetch_row($res);
			return $arr[0];
		}
		else
		{
			$res = dbquery("
				SELECT
					time_action
				FROM
					user_sessionlog
				WHERE
					user_id='".$this->id."'
				ORDER BY
					time_action DESC
				LIMIT 1;");
			if (mysql_num_rows($res))
			{
				$arr = mysql_fetch_row($res);
				return $arr[0];
			}
			else
				return 1;
		}
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

		dbQuerySave("
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
			'".mysql_real_escape_string($message)."',
			'".gethostbyname($_SERVER['REMOTE_ADDR'])."',
			".intval($public)."
		);");
		return true;
	}

	/**
	* Sends a system message to the user
	*/
	function sendMessage($msg_type,$subject,$text)
	{
		dbquery("
			INSERT INTO
				messages
			(
				message_user_from,
				message_user_to,
				message_timestamp,
				message_cat_id
			)
			VALUES
			(
				'0',
				'".$this->id."',
				'".time()."',
				'".$msg_type."'
			);
		");
		dbquery("
			INSERT INTO
				message_data
			(
				id,
				subject,
				text
			)
			VALUES
			(
				".mysql_insert_id().",
				'".addslashes($subject)."',
				'".addslashes($text)."'
			);
		");
	}

	/**
	* Benutzer löschen
	*/
	function delete($self=false,$from="")
	{
		$utx = new UserToXml($this->id);
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
					user_id='".$this->id."'
				LIMIT 1;
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
						".self::tableName." AS u
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
					$this->__get('alliance')->delete($this);
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
			dbquery("DELETE FROM missilelist WHERE missilelist_user_id='".$this->id."';");	// Raketen löschen

			//Buddyliste löschen
			dbquery("DELETE FROM buddylist WHERE bl_user_id='".$this->id."' OR bl_buddy_id='".$this->id."';");

			//Markt Angebote löschen
			dbquery("DELETE FROM market_ressource WHERE user_id='".$this->id."';"); 	// Rohstoff Angebot
			dbquery("DELETE FROM market_ship WHERE user_id='".$this->id."';"); 				// Schiff Angebot
			dbquery("DELETE FROM market_auction WHERE user_id='".$this->id."';"); // Auktionen

			//Notitzen löschen
			$np = new Notepad($this->id);
			$numNotes = $np->deleteAll();
			unset($np);

			//Gespeicherte Koordinaten löschen
			dbquery("DELETE FROM bookmarks WHERE user_id='".$this->id."';");
			dbquery("DELETE FROM fleet_bookmarks WHERE user_id='".$this->id."';");

			//'user' Info löschen
			//dbquery("DELETE FROM user_log WHERE log_user_id='".$this->id."';"); 			//Log löschen
			dbquery("DELETE FROM user_multi WHERE user_id='".$this->id."' OR multi_id='".$this->id."';"); //Multiliste löschen
			dbquery("DELETE FROM user_points WHERE point_user_id='".$this->id."';"); 					//Punkte löschen
			dbquery("DELETE FROM user_warnings WHERE warning_user_id='".$this->id."';"); 				//Nickänderungsanträge löschen
			dbquery("DELETE FROM user_sitting WHERE user_id='".$this->id."';"); 			//Sitting löschen
			dbquery("DELETE FROM user_properties WHERE id = '".$this->id."';");							//Properties löschen
			dbquery("DELETE FROM user_surveillance WHERE user_id='".$this->id."';");					//Beobachter löschen
			dbquery("DELETE FROM user_comments WHERE comment_user_id='".$this->id."';");						//Kommentare löschen
			dbquery("DELETE FROM user_ratings WHERE id='".$this->id."';");							//Ratings löschen
			// Todo: clean tickets

			//
			//Benutzer löschen
			//
			dbquery("DELETE FROM ".self::tableName." WHERE user_id='".$this->id."';");

			//Log schreiben
			if($self)
				add_log("3","Der Benutzer ".$this->nick." hat sich selbst gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());
			elseif(!$self && $from!="")
				add_log("3","Der Benutzer ".$this->nick." wurde von ".$from." gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());
			else
				add_log("3","Der Benutzer ".$this->nick." wurde gelöscht!\nDie Daten des Benutzers wurden nach ".$xmlfile." exportiert.",time());

			$text ="Hallo ".$this->nick."

Dein Accouont bei Escape to Andromeda (".Config::getInstance()->roundname->v.") wurde auf Grund von Inaktivität
oder auf eigenem Wunsch hin gelöscht.

Mit freundlichen Grüssen,
die Spielleitung";
			$mail = new Mail("Accountlöschung",$text);
			$mail->send($this->email);

			return true;

		}
		else
		{
			error_msg("Konnte UserXML für ".$this->id." nicht exportieren, User nicht gelöscht!");
		}
	}

	/**
	* Registers a new user
	*/
	static public function register($data, &$errorCode, $welcomeMail=1)
	{
		$time = time();
		$cfg = Config::getInstance();

		if ($data['name']=="" || $data['nick']=="" || $data['email']=="")
		{
			$errorCode = "Nicht alle Felder sind ausgef&uuml;llt!";
			return false;
		}

		$nick=trim($data['nick']);

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
			".self::tableName."
		WHERE
			user_nick='".mysql_real_escape_string($nick)."'
			OR user_email_fix='".mysql_real_escape_string($data['email'])."'
		LIMIT 1;");
		if (mysql_num_rows($res)>0)
		{
			$errorCode = "Der Benutzer mit diesem Nicknamen oder dieser E-Mail-Adresse existiert bereits!";
			return false;
		}

		$pw = (isset($data['password']) && $data['password']!="") ? $data['password'] : mt_rand(100000000,9999999999);
		if (dbquery("
			INSERT INTO
			".self::tableName." (
			user_name,
			user_nick,
			user_password,
			user_email,
			user_email_fix,
			user_race_id,
			user_ghost,
			user_registered,
			user_sitting_days
			)
			VALUES
			('".mysql_real_escape_string($data['name'])."',
			'".mysql_real_escape_string($nick)."',
			'".saltPasswort($pw)."',
			'".mysql_real_escape_string($data['email'])."',
			'".mysql_real_escape_string($data['email'])."',
			'".(isset($data['race']) ? intval($data['race']) : 0)."',
			'".(isset($data['ghost']) ? intval($data['ghost']) : 0)."',
			'".$time."',
			'".$cfg->get("user_sitting_days")."');"))
		{
			$errorCode = mysql_insert_id();
			$rating = new UserRating($errorCode);
			$properties = new UserProperties($errorCode);

			if (!isset($data['password']))
				add_log(3,"Der Benutzer ".$nick." (".$data['name'].", ".$data['email'].") hat sich registriert!");
			else
				add_log(3,"Der Benutzer ".$nick." (".$data['name'].", ".$data['email'].") wurde registriert!");

			if ($welcomeMail == 1)
			{
				$email_text = "Hallo ".$nick."\n\nDu hast dich erfolgreich beim Sci-Fi Browsergame Escape to Andromeda registriert.\nHier nochmals deine Daten:\n\n";
				$email_text.= "Universum: ".Config::getInstance()->roundname->v."\n";
				$email_text.= "Name: ".$data['name']."\n";
				$email_text.= "E-Mail: ".$data['email']."\n\n";
				$email_text.= "Nick: ".$nick."\n";
				$email_text.= "Passwort: ".$pw." (bitte nach dem ersten Login ändern)\n\n";
				$email_text.= "WICHTIG: Gib das Passwort an niemanden weiter. Gib dein Passwort auch auf keiner Seite ausser unserer Loginseite ein. Ein Game-Admin oder Entwickler wird dich auch nie nach dem Passwort fragen!\n";
				$email_text.= "Desweiteren solltest du dich mit den Regeln (".RULES_URL.") bekannt machen, da ein Regelverstoss eine (zeitweilige) Sperrung deines Accounts zur Folge haben kann!\n\n";
				$email_text.= "Viel Spass beim Spielen!\nDas EtoA-Team";

				$mail = new Mail("Account-Registrierung",$email_text);
				$mail->send($data['email']);
			}
			return true;
		}

		$errorCode = "Ein unbekannter Fehler trat auf!";
		return false;
	}

	/**
	* Returns the total number of users
	*/
	static public function count()
	{
		$ures = dbquery("SELECT COUNT(user_id) FROM ".self::tableName.";");
		$uarr = mysql_fetch_row($ures);
		return $uarr[0];
	}

	static function findIdByNick($nick)
	{
		$res = dbquery("
		SELECT
			user_id
		FROM
			".self::tableName."
		WHERE
			user_nick='".mysql_real_escape_string($nick)."'
		LIMIT 1;
		");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_row($res);
			return $arr[0];
		}
		return 0;
	}

	public function detailLink()
	{
		return "<a href=\"?page=userinfo&amp;id=".$this->id."\">".$this->__toString()."</a>";
	}
}
?>
