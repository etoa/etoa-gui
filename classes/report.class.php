<?php
/**
 * Description of report
 *
 * @author Nicolas
 */
abstract class Report
{
	static $types = array('battle'=>'Kampf','spy'=>'Spionage','explore'=>'Erkundung','market'=>'Markt','crypto'=>'Krypto');

	protected $valid = false;
	protected $type = 'other';
	protected $id, $timestamp, $subject, $content;
	protected $read = false;
	protected $userId=0;
	protected $allianceId=0;
	protected $entity1Id=0;
	protected $entity2Id=0;

	function __construct($id)
	{
		if (is_integer($id))
		{
			$res = dbquery("
			SELECT
				*
			FROM
				reports
			WHERE
				id=".intval($id)."
			LIMIT 1;
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
			}
		}
		elseif (is_array($id))
		{
			$arr = $id;
		}

		if (isset($arr))
		{
			$this->id = $arr['id'];
			$this->timestamp = $arr['timestamp'];
			$this->type = $arr['type'];
			$this->read = $arr['read']==1;
			$this->userId = $arr['user_id'];
			$this->allianceId = $arr['alliance_id'];
			$this->subject = $arr['subject'];
			$this->content = $arr['content'];
			$this->entity1Id = $arr['entity1_id'];
			$this->entity2Id = $arr['entity2_id'];

			$this->valid = true;
		}
	}

	function __get($field)
	{
		try
		{
			return $this->$field;
		}
		catch (Eexception $e)
		{
			echo $e;
		}
		return null;
	}

	static function add($data)
	{
		if (isset($data['user_id']) || isset($data['alliance_id']))
		{
			$fs = "";
			$vs = "";

			if (isset($data['user_id']))
			{
				$fs.= ",user_id";
				$vs.= ",'".$data['user_id']."'";
			}
			if (isset($data['alliance_id']))
			{
				$fs.= ",alliance_id";
				$vs.= ",'".$data['alliance_id']."'";
			}
			if (isset($data['subject']))
			{
				$fs.= ",subject";
				$vs.= ",'".addslashes($data['subject'])."'";
			}
			if (isset($data['content']))
			{
				$fs.= ",content";
				$vs.= ",'".addslashes($data['content'])."'";
			}
			if (isset($data['entity1_id']))
			{
				$fs.= ",entity1_id";
				$vs.= ",'".addslashes($data['entity1_id'])."'";
			}
			if (isset($data['entity2_id']))
			{
				$fs.= ",entity2_id";
				$vs.= ",'".addslashes($data['entity2_id'])."'";
			}

			$sql = "INSERT INTO
				reports
			(
				timestamp,
				type
				".$fs."
			)
			VALUES
			(
				".time().",
				'".(isset($data['type']) && isset(self::$types[$data['type']]) ? $data['type'] : 'other')."'
				".$vs."
			);";
			dbquery($sql);
			return mysql_insert_id();
		}
		err_msg("Kein Report-Besitzer angegeben!");
		dump($data);
		return null;
	}

	static function & find($where=null,$order=null)
	{
		if ($order==null)
			$order = " timestamp DESC ";

		if (is_array($where))
		{
			$wheres	= " WHERE 1 ";
			foreach ($where as $k=>$v)
			{
				$wheres.= " AND `".$k."`='".$v."'";
			}
		}
		else
			$wheres = "";

		$sql = "SELECT * FROM reports $wheres ORDER BY $order";
		$res = dbquery($sql);
		$rtn = array();
		if (mysql_num_rows($res) > 0)
		{
			while ($arr = mysql_fetch_assoc($res))
			{
				$rtn[$arr['id']] = Report::createFactory($arr);
			}
		}
		return $rtn;
	}

	static function createFactory($args)
	{
		if (is_array($args) && isset($args['type']))
		{
			$type = $args['type'];
		}
		elseif (is_integer($args))
		{
			$sql = "SELECT * FROM reports WHERE id=".$args." LIMIT 1;";
			$res = dbquery($sql);
			if (mysql_num_rows($res) > 0)
			{
				$args = mysql_fetch_assoc($res);
				$type = $args['type'];
			}
		}

		try
		{
			if (isset($type))
			{
				switch ($type)
				{
					case 'market':
						return new MarketReport($args);
				}
			}
			throw new Eexception("Keine passende Reportklasse für $type gefunden!");
		}
		catch (Eexception $e)
		{
			echo $e;
		}

		return null;
	}

}
?>
