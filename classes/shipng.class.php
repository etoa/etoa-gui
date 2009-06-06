<?php
/**
 * Description of ShipNG
 *
 * @author Nicolas
 */
class ShipNG
{
	private static $table = "ng_ships";
	private $valid = false;

	function __construct($id)
	{
		$res = dbquery("
		SELECT
			s.*,
			t.cat_name as type_name
		FROM
			".self::$table." s
		INNER JOIN
			ship_cat t
			ON t.cat_id = s.type_id
		WHERE
			id=".$id."
		LIMIT 1");
		if (mysql_num_rows($res)>0)
		{
			$this->valid = true;
			$arr = mysql_fetch_assoc($res);
			foreach ($arr as $k=>$v)
			{
				if (stristr($k,"_"))
				{
					$ex = explode("_",$k);
					$str = array_shift($ex);
					foreach ($ex as $ev)
					{
						$str.=ucfirst($ev);
					}
					$this->$str = $v;
				}
				else
				$this->$k = $v;
			}
		}
	}

	function __get($field)
	{
		if (isset($this->$field))
		return $this->$field;
		return null;
	}

	static function find($where=null,$order=null)
	{
		$res = dbquery("
		SELECT
			id
		FROM
			".self::$table."
		");
		$result = array();
		if (mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_assoc($res))
			{
				$result[$arr['id']] = new self($arr['id']);
			}
		}
		return $result;
	}


	function imgPath($type="s")
	{
		if ($type=="small" || $type=="s")
		return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_small.".IMAGE_EXT;
		if ($type=="middle" || $type=="medium" || $type=="m")
		return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id."_middle.".IMAGE_EXT;
		return IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$this->id.".".IMAGE_EXT;
	}

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
		$actions = explode(",",$this->actions);
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


}
?>
