<?php

class Alliance 
{
	protected $id;
	protected $name;
	protected $tag;
	protected $points;
	protected $memberCount;
	protected $visits;
	protected $visitsExt;
	protected $text;
	protected $image;
	protected $url;
	protected $acceptApplications;
	protected $acceptPact;
	protected $foundationDate;
	protected $publicMemberList;

	protected $wings = null;
	protected $wingRequests = null;
	protected $members = null;
	protected $ranks = null;

	protected $founderId;
	protected $founder = null;

	protected $motherId;
	protected $mother = null;
	protected $motherRequestId;
	protected $motherRequest = null;
	
	protected $valid;
	protected $changedFields;
	
	/**
	* Constructor
	*/
  function Alliance($id) 
  {
  	$this->id = $id;
  	$this->valid = false;
  	$res = dbquery("
  	SELECT
  		alliances.*,
  		COUNT(users.user_id) as member_count
  	FROM
  		alliances
  	LEFT JOIN
  		users
  		ON user_alliance_id=alliance_id
  	WHERE
  		alliance_id=".$this->id."
  	GROUP BY
  		alliance_id
  	LIMIT 1;
  	");
  	if (mysql_num_rows($res)>0)
  	{
  		$arr = mysql_fetch_assoc($res);
  		$this->name = $arr['alliance_name'];
  		$this->tag = $arr['alliance_tag'];
  		$this->motherId = $arr['alliance_mother'];
  		$this->motherRequestId = $arr['alliance_mother_request'];
  		$this->points = $arr['alliance_points'];
  		$this->memberCount = $arr['member_count'];
			$this->founderId = $arr['alliance_founder_id'];  		
			$this->visits = $arr['alliance_visits'];
			$this->visitsExt = $arr['alliance_visits_ext'];
			$this->text = $arr['alliance_text'];
			$this->image = $arr['alliance_img'];
			$this->url = $arr['alliance_url'];
			$this->acceptApplications = (bool)$arr['alliance_accept_applications'];
			$this->acceptPact = (bool)$arr['alliance_accept_bnd'];
			$this->publicMemberList = (bool)$arr['alliance_public_memberlist'];
			$this->foundationDate = $arr['alliance_foundation_date'];
			  	
			
			$this->changedFields = array();
  		$this->valid = true;
  	}
  }
    
  /**
  * Returns a propperly formated alliance name
  */
	public function __toString()
	{
		return "[".$this->tag."] ".$this->name;
	}
	
	/**
	* Destruktor
	*/
	function __destruct()
	{
		$cnt = count($this->changedFields);
		if ($cnt > 0)
		{
			$sql = "UPDATE 
				alliances
			SET ";
			foreach ($this->changedFields as $k=>$v)
			{
				if ($k == "visits")	
			    $sql.= " alliance_visits=".$this->$k.",";
				elseif ($k == "visitsExt")	
			    $sql.= " alliance_visits_ext=".$this->$k.",";
				elseif ($k == "founderId")
			    $sql.= " alliance_founder_id=".$this->$k.",";
			   /*
				elseif ($k == "profileImage")
				{
					if ($this->profileImage == "")
				    $sql.= " user_profile_img='',user_profile_img_check=0,";
          else
			    	$sql.= " user_profile_img='".$this->profileImage."',user_profile_img_check=1,";
				}*/
				else
					echo " $k has no valid UPDATE query!<br/>";
			}
			$sql.=" alliance_id=alliance_id WHERE
			    	alliance_id=".$this->id.";";
			dbquery($sql);
		}
		unset($this->changedFields);
		
	}	
	
	/**
	* Chances alliance properties
	*/
	public function __set($key, $val)
	{
		try
		{
			if (!property_exists($this,$key))
				throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
			
			if ($key=="visits")
			{
				$this->$key = intval($val);
				$this->changedFields[$key] = true;
				return true;				
			}
			if ($key=="visitsExt")
			{
				$this->$key = intval($val);
				$this->changedFields[$key] = true;
				return true;				
			}		
			if ($key=="founderId")
			{
				if ($this->members == null)				
					$this->getMembers();
				if (isset($this->members[$val]))
				{
					$this->$key = $val;
					$this->founder = & $this->members[$val];
					$this->addHistory("Der Spieler [b]".$this->founder."[/b] wird zum Gründer befördert.");
					$this->founder->sendMessage(MSG_ALLYMAIL_CAT,"Gründer","Du hast nun die Gründerrechte deiner Allianz!");
					$this->founder->addHistory("{nick} ist nun Gründer der Allianz ".$this);
					$this->changedFields[$key] = true;
					return true;				
				}
				return false;
			}				
		
			throw new EException("Property $key der Klasse  ".__CLASS__." ist nicht änderbar!");
				return false;
		}
		catch (EException $e)
		{
			echo $e;
		}
	}

	/**
	* Gets alliance properties
	*/	
	public function __get($key)
	{
		try
		{
			// Return special non-defined properties
			if ($key == "avgPoints")
				return floor($this->points / $this->memberCount);
			if ($key == "imageUrl")
				return ALLIANCE_IMG_DIR."/".$this->image;

			// Check if property exists
			if (!property_exists($this,$key))
				throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);

			// Do actions for some special properties
			if ($key == "members" && $this->members == null)
				$this->getMembers();
			if ($key == "wings" && $this->wings == null)
				$this->getWings();
			if ($key == "wingRequests" && $this->wingRequests == null)
				$this->getWingRequests();
			if ($key == "mother" && $this->mother == null)
				$this->mother = new Alliance($this->motherId);
			if ($key == "motherRequest" && $this->motherRequest == null)
				$this->motherRequest = new Alliance($this->motherRequestId);
			if ($key == "founder" && $this->founder == null) 
			{
				if ($this->members == null)
					$this->getMembers();
				if (isset($this->members[$this->founderId]))
					$this->founder = & $this->members[$this->founderId];
			}


			// Protected properties
			if ($key == "changedFields")
				throw new EException("Property $key der Klasse ".__CLASS__." ist geschützt!");
				

			return $this->$key;
		}
		catch (EException $e)
		{
			echo $e;
			return null;
		}
	}	
	
