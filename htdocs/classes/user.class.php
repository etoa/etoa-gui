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
class User implements \EtoA\User\UserInterface
{
	const tableName = "users";

	// Fields
	protected $id;	// Database record id
	protected $nick; // Unicke nickname
	protected $setup; // Cheker if account is propperly setup
	protected $isValid; // Checker if class instance belongs to valid user
	protected $maskMatrix; // Matrix for the "fog of war" effect in the space map
	protected $realName;
	protected $pw;
	protected $npc;
	protected $email;
	protected $emailFix;
    protected $d_email;   //Dual E-mail
    protected $d_realName; //Dual name
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
	protected $developer;
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
	protected $allianceLeave;
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
	protected $changedMainPlanet;

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

	protected $isVerified;
	protected $verificationKey;

	protected $dmask;

	/**
	* The constructor initializes and loads
	* all importand data about this user
	*/
    public function __construct($id)
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
			$this->pw = $arr['user_password'];
			$this->realName=$arr['user_name'];
			$this->email=$arr['user_email'];
			$this->emailFix=$arr['user_email_fix'];

            $this -> d_email = $arr['dual_email'];
            $this -> d_realName = $arr['dual_name'];

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

			$this->monitored = ($arr['user_observe']!="") ? true : false;

			$this->registered = $arr['user_registered'];
			$this->setup = $arr['user_setup']==1 ? true : false;
			$this->chatadmin=$arr['user_chatadmin']==1 ? true : false;
			$this->admin=$arr['admin']==1 ? true : false;
			$this->npc=$arr['npc']==1 ? true : false;
			$this->developer=$arr['admin']==2 ? true : false;
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
			$this->allianceLeave = $arr['user_alliance_leave'];

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
            
            $this->changedMainPlanet = $arr['user_changed_main_planet'];

			$this->raceId = $arr['user_race_id'];

			$this->allianceShippoints = $arr['user_alliace_shippoints'];

			$this->changedFields = array();

			$this->isVerified = ($arr['verification_key'] == '');
			$this->verificationKey = $arr['verification_key'];

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
            
			$this->changedMainPlanet = 0;

			$this->raceId = 0;

