<?PHP
	
	class Defense
	{
		function Defense($sid)
		{
			$this->isValid = false;
			
			if (is_array($sid))
			{
				$arr = $sid;
			}
			else
			{
				$res = dbquery("
				SELECT
					*
				FROM	
					defense
				WHERE
					def_id=".$sid."			
				");			
				if (!$arr = mysql_fetch_assoc($res))
				{
					throw new EException("Ungültige Verteidigungs-ID: $sid");
					return false;
				}
			}			
			
			$this->id = $arr['def_id'];
			$this->name = $arr['def_name'];
			$this->shortComment = $arr['def_shortcomment'];
			$this->structure = $arr['def_structure'];
			$this->shield = $arr['def_shield'];
			$this->weapon = $arr['def_weapon'];
			$this->heal = $arr['def_heal'];
			$this->fields = $arr['def_fields'];

			$this->isValid = true;
		}
		
		
		function isValid() { return $this->isValid; }
		function name() { return $this->name; }
		function shortComment() { return $this->shortComment; }
		function capacity() { return $this->capacity; }
		function peopleCapacity() { return $this->peopleCapacity; }
		
		function __toString()
		{
			return $this->name;
		}
		
		
		function imgPathSmall() 
		{
			return IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$this->id."_small.".IMAGE_EXT;			
		}
		
		function imgSmall()
		{
			return "<img src=\"".$this->imgPathSmall()."\" style=\"width:40px;height:40px;\"/>";
		}
		
	
	}

?>