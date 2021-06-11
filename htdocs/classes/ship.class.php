<?PHP

	class Ship
	{
        public function __construct($sid)
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
					ships
				WHERE
					ship_id=".$sid."
				");
				if (!$arr = mysql_fetch_assoc($res))
				{
					throw new EException("Ung&uuml;ltige Schiff-ID: $sid");
					return false;
				}
			}

			$this->id = $arr['ship_id'];
			$this->name = $arr['ship_name'];
			$this->shortComment = $arr['ship_shortcomment'];
			$this->structure = $arr['ship_structure'];
			$this->shield = $arr['ship_shield'];
			$this->weapon = $arr['ship_weapon'];
			$this->heal = $arr['ship_heal'];
			$this->capacity = $arr['ship_capacity'];
			$this->peopleCapacity = $arr['ship_people_capacity'];
			$this->speed = $arr['ship_speed'];
			$this->time2start = $arr['ship_time2start'];
			$this->time2land = $arr['ship_time2land'];

			$this->bStructure = $arr['special_ship_bonus_structure'];
			$this->bShield = $arr['special_ship_bonus_shield'];
			$this->bWeapon = $arr['special_ship_bonus_weapon'];
      		$this->bHeal = $arr['special_ship_bonus_heal'];
			$this->bCapa = $arr['special_ship_bonus_capacity'];


			$this->actionString = $arr['ship_actions'];

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

		/*
		function imgPathSmall()
		{
			return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_small.".IMAGE_EXT;
		}*/

		function imgPath($type="s")
		{
			if ($type=="small" || $type=="s")
				return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_small.".IMAGE_EXT;
			if ($type=="middle" || $type=="medium" || $type=="m")
				return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_middle.".IMAGE_EXT;
			return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id.".".IMAGE_EXT;
		}

/*
		function imgSmall($float="")
		{
			if ($float == "left")
				return "<img src=\"".$this->imgPathSmall()."\" style=\"width:40px;height:40px;float:left;margin-right:6px;\"/>";
			return "<img src=\"".$this->imgPathSmall()."\" style=\"width:40px;height:40px;\"/>";
		}
		*/

		function img($type="s",$float="")
		{
			if ($float == "left")
				return "<img src=\"".$this->imgPath($type)."\" style=\"float:left;margin-right:6px;\"/>";
			if ($float == "right")
				return "<img src=\"".$this->imgPath($type)."\" style=\"float:right;\"/>";
			return "<img src=\"".$this->imgPath($type)."\" style=\"\"/>";
		}

		function & getActions($string=0)
		{
			$actions = explode(",",$this->actionString);
			$ao = array();
			$str = "";
			$cnt = count($actions);
			if ($cnt>0)
			{
				foreach ($actions as $i)
				{
					if ($ac = FleetAction::createFactory($i))
					{
						if ($string>0)
						{
							if ($str!="")
								$str.=", ";
							$str.= $ac->__toString();
						}
						else
							$ao[$i] = $ac;
					}
				}
			}
			if ($string>0)
				return $str;
			return $ao;
		}

		function toolTip()
		{
			$tt = "<div style=\"display:none;\" id=\"shiptt".$this->id."\">
			<div style=\"width:450px\">
			".$this->img("m","left")."
			<div style=\"float:left;width:260px\">
			<b>$this->name</b><br/>
			$this->shortComment<br/><br/>
			<table style=\"width:260px;font-size:small;\">
			<tr>
			<td>Schaden:</td><td>".nf($this->weapon)."</td>
			<td>Regeneration:</td><td>".nf($this->heal)."</td>
			</tr><tr>
			<td>Schild:</td><td>".nf($this->shield)."</td>
			<td>Kapazität:</td><td>".nf($this->capacity)."</td>
			</tr><tr>
			<td>Struktur:</td><td>".nf($this->structure)."</td>
			<td>Speed:</td><td>".nf($this->speed)."</td>
			</tr>
			</table><br/>".$this->getActions(1)."</div>
			<br style=\"clear:both;\"/></div></div>";
			return $tt."<span ".tt('shiptt'.$this->id).">".$this->__toString()."</span>";
		}


		static function xpByLevel($base_xp,$factor,$level)
		{
			return $base_xp * intpow($factor,$level-1);
		}

		static function levelByXp($base_xp,$factor,$xp)
		{
			return max(0,floor(1 + ((log($xp)-log($base_xp))/log($factor))));
		}

		static function getItems()
		{
			$res = dbquery("
			SELECT
				*
			FROM
				ships
			ORDER BY
				ship_order
			;");
			$rtn=array();
			while($arr = mysql_fetch_assoc($res))
			{
				$rtn[$arr['ship_id']] = new Ship($arr);
			}
			return $rtn;
		}



	}

?>
