<?php
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	// $Author: mrcage $
	// $Date: 2009-05-28 12:20:00 +0200 (Do, 28 Mai 2009) $
	// $Rev: 1058 $
	//

/**
 * Implements a report management system, replacing some of the
 * automatically generated ingame messages. This is an abstract basic class  based
 * on the table 'reports'. Every report type must implement it's own class inherited
 * from this class (and possibly have own table on an 1:1 relation with 'reports').
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
abstract class Report
{
	/**
	 * @var array Available report types
	 */
	static $types = array('battle'=>'Kampf','spy'=>'Spionage','explore'=>'Erkundung','market'=>'Markt','crypto'=>'Krypto','other'=>'Sonstige');

	protected $valid = false;
	protected $type = 'other';
	protected $id;
	protected $timestamp;
	protected $subject;
	protected $content;
	protected $read = false;
	protected $userId=0;
	protected $allianceId=0;
	protected $entity1Id=0;
	protected $entity2Id=0;
	protected $opponent1Id=0;

	/**
	 * Class constructor. To be called from the derived class.
	 *
	 * @param mixed $id Accepts a record id or an array of already fetched record data
	 */
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
			$this->opponent1Id = $arr['opponent1_id'];
			
			$this->valid = true;
		}
	}

	/**
	 * Class property getter
	 *
	 * @param string $field Property name
	 * @return mixed Requested property value
	 */
	function __get($field)
	{
		try
		{
			if (isset($this->$field))
				return $this->$field;
			throw new Eexception("Property $field does not exists!");
		}
		catch (Eexception $e)
		{
			echo $e;
		}
		return null;
	}

	function __set($field,$value)
	{
		try
		{
			if (isset($this->$field))
			{
				if ($field == "read")
				{
					$this->$field = $value;
					dbquery("UPDATE reports SET `".$field."`=".($value ? 1:0)." WHERE id=$this->id;");
					return true;
				}
				throw new Eexception("Property $field is write protected!");
			}
			throw new Eexception("Property $field does not exists!");
		}
		catch (Eexception $e)
		{
			echo $e;
			return false;
		}
		return false;
	}

	/**
	 * Adds a new report. To be called from the derived class.
	 *
	 * @param array $data Associative array containing various fields
	 * @return boolean True if adding was successfull, false otherwise
	 */
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
			if (isset($data['opponent1_id']))
			{
				$fs.= ",opponent1_id";
				$vs.= ",'".addslashes($data['opponent1_id'])."'";
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

	/**
	 * Gets a list of reports
	 *
	 * @param array $where WHERE conditions where $arrayKey is database field name
	 * and $arrayValue is database field value
	 * @param string $order ORDER query string
	 * @return array Array containing a list of reports
	 */
	static function & find($where=null,$order=null,$limit="",$count=0)
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

		if ($count>0)
		{
			$sql = "SELECT COUNT(id) FROM reports $wheres ORDER BY $order";
			if ($limit != "" || $limit>0)
				$sql.=" LIMIT $limit";
			$res = dbquery($sql);
			$arr = mysql_fetch_row($res);
			return $arr[0];
		}

		$sql = "SELECT * FROM reports $wheres ORDER BY $order";
		if ($limit != "" || $limit>0)
			$sql.=" LIMIT $limit";
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

	/**
	 * Factory design pattern for getting instances depending on funcion argument
	 *
	 * @param mixed $args Array containing fetched database record or a record id
	 * @return Report New report object instance
	 */
	static function createFactory($args)
	{
		if (is_array($args) && isset($args['type']))
		{
			$type = $args['type'];
		}
		elseif (intval($args)>0)
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

		/**
		 * Check new messages of the given user
		 * 
		 * @param int $userId
		 * @return int Number of new messages
		 */
		static function countNew($userId)
		{
			$res = dbquery("SELECT COUNT(id) FROM reports WHERE user_id=".intval($userId)." AND `read`=0;");
			$arr = mysql_fetch_row($res);
			return $arr[0];
		}

		function typeName()
		{
			return self::$types[$this->type];
		}

	}
	?>
