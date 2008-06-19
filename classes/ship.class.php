<?PHP
	
	class Ship
	{
		function Ship($sid)
		{
			$this->isValid = false;
			
			$res = dbquery("
			SELECT
				*
			FROM	
				ships
			WHERE
				ship_id=".$sid."			
			");			
			if ($arr = mysql_fetch_assoc($res))
			{
				$this->name = $arr['ship_name'];
				$this->shortComment = $arr['ship_shortcomment'];
				$this->capacity = $arr['ship_capacity'];
				$this->peopleCapacity = $arr['ship_people_capacity'];

				$this->id = $sid;
				$this->isValid = true;
			}		
		}
		
		
		function isValid() { return $this->isValid; }
		function name() { return $this->name; }
		function shortComment() { return $this->shortComment; }
		function capacity() { return $this->capacity; }
		function peopleCapacity() { return $this->peopleCapacity; }
		
		
		
		function imgPathSmall() 
		{
			return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_small.".IMAGE_EXT;			
		}
		
		function imgSmall()
		{
			return "<img src=\"".$this->imgPathSmall()."\" style=\"width:40px;height:40px;\"/>";
		}
		
		static function xpByLevel($base_xp,$factor,$level)
		{
			return $base_xp * intpow($factor,$level-1);
		}
		
		static function levelByXp($base_xp,$factor,$xp)
		{
			return max(0,floor(1 + ((log($xp)-log($base_xp))/log($factor))));
		}
	
	}

?>