			$this->isValid=false;
		}
	}

    public function getId(): int
    {
        return (int)$this->id;
    }

    public function getNick(): string
    {
        return $this->nick;
    }

	/**
	 * The destructor saves pedning changes
	 */
	function __destruct()
	{
		if ($this->changedFields && count($this->changedFields) > 0)
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
        		elseif ($k=="d_realName")
				$sql.= " dual_name='".$this->d_realName."',";
                elseif ($k=="d_email")
				$sql.= " dual_email='".$this->d_email."',";
                elseif ($k=="allianceLeave")
                $sql.= " user_alliance_leave='".$this->allianceLeave."',";
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
						error_msg("Ungültige Mail-Adresse!");
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

	public function setVerified($verified)
	{
		if ($verified) {
			$this->verificationKey = '';
		} else {
			$this->verificationKey = generateRandomString(64);
		}
		dbQuerySave("
		UPDATE
			".self::tableName."
		SET
			verification_key=?
		WHERE
			user_id=?
		;", [
			$this->verificationKey,
			$this->id
		]);
		$this->isVerified = $verified;
	}

	public static function findFirstByVerificationKey($verificationKey) {
		$res = dbQuerySave("
		SELECT
			user_id
		FROM
			".self::tableName."
		WHERE
			verification_key=?
		;", [
			$verificationKey
		]);
		if ($arr = mysql_fetch_row($res)) {
			return new User($arr[0]);
		}
		return null;
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

	public function isNPC()
	{
		if($this->npc)
			return true;
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
    
	/**
	 * Returns whether this user has changed their main planet
	 * @return boolean
	 */
	public function changedMainPlanet(){
		return $this->changedMainPlanet;
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
     * Löschantrag stellen
     */
    function deleteRequest($pw) {
        if (validatePasswort($pw, $this->pw))
        {
            $t = time() + (USER_DELETE_DAYS*3600*24);
            dbquery("
				UPDATE
					users
				SET
					user_deleted=".$t."
				WHERE
					user_id=".$this->id."
				;");
            return true;
        }
        else
        {
            return false;
        }
    }

    /**
     * Löschantrag widerrufen
     */

    function revokeDelete() {
        dbquery("
			UPDATE
				users
			SET
				user_deleted=0
			WHERE
				user_id=".$this->id."
			;");
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
     * Umode aktivieren
     */

    function activateUmode($force = false)
    {

        $cres = dbquery("SELECT id FROM fleet WHERE user_id='" . $this->id . "';");
        $carr = mysql_fetch_row($cres);
        if ($carr[0] == 0 || $force) {
            $pres = dbquery("SELECT 
                                    f.id 
                                FROM 
                                    fleet as f
                                INNER JOIN
                                    planets as p
                                ON f.entity_to=p.id
                                AND p.planet_user_id='" . $this->id . "'
                                AND (f.user_id='" . $this->id . "' OR (status=0 AND action NOT IN ('collectdebris','explore','flight','createdebris')));");
            $parr = mysql_fetch_row($pres);
            if ($parr[0] == 0 || $force) {
                $sres = dbquery("SELECT 
                                        queue_id,
                                        queue_starttime 
                                    FROM 
                                        ship_queue 
                                    WHERE 
                                        queue_user_id='" . $this->id . "';");
                while ($sarr = mysql_fetch_row($sres)) {
                    if ($sarr[1] > time()) {
                        dbquery("UPDATE 
                                        ship_queue 
                                    SET 
                                        queue_build_type=1
                                    WHERE 
                                        queue_user_id='" . $this->id . "';");
                    } else {
                        dbquery("UPDATE 
                                        ship_queue 
                                    SET 
                                        queue_build_type=1
                                    WHERE 
                                        queue_user_id='" . $this->id . "';");
                    }
                }
                $sres = dbquery("SELECT 
                                        queue_id,
                                        queue_starttime 
                                    FROM 
                                        def_queue 
                                    WHERE 
                                        queue_user_id='" . $this->id . "';");
                while ($sarr = mysql_fetch_row($sres)) {
                    if ($sarr[1] > time()) {
                        dbquery("UPDATE 
                                        def_queue 
                                    SET 
                                        queue_build_type=1
                                    WHERE 
                                        queue_user_id='" . $this->id . "';");
                    } else {
                        dbquery("UPDATE 
                                        def_queue 
                                    SET 
                                        queue_build_type=1
                                    WHERE 
                                        queue_user_id='" . $this->id . "';");
                    }
                }

                dbquery("UPDATE 
                                buildlist 
                            SET 
                                buildlist_build_type = 1
                            WHERE 
                                buildlist_user_id='" . $this->id . "' 
                                AND buildlist_build_start_time>0;");
                dbquery("UPDATE 
                                techlist 
                            SET 
                                techlist_build_type=1
                            WHERE 
                                techlist_user_id='" . $this->id . "' 
                                AND techlist_build_start_time>0;");

                $hfrom = time();

                $hto = $hfrom + (MIN_UMOD_TIME*24*3600);
                dbquery("
                        UPDATE
                            planets
                        SET
                            planet_last_updated='0',
                            planet_prod_metal=0,
                            planet_prod_crystal=0,
                            planet_prod_plastic=0,
                            planet_prod_fuel=0,
                            planet_prod_food=0
                        WHERE
                            planet_user_id='" . $this->id . "';");

                dbquery("UPDATE users SET user_hmode_from=$hfrom,user_hmode_to=$hto,user_logouttime='" . time() . "' WHERE user_id='" . $this->id . "';");

                $this->hmode_from = $hfrom;
                $this->hmode_to = $hto;
                return true;
            }
            else
                return false;
        }
        else
            return false;

    }

    /**
     * Umode aufheben
     */

    function removeUmode($force = false) {

        if ($this->hmode_from > 0 && (($this->hmode_from < time() && $this->hmode_to < time()) || $force)) {
            $hmodTime = time() - $this->hmode_from;
            $bres = dbquery("
								SELECT
									buildlist_id,
									buildlist_build_end_time,
									buildlist_build_start_time,
									buildlist_build_type
								FROM
									buildlist
								WHERE
									buildlist_build_start_time>0
									AND buildlist_build_type>0
									AND buildlist_user_id=" . $this->id . ";");

            while ($barr = mysql_fetch_row($bres)) {
                $start = $barr[2] + $hmodTime;
                $end = $barr[1] + $hmodTime;
                $status = $barr[3] + 2;
                dbquery("UPDATE
								buildlist
							SET
								buildlist_build_type='" . $status . "',
								buildlist_build_start_time='" . $start . "',
								buildlist_build_end_time='" . $end . "'
							WHERE
								buildlist_id='" . $barr[0] . "';");
            }

            $tres = dbquery("
								SELECT
									techlist_id,
									techlist_build_end_time,
									techlist_build_start_time,
									techlist_build_type
								FROM
									techlist
								WHERE
									techlist_build_start_time>0
									AND techlist_build_type>0
									AND techlist_user_id=" . $this->id . ";");

            while ($tarr = mysql_fetch_row($tres)) {
                $status = $tarr[3] + 2;
                $start = $tarr[2] + $hmodTime;
                $end = $tarr[1] + $hmodTime;
                dbquery("UPDATE
								techlist
							SET
								techlist_build_type='" . $status . "',
								techlist_build_start_time='" . $start . "',
								techlist_build_end_time='" . $end . "'
							WHERE
								techlist_id=" . $tarr[0] . ";");
            }

            $sres = dbquery("SELECT 
									queue_id,
									queue_endtime,
									queue_starttime
								 FROM 
								 	ship_queue 
								WHERE 
									queue_user_id='" . $this->id . "'
								ORDER BY 
									queue_starttime ASC;");
            $time = time();
            while ($sarr = mysql_fetch_row($sres)) {
                $start = $sarr[2] + $hmodTime;
                $end = $sarr[1] + $hmodTime;
                dbquery("UPDATE 
								ship_queue
							SET
								queue_build_type=0,
								queue_starttime='" . $start . "',
								queue_endtime='" . $end . "'
							WHERE
								queue_id=" . $sarr[0] . ";");
            }

            $dres = dbquery("SELECT 
									queue_id,
									queue_endtime,
									queue_starttime
								 FROM 
								 	def_queue 
								WHERE 
									queue_user_id='" . $this->id . "'
								ORDER BY 
									queue_starttime ASC;");
            $time = time();
            while ($darr = mysql_fetch_row($dres)) {
                $start = $darr[2] + $hmodTime;
                $end = $darr[1] + $hmodTime;
                dbquery("UPDATE 
								def_queue
							SET
								queue_build_type=0,
							queue_starttime='" . $start . "',
								queue_endtime='" . $end . "'
							WHERE
								queue_id=" . $darr[0] . ";");
            }

            // Prolong specialist contract
            dbquery("
                UPDATE
                  users
                SET
                  user_specialist_time=user_specialist_time+" . $hmodTime . "
                WHERE
                  user_specialist_id > 0
                  AND user_id=" . $this->id . "
                ;");

            dbquery("UPDATE users SET user_hmode_from=0,user_hmode_to=0,user_logouttime='" . time() . "' WHERE user_id='" . $this->id . "';");
            dbquery("UPDATE planets SET planet_last_updated=" . time() . " WHERE planet_user_id='" . $this->id . "';");
            return true;
        }
        else {
            return false;
        }
    }

	/**
	* Registers a new user
	*/
	static public function register($name, $email, $nick, $password, $race=null, $ghost=false)
	{
		$cfg = Config::getInstance();

		// Validate required data is not empty
		if (empty($name) || empty($email) || empty($nick) || empty($password))
		{
			throw new Exception("Nicht alle Felder sind ausgef&uuml;llt!");
		}

		// Validate email
		if (!checkEmail($email))
		{
			throw new Exception("Diese E-Mail-Adresse scheint ung&uuml;ltig zu sein. Pr&uuml;fe nach, ob dein E-Mail-Server online ist und die Adresse im korrekten Format vorliegt!");
		}

		// Validate name
		if (!checkValidName($name))
		{
			throw new Exception("Du hast ein unerlaubtes Zeichen im vollst&auml;ndigen Namen!");
		}

		// Validate nickname
		$nick = trim($nick);
		if (!checkValidNick($nick))
		{
			throw new Exception("Du hast ein unerlaubtes Zeichen im Benutzernamen!");
		}
		if ($nick == '')
		{
			throw new Exception("Dein Nickname darf nicht nur aus Leerzeichen bestehen!");
		}
		$nick_length = strlen(utf8_decode($nick));
		if ($nick_length < NICK_MINLENGHT || $nick_length > NICK_MAXLENGHT)
		{
			throw new Exception("Dein Nickname muss mindestens ".NICK_MINLENGHT." Zeichen und maximum ".NICK_MAXLENGHT." Zeichen haben!");
		}

		// Validate password
		if (strlen($password) < PASSWORD_MINLENGHT)
		{
			throw new Exception("Das Passwort ist noch zu kurz (mind. ".PASSWORD_MINLENGHT." Zeichen sind nötig)!");
		}

		// Check existing user
		$res = dbQuerySave("
		SELECT
			user_id
		FROM
			".self::tableName."
		WHERE
			user_nick=?
			OR user_email_fix=?
		LIMIT 1;", [
			$nick,
			$email
		]);
		if (mysql_num_rows($res)>0)
		{
			throw new Exception("Der Benutzer mit diesem Nicknamen oder dieser E-Mail-Adresse existiert bereits!");
		}

		// Add new record
		if (dbQuerySave("
			INSERT INTO
				".self::tableName." 
			(
				user_name,
				user_nick,
				user_password,
				user_logouttime,
				user_email,
				user_email_fix,
				user_race_id,
				user_ghost,
				user_registered,
				user_sitting_days
			)
			VALUES
			(
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				?,
				UNIX_TIMESTAMP(),
				?
			);", [
				$name,
				$nick,
				saltPasswort($password),
				time(),
				$email,
				$email,
				(isset($race) ? intval($race) : 0),
				($ghost ? 1 : 0),
				$cfg->get("user_sitting_days")
			]))
		{
			$uid = mysql_insert_id();
			$rating = new UserRating($uid);
			$properties = new UserProperties($uid);
			return new User($uid);
		}
		throw new Exception("Ein unbekannter Fehler trat auf! ".mysql_error());
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

    public function isUserNoobProtected(User $u)
    {
        // check whether user points are outside limits
        // or this user or opponent is below minimum attack threshold
        return  ($this->points*USER_ATTACK_PERCENTAGE > $u->points || $this->points/USER_ATTACK_PERCENTAGE < $u->points)
                || ($this->points <= USER_ATTACK_MIN_POINTS)
                || ($u->points <= USER_ATTACK_MIN_POINTS);
    }

    public function canAttackUser(User $u)
    {
    	// somehow $this->alliance doesn't use the getter
        // neither does $u->locked, wtf

        // att allowed if war is active
        // or att allowed if target user is not noob protected
        // or att allowed if target user is inactive
        // or att allowed if target user is locked
        if ($this->allianceId() > 0 && $u->allianceId() > 0)
       	{
       		return $this->__get('alliance')->checkWar($u->allianceId())
                || !$this->isUserNoobProtected($u)
                || $u->isInactiv()
                || $u->__get('locked')
                || $u->isNPC();
        }
        else
        {
        	return !$this->isUserNoobProtected($u)
                || $u->isInactiv()
                || $u->__get('locked')
                || $u->isNPC();
        }
    }

    public function canAttackPlanet(Planet $p)
    {
        // Planet is attackable if user is attackable
        // or if last owner == this owner (invade time threshold)
        return $this->canAttackUser($p->owner()) || $this->id == $p->lastUserCheck();
    }
    
    /**
     * Setzt, ob dieser Spieler seinen Hauptplaneten bereits gewechselt hat.
     * @param boolean $changed
     */
    public function setChangedMainPlanet($changed){
        $changed_value = ($changed ? 1 : 0);
        $this->changedMainPlanet = $changed_value;
        dbquery("
        UPDATE
            users 
        SET
            user_changed_main_planet=$changed_value 
        WHERE
            user_id=".$this->id."
        ");
    }

	private function loadDiscoveryMask()
	{
		$cfg = Config::getInstance();
		$sx_num=$cfg->param1('num_of_sectors');
		$cx_num=$cfg->param1('num_of_cells');
		$sy_num=$cfg->param2('num_of_sectors');
		$cy_num=$cfg->param2('num_of_cells');

		$res = dbquery("
		SELECT
			discoverymask
		FROM				
			users
		WHERE
			user_id=".$this->id."
		");
		$this->dmask = '';
		$arr = mysql_fetch_row($res);
		if (strlen($arr[0])<3)
		{
			for ($x=1;$x<=$sx_num*$cx_num;$x++)
			{
				for ($y=1;$y<=$sy_num*$cy_num;$y++)
				{
					$this->dmask.= '0';
				}
			}
			dbquery("
					UPDATE
						users
					SET
						discoverymask='".$this->dmask."'
					WHERE
						user_id='".$this->id."'
					LIMIT 1;");
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
		return (($pos < strlen($this->dmask)) ? $this->dmask{$pos} > 0 : false);
	}

	function getDiscoveredPercent()
	{
		if (!isset($this->dmask))
		{
			$this->loadDiscoveryMask();
		}
		$strl = strlen($this->dmask);
		if ($strl > 0) {
			return substr_count($this->dmask, "1") / $strl * 100;
		}
		return 0;
	}

	function setDiscovered($absX,$absY,$radius=1)
	{
		if (!isset($this->dmask))
		{
			$this->loadDiscoveryMask();
		}
		$cfg = Config::getInstance();
		$sx_num=$cfg->param1('num_of_sectors');
		$cx_num=$cfg->param1('num_of_cells');
		$sy_num=$cfg->param2('num_of_sectors');
		$cy_num=$cfg->param2('num_of_cells');

		for ($x=$absX-$radius; $x<=$absX+$radius; $x++)
		{
			for ($y=$absY-$radius; $y<=$absY+$radius; $y++)
			{
				if ($x > 0 && $y > 0 && $x <= $sx_num * $cx_num && $y <= $sy_num * $cy_num)
				{
					$pos = $x + ($cy_num*$sy_num)*($y-1)-1;
					if ($pos>= 0 && $pos <= $sx_num*$sy_num*$cx_num*$cy_num)
					{
						$this->dmask{$pos} = '1';
					}
				}
			}
		}
		$this->saveDiscoveryMask();
	}

	function setDiscoveredAll($discovered)
	{
		if (!isset($this->dmask))
		{
			$this->loadDiscoveryMask();
		}
		$cfg = Config::getInstance();
		$sx_num=$cfg->param1('num_of_sectors');
		$cx_num=$cfg->param1('num_of_cells');
		$sy_num=$cfg->param2('num_of_sectors');
		$cy_num=$cfg->param2('num_of_cells');

		for ($x=1; $x <= $sx_num * $cx_num; $x++)
		{
			for ($y=1; $y <= $sy_num * $cy_num; $y++)
			{
				$pos = $x + ($cy_num*$sy_num)*($y-1)-1;
				$this->dmask{$pos} = $discovered ? '1' : '0';
			}
		}
		$this->saveDiscoveryMask();
	}

	private function saveDiscoveryMask()
	{
		dbquery("
		UPDATE
			users
		SET
			discoverymask='".$this->dmask."'
		WHERE
			user_id=".$this->id."
		");
	}

}