	/**
	* Add text to alliance history
	*/
	function addHistory($text)
	{
		dbquery("
			INSERT INTO
			alliance_history
			(
				history_alliance_id,
				history_text,
				history_timestamp
			)
			VALUES
			(
				'".$this->id."',
				'".addslashes($text)."',
				'".time()."'
			);");
	}
	
	/**
	* Returns alliance members as an array of user objecs
	*/
	public function & getMembers()
	{
		if ($this->members == null)
		{
			$this->members = array();
	  	$res = dbquery("
	  	SELECT
	  		user_id
	  	FROM
	  		users
	  	WHERE
	  		user_alliance_id=".$this->id."
	  	");			
	  	if (mysql_num_rows($res)>0)
	  	{
	  		while ($arr = mysql_fetch_row($res))
	  		{
		  		$this->members[$arr[0]] = new User($arr[0]);
	  		}
	  	}
		}
		return $this->members;
	}
	
	/**
	* Adds a new user to the alliance
	*/
	public function addMember($userId)
	{
		$this->getMembers();
		if (!isset($this->members[$userId]))
		{
			$tmpUser = new User($userId);
			if ($tmpUser->isValid)
			{
				if ($tmpUser->alliance = $this)
				{
					$this->members[$userId] = $tmpUser;
					$this->members[$userId]->sendMessage(MSG_ALLYMAIL_CAT,"Allianzaufnahme","Du wurdest in die Allianz [b]".$this."[/b] aufgenommen!");
					$this->addHistory("[b]".$tmpUser."[/b] wurde als neues Mitglied aufgenommen");
					return true;
				}
			}
			unset($tmpUser);
		}
		return false;
	}	
	
	/**
	* Removes an user from the alliance
	*/
	public function kickMember($userId,$kick=1)
	{
		$this->getMembers();
		if ($this->members[$userId]->isValid)
		{
			$this->members[$userId]->alliance = null;
			if ($this->members[$userId]->allianceId == 0)
			{
				if ($kick==1)
					$this->members[$userId]->sendMessage(MSG_ALLYMAIL_CAT,"Allianzausschluss","Du wurdest aus der Allianz [b]".$this."[/b] ausgeschlossen!");
				else
					$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Allianzaustritt","Der Spieler ".$this->members[$userId]." trat aus der Allianz aus!");
				
				$this->addHistory("[b]".$this->members[$userId]."[/b] ist nun kein Mitglied mehr von uns.");
				unset($this->members[$userId]);
				return true;
			}
		}
		unset($tmpUser);
		return false;
	}		
	
	/**
	* Returns all wings of this alliance as an array of alliance objects
	*/
	public function & getWings()
	{
		if ($this->wings == null)
		{
			$this->wings = array();
	  	$res = dbquery("
	  	SELECT
	  		alliance_id
	  	FROM
	  		alliances
	  	WHERE
	  		alliance_mother=".$this->id."
	  		AND alliance_id!=".$this->id."
	  	");			
	  	if (mysql_num_rows($res)>0)
	  	{
	  		while ($arr = mysql_fetch_row($res))
	  		{
		  		$this->wings[$arr[0]] = new Alliance($arr[0]);
	  		}
	  	}
		}
		return $this->wings;
	}
	
	public function & getWingRequests()
	{
		if ($this->wingRequests == null)
		{
			$this->wingRequests = array();
	  	$res = dbquery("
	  	SELECT
	  		alliance_id
	  	FROM
	  		alliances
	  	WHERE
	  		alliance_mother_request=".$this->id."
	  		AND alliance_id!=".$this->id."
	  	");			
	  	if (mysql_num_rows($res)>0)
	  	{
	  		while ($arr = mysql_fetch_row($res))
	  		{
		  		$this->wingRequests[$arr[0]] = new Alliance($arr[0]);
	  		}
	  	}
		}
		return $this->wingRequests;
	}	
	
	public function addWing($allianceId)
	{
		$this->getWings();
		if ($allianceId != $this->id && !isset($this->wings[$allianceId]))
		{
			$res = dbquery("
			UPDATE
				alliances
			SET
				alliance_mother=".$this->id.",
				alliance_mother_request=0
			WHERE
				alliance_id=".$allianceId."
			");
			if (mysql_affected_rows()>0)
			{
				$this->wings[$allianceId] = new Alliance($allianceId);
				$this->addHistory($this->wings[$allianceId]." wurde als neuer Wing hinzugefügt.");
				$this->wings[$allianceId]->addHistory("Wir sing nun ein Wing von ".$this);
				$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist nun ein Wing von [b]".$this."[/b]");
				$this->wings[$allianceId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist nun ein Wing von [b]".$this."[/b]");
				return true;
			}			
		}
		return false;
	}
	
	public function addWingRequest($allianceId)
	{
		if ($allianceId != $this->id)
		{
			$res = dbquery("
			UPDATE
				alliances
			SET
				alliance_mother_request=".$this->id."
			WHERE
				alliance_mother_request=0
				AND alliance_mother=0
				AND alliance_id=".$allianceId."
			");
			if (mysql_affected_rows()>0)
			{
				$this->wingRequests[$allianceId] = new Alliance($allianceId);
				$this->wingRequests[$allianceId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage","Die Allianz [b]".$this."[/b] möchte eure Allianz als Wing hinzufügen. [url ?page=alliance&action=wings]Anfrage beantworten[/url]");
				return true;
			}			
		}
		return false;
	}	
	
	public function removeWing($wingId)
	{
		dbquery("
		UPDATE
			alliances
		SET
			alliance_mother=0
		WHERE
			alliance_mother=".$this->id."
			AND alliance_id=".$wingId."
		");
		if (mysql_affected_rows()>0)
		{
			if ($this->wings != null)
			{
				$this->addHistory($this->wings[$wingId]." ist nun kein Wing mehr von uns");
				$this->wings[$wingId]->addHistory("Wir sind nun kein Wing mehr von ".$this);
				$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist kein Wing mehr von [b]".$this."[/b]");
				$this->wings[$wingId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing","Die Allianz [b]".$this->wings[$allianceId]."[/b] ist kein Wing mehr von [b]".$this."[/b]");
				unset($this->wings[$wingId]);
			}
			else
			{
				$tmpWing = new Alliance($wingId);
				$this->addHistory($tmpWing." ist nun kein Wing mehr.");
				$tmpWing->addHistory("Wir sind nun kein Wing mehr von ".$this);
				unset($tmpWing);
			}
			return true;
		}
		return false;
	}
	
	public function cancelWingRequest($wingId,$reverse=0)
	{
		dbquery("
		UPDATE
			alliances
		SET
			alliance_mother_request=0
		WHERE
			alliance_mother_request=".$this->id."
			AND alliance_id=".$wingId."
		");
		if (mysql_affected_rows()>0)
		{
			if ($this->wingRequests != null)
			{
				if ($reverse==1)
					$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgewiesen","Die Allianz [b]".$this->wingRequests[$wingId]."[/b] hat die Wing-Anfrage zurückgewiesen.");
				else
					$this->wingRequests[$wingId]->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgezogen","Die Allianz [b]".$this."[/b] hat die Wing-Anfrage zurückgezogen.");
				unset($this->wingRequests[$wingId]);
			}
			else
			{
				$tmpWing = new Alliance($wingId);
				if ($reverse==1)
					$this->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgewiesen","Die Allianz [b]".$tmpWing."[/b] hat die Wing-Anfrage zurückgewiesen.");
				else
					$tmpWing->__get('founder')->sendMessage(MSG_ALLYMAIL_CAT,"Wing-Anfrage zurückgezogen","Die Allianz [b]".$this."[/b] hat die Wing-Anfrage zurückgezogen.");
				unset($tmpWing);
			}
			return true;
		}
		return false;
	}	
	
	
	
	//
	// Statics
	//
	
  static function checkActionRights($action)
  {
		global $myRight,$isFounder,$page;
		if ($isFounder || $myRight[$action])
		{
			return true;
		}
		error_msg("Keine Berechtigung!");
		echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
		return false;    	
  }
  
  static public function & getList()
  {
  	$rtn = array();
  	$res = dbquery("
  	SELECT
  		alliance_id,
  		alliance_tag,
  		alliance_name
  	FROM
  		alliances
		ORDER BY
			alliance_name
  	");  	
  	if (mysql_num_rows($res)>0)
  	{
  		while ($arr = mysql_fetch_row($res))
  			$rtn[$arr[0]] = "[".$arr[1]."] ".$arr[2];
  	}
  	return $rtn;
  }
}
?>