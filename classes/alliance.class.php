<?php

class Alliance 
{
	protected $id;
	protected $name;
	protected $tag;
	protected $motherId;
	protected $points;
	protected $memberCount;

	protected $wings = null;

	protected $valid;
	
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
  		$this->points = $arr['alliance_points'];
  		$this->memberCount = $arr['member_count'];
  		
  		$this->valid = true;
  	}
  }
    
	public function __toString()
	{
		return "[".$this->tag."] ".$this->name;
	}
	
	public function __set($key, $val)
	{
		try
		{
			if (!property_exists($this,$key))
				throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
			
			throw new EException("Property $key der Klasse  ".__CLASS__." ist nicht änderbar!");
				return false;

			
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
			if ($key == "avgPoints")
				return floor($this->points / $this->memberCount);
				
			if (!property_exists($this,$key))
				throw new EException("Property $key existiert nicht in ".__CLASS__);

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
	
	
	public function getWings()
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
	
	public function addWing($allianceId)
	{
		$this->getWings();
		if ($allianceId != $this->id && !isset($this->wings[$allianceId]))
		{
			$res = dbquery("
			UPDATE
				alliances
			SET
				alliance_mother=".$this->id."
			WHERE
				alliance_id=".$allianceId."
			");
			if (mysql_affected_rows()>0)
			{
				$this->wings[$allianceId] = new Alliance($allianceId);
				$this->addHistory($this->wings[$allianceId]." wurde als neuer Wing hinzugefügt.");
				$this->wings[$allianceId]->addHistory("Wir sing nun ein Wing von ".$this);
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
			alliance_id=".$wingId."
		");
		if (mysql_affected_rows()>0)
		{
			if ($this->wings != null)
			{
				$this->addHistory($this->wings[$wingId]." ist nun kein Wing mehr von uns");
				$this->wings[$wingId]->addHistory("Wir sind nun kein Wing mehr von ".$this);
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