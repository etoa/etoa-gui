<?php
/**
 * Support tickets provide easy communication between
 * users and admins in case of problems. This class handles
 * the ticket records and messages
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class Ticket
{
	private $id,$solution,$status,$catId,$userId,$adminId,$timestamp,$adminComment;
	private $userNick,$adminNick;
	private $messages = array();
	private $changed = false;

	/**
	 * Workflow status of ticket
	 * @var array
	 */
	static $statusItems = array(
		"new"=>"Neu",
		"assigned"=>"Zugeteilt",
		"closed"=>"Abgeschlossen");

	/**
	 * Solution type
	 * @var array
	 */
	static $solutionItems = array(
		"open"=>"Offen",
		"solved"=>"Behoben",
		"duplicate"=>"Duplikat",
		"invalid"=>"Ungültig");

	/**
	 * Ticket categories
	 * 
	 * @var array
	 */
	static $categories = array();

	/**
	 * Initializes the class and loads data from database
	 * @param int $id Ticket-ID
	 */
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
				$this->adminComment = $arr['admin_comment'];
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

	/**
	 * Writes ticket changes back to database
	 */
	function __destruct()
	{
		if ($this->changed)
		{
			dbquery("
			UPDATE
				tickets
			SET
				status='".$this->status."',
				solution='".$this->solution."',
				cat_id=".$this->catId.",
				admin_id=".$this->adminId.",
				timestamp=".time().",
				admin_comment='".addslashes($this->adminComment)."'
			WHERE
				id=".$this->id."
			");
		}
	}

	/**
	 * Reads and returns value of internal variable
	 *
	 * @param string $field Variable name
	 * @return mixed Value of requestet variable
	 */
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
			if (!isset($this->adminNick) || $this->adminNick == null)
			{
				$tu = new AdminUser($this->adminId);
				if ($tu->isValid())
				{
					$this->adminNick = $tu->nick;
				}
			}
			return $this->adminNick;
		}
		if ($field=="catId")
		{
			return $this->catId;
		}
		if ($field=="id")
		{
			return $this->id;
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
			if ($this->status=="closed" && isset(self::$solutionItems[$this->solution]))
			{
				return self::$statusItems[$this->status].": ".self::$solutionItems[$this->solution];
			}
			return self::$statusItems[$this->status];
		}
		if ($field=="time")
		{
			return self::$this->timestamp;
		}
		if ($field=="changed")
		{
			return $this->changed;
		}
		if ($field=="adminComment")
		{
			return stripslashes($this->adminComment);
		}
		err_msg(__CLASS__.".get(): Feld $field nicht vorhanden!");
		return null;
	}

	/**
	 * Writes internal variable
	 *
	 * @param string $field Variable name
	 * @param mixed $value Value
	 * @return boolean True if writing succeeded, else false
	 */
	function __set($field,$value)
	{
		if ($field=="status" && isset(self::$statusItems[$value]))
		{
			if ($this->status != $value)
			{
				$this->status = $value;
				$this->changed = true;
				return true;
			}
			return false;
		}
		if ($field=="solution" && isset(self::$solutionItems[$value]))
		{
			if ($this->solution != $value)
			{
				$this->solution = $value;
				$this->changed = true;
				return true;
			}
			return false;
		}
		if ($field=="catId")
		{
			if ($this->catId != $value)
			{
				$this->catId = $value;
				$this->changed = true;
				return true;
			}
			return false;
		}
		if ($field=="adminId")
		{
			if ($this->adminId != $value)
			{
				$this->adminId = intval($value);
				$this->adminNick = null;
				$this->changed = true;
				return true;
			}
			return false;
		}
		if ($field=="adminComment")
		{
			if ($this->adminComment != $value)
			{
				$this->adminComment = $value;
				$this->changed = true;
				return true;
			}
			return false;
		}
		err_msg(__CLASS__.".set(): Feld $field nicht vorhanden!");
		return false;
	}

	/**
	 * Assigns the ticket to an admin
	 *
	 * @param int $adminId ID 
	 */
	function assign($adminId)
	{
		$this->__set("adminId",$adminId);
		$this->__set("status","assigned");
		$mdata['message'] = "Das Ticket wurde dem Administrator ".$this->__get("adminNick")." zugewiesen.";
		$this->addMessage($mdata);
	}

	/**
	 * Closes the ticket and adds a message to the ticket
	 * @param <type> $solution 
	 */
	function close($solution)
	{
		if ($this->status=="assigned")
		{
			$this->__set("status","closed");
			$this->__set("solution","$solution");
			$mdata['message'] = "Das Ticket wurde geschlossen und als ".self::$solutionItems[$this->solution]." gekennzeichnet.";
			$this->addMessage($mdata);
		}
	}

	/**
	 * Reopens an already closed ticket
	 */
	function reopen()
	{
		if ($this->status == "closed")
		{
			$this->__set("adminId",0);
			$this->__set("status","new");
			$mdata['message'] = "Das Ticket wurde wieder eröffnet.";
			$this->addMessage($mdata);
		}
		elseif ($this->status == "assigned")
		{
			$this->__set("adminId",0);
			$this->__set("status","new");
			$mdata['message'] = "Der Ticketadministrator hat das Ticket wieder als Neu markiert.";
			$this->addMessage($mdata);
		}
	}

	/**
	 * Loads and returns all messages which were appended to this ticket
	 *
	 * @return array List of all messages
	 */
	function & getMessages()
	{
		if (count($this->messages)==0)
		{
			$this->messages = & TicketMessage::find(array("ticket_id"=>$this->id));
		}
		return $this->messages;
	}

	/**
	 * Counts all messages of the ticket
	 *
	 * @return int Number of messages
	 */
	function countMessages()
	{
		return count ($this->getMessages());
	}

	/**
	 * Appends a message to the ticket
	 *
	 * @param array $data Contains the sender and message body
	 * @param <type> $informUser Sends a pm to the owner if set to 1 (default)
	 * @return boolean Returns true if success
	 */
	function addMessage($data,$informUser=1)
	{
		if (count($this->messages)==0)
		{
			$this->messages = & TicketMessage::find(array("ticket_id"=>$this->id));
		}
		$tmi = TicketMessage::create(array_merge($data,array("ticket_id"=>$this->id)));
		if ($tmi > 0)
		{
			$this->messages[$tmi] = new TicketMessage($tmi);

			if ($informUser==1 && $this->messages[$tmi]->userId == 0)
			{
				$text = "Hallo!\n\nDein [url ?page=ticket&id=".$this->id."]Ticket ".$this->idString."[/url] wurde aktualisiert!";
				send_msg($this->userId,USER_MSG_CAT_ID,"Dein Ticket ".$this->id."",$text);
			}
			$this->changed = true;
			return true;
		}
		return false;
	}

	//
	// Statics
	//

	/**
	 * Creates a new ticket
	 *
	 * @param array $data An array containing all data to create the ticket (user_id, cat_id, message, admin_id(optional))
	 * @return int The id of the created ticket
	 */
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

	/**
	 * Loads and returns all ticket categories
	 *
	 * @return array Ticket categories as id => name
	 */
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

	/**
	 * Searches a set of tickets accoring to the given conditions
	 * and returns it back as an array
	 *
	 * @param array $args Where conditions
	 * @param array $sort Sorting order
	 * @return array List of search results
	 */
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

	/**
	 * Counts all new tickets
	 * 
	 * @return int Number of new tickets
	 */
	static function countNew()
	{
		$res = dbquery("SELECT COUNT(id) FROM tickets WHERE status='new'");
		$arr = mysql_fetch_row($res);
		return $arr[0];
	}

	/**
	 * Counts all assigned tickets
	 *
	 * @param int $adminId Admin-ID to which the tickets belong
	 * @return int Number of assigned ticker
	 */
	static function countAssigned($adminId)
	{
		$res = dbquery("SELECT COUNT(id) FROM tickets WHERE status='assigned' AND admin_id=".$adminId.";");
		$arr = mysql_fetch_row($res);
		return $arr[0];
	}

}

/**
 * Encapsulates a single ticket message
 *
 * @author Nicolas Perrenoud <mrcage@etoa.ch>
 */
class TicketMessage
{
	private $id;
	var $message,$timestamp,$userId,$adminId;

	/**
	 * Loads the message data from database
	 *
	 * @param int $id Message-ID
	 */
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

	/**
	 * Reads the desired variable value
	 * @param string $field Variable name
	 * @return mixed Variable value
	 */
	function __get($field)
	{
		if ($field=="authorNick")
		{
			if ($this->userId>0)
			{
				$tu = new User($this->userId);
				$this->authorNick = $tu->nick;
				unset($tu);
			}
			elseif ($this->adminId>0)
			{
				$tu = new AdminUser($this->adminId);
				$this->authorNick = $tu->nick." (Admin)";
				unset($tu);
			}
			else
			{
				$this->authorNick = "System";
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

	/**
	 * Creates a new message
	 *
	 * @param array $data Array containing the required data to create the message
	 * @return int Returns the id of the just created message
	 */
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

	/**
	 * Finds and returns a set of messages
	 *
	 * @param array $args WHERE conditions
	 * @param array $sort Sorting order
	 * @return array Set of messages
	 */
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
