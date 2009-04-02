<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ticket
 *
 * @author Nicolas
 */
class Ticket
{
	private $solution,$status,$id,$catId,$userId,$adminId,$timestamp;
	private $userNick,$adminNick;
	private $messages = array();

	static $statusItems = array(
		"new"=>"Neu",
		"assigned"=>"Zugeteilt",
		"closed"=>"Geschlossen");
	static $solutionItems = array(
		"open"=>"Offen",
		"solved"=>"Behoben",
		"duplicate"=>"Duplikat",
		"invalid"=>"Ungültig");

	static $categories = array();

	function __construct($id)
	{
		try
		{
			$res = dbquery("
			SELECT
				*
			FROM
				tickets
			WHERE
				id=".$id."
			");
			if ($arr = mysql_fetch_assoc($res))
			{
				$this->solution = $arr['solution'];
				$this->status = $arr['status'];
				$this->id = $id;
				$this->catId = $arr['cat_id'];
				$this->userId = $arr['user_id'];
				$this->adminId = $arr['admin_id'];
				$this->timestamp = $arr['timestamp'];
			}
			else
			{
				throw new EException("Ungültige Ticket-ID");
			}
		}
		catch(Exception $e)
		{
			echo $e;
		}
	}

	function __get($field)
	{
		if ($field=="idString")
		{
			return "#".$this->id;
		}
		if ($field=="userNick")
		{
			if (!isset($this->userNick))
			{
				$tu = new User($this->userId);
				$this->userNick = $tu->nick;
			}
			return $this->userNick;
		}
		if ($field=="userId")
		{
			return $this->userId;
		}
		if ($field=="adminId")
		{
			return $this->adminId;
		}
		if ($field=="adminNick")
		{
			return $this->adminNick;
		}
		if ($field=="catId")
		{
			return $this->catId;
		}
		if ($field=="status")
		{
			return $this->status;
		}
		if ($field=="solution")
		{
			return $this->solution;
		}
		if ($field=="catName")
		{
			$this->getCategories();
			return self::$categories[$this->catId];
		}
		if ($field=="statusName")
		{
			if ($this->status=="closed")
				return self::$statusItems[$this->status].":".self::$solutionItems[$this->solution];;
			return self::$statusItems[$this->status];
		}
		if ($field=="time")
		{
			return self::$this->timestamp;
		}
	}

	function & getMessages()
	{
		if (count($this->messages)==0)
		{
			$this->messages = & TicketMessage::find(array("ticket_id"=>$this->id));
		}
		return $this->messages;
	}

	function addMessage($data)
	{
		if (count($this->messages)==0)
		{
			$this->messages = & TicketMessage::find(array("ticket_id"=>$this->id));
		}
		$tmi = TicketMessage::create(array_merge($data,array("ticket_id"=>$this->id)));
		if ($tmi > 0)
		{
			$this->messages[$tmi] = new TicketMessage($tmi);
			return true;
		}
		return false;
	}

	static function create(&$data)
	{
		dbquery("
		INSERT INTO
			tickets
		(
			user_id,
			cat_id,
			admin_id,
			timestamp,
			status,
			solution
		)
		VALUES
		(
			".$data['user_id'].",
			".$data['cat_id'].",
			".(isset($data['admin_id'])?$data['admin_id']:0).",
			".time().",
			'new',
			'open'
		);");
		$tid = mysql_insert_id();
		TicketMessage::create(array("ticket_id"=>$tid,"user_id"=>$data['user_id'],"message"=>$data['message']));
		return $tid;
	}

	static function & getCategories()
	{
		if (count(self::$categories)==0)
		{
			$res = dbquery("SELECT id,name FROM ticket_cat ORDER BY sort,name;");
			while ($arr=mysql_fetch_row($res))
			{
				self::$categories[$arr[0]] = $arr[1];
			}
		}
		return self::$categories;
	}

	static function & find($args=null,$sort=null)
	{
		$where = "";
		if ($args!=null)
		{
			$where = "WHERE 1 ";
			foreach ($args as $k => $v)
			{
				$where.= " AND `".$k."`='".$v."' ";
			}
		}

		$order = "status,
			timestamp DESC";
		if ($sort!=null)
		{
			$order = "";
			$sc = count($sort);
			$scc = 0;
			foreach ($sort as $v)
			{
				$scc++;
				$order.= " $v ";
				if ($scc<$sc)
					$order.=",";
			}
		}

		$rtn = array();
		$res = dbquery("
		SELECT
			id
		FROM
			tickets
		".$where."
		ORDER BY
			".$order."
		");
		if (mysql_num_rows($res)>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				$rtn[$arr['id']] = new Ticket($arr['id']);
			}
		}
		return $rtn;
	}

}

class TicketMessage
{
	private $id;
	var $message,$timestamp,$userId,$adminId;

	function __construct($id)
	{
		try
		{
			$res = dbquery("
			SELECT
				*
			FROM
				ticket_msg
			WHERE
				id=".$id."
			");
			if ($arr = mysql_fetch_assoc($res))
			{
				$this->id = $id;
				$this->userId = $arr['user_id'];
				$this->adminId = $arr['admin_id'];
				$this->timestamp = $arr['timestamp'];
				$this->message = $arr['message'];
			}
			else
			{
				throw new EException("Ungültige Ticket-Nachricht-ID");
			}
		}
		catch(Exception $e)
		{
			echo $e;
		}
	}

	function __get($field)
	{
		if ($field=="authorNick")
		{
			if ($this->userId>0)
			{
				$tu = new User($this->userId);
				$this->authorNick = $tu->nick;
			}
			elseif ($this->adminId>0)
			{
				// TODO: Fix it
				$this->authorNick = $this->adminId." (Admin)";
			}
			else
			{
				$this->authorNick = "Niemand";
			}
			return $this->authorNick;
		}
		if ($field=="message")
		{
			return text2html($this->message);
		}
		if ($field=="id")
		{
			return $this->id;;
		}
	}

	static function create($data)
	{
		dbquery("
		INSERT INTO
			ticket_msg
		(
			ticket_id,
			user_id,
			admin_id,
			message,
			timestamp
		)
		VALUES
		(
			".$data['ticket_id'].",
			".(isset($data['user_id'])?$data['user_id']:0).",
			".(isset($data['admin_id'])?$data['admin_id']:0).",
			'".addslashes($data['message'])."',
			".time()."
		);");
		$tid = mysql_insert_id();
		return $tid;
	}

	static function & find($args=null,$sort=null)
	{
		$where = "";
		if ($args!=null)
		{
			$where = "WHERE 1 ";
			foreach ($args as $k => $v)
			{
				$where.= " AND `".$k."`='".$v."' ";
			}
		}

		$order = " timestamp ASC";
		if ($sort!=null)
		{
			$order = "";
			$sc = count($sort);
			$scc = 0;
			foreach ($sort as $v)
			{
				$scc++;
				$order.= " $v ";
				if ($scc<$sc)
					$order.=",";
			}
		}
		
		$rtn = array();
		$res = dbquery("
		SELECT
			id
		FROM
			ticket_msg
		".$where."
		ORDER BY
			".$order."
		");
		if (mysql_num_rows($res)>0)
		{
			while($arr=mysql_fetch_assoc($res))
			{
				$rtn[$arr['id']] = new TicketMessage($arr['id']);
			}
		}
		return $rtn;
	}

}

?>
