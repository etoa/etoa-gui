<?PHP

	class Notepad
	{
		private $note;
		private $userId;
		private $num;
		
		function Notepad($userId,$loadAll=0)
		{
			$this->userId = $userId;
			$this->note = array();
			$this->num=-1;
			
			if ($loadAll==1)
			{
				$res=dbQuerySave("
				SELECT 
					n.id,
					n.timestamp,
					nd.subject,
					nd.text 
				FROM 
					notepad as n
				INNER JOIN	
					notepad_data as nd
					ON nd.id=n.id
					AND user_id=?
				ORDER BY 
					timestamp DESC;", array($this->userId));
				$this->num=mysql_num_rows($res);
				if ($this->num>0)
				{
					while ($arr=mysql_fetch_array($res))
					{
						$this->note[$arr['id']] = new Note($arr['id'],stripslashes($arr['subject']),stripslashes($arr['text']),$arr['timestamp']);
					}
				}
			}
			
		}
		
		function getArray()
		{
			return $this->note;
		}
		
		function get($noteId)
		{
			if (isset($this->note[$noteId]))
			{
				return $this->note[$noteId];
			}
			else
			{
				$res=dbQuerySave("
				SELECT 
					n.id,
					n.timestamp,
					nd.subject,
					nd.text 
				FROM 
					notepad as n
				INNER JOIN	
					notepad_data as nd
					ON nd.id=n.id
					AND n.user_id=?
					AND n.id=?",
					array($this->userId, $noteId));
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					$this->note[$arr['id']] = new Note($arr['id'],stripslashes($arr['subject']),stripslashes($arr['text']),$arr['timestamp']);
					return $this->note[$arr['id']];
				}				
				return false;
			}
		}
		
		function add($subject,$text)
		{
			$time = time();
			dbQuerySave("
			INSERT INTO 
				notepad 
			(
				user_id,
				timestamp				
			) 
			VALUES 
			(
				?,
				?
			);", array($this->userId, $time));
			$mid = mysql_insert_id();
			dbQuerySave("
			INSERT INTO 
				notepad_data
			(
				id,
				subject,
				text				
			) 
			VALUES 
			(
				?, ?, ?
			);", array($mid, $subject, $text));
			$this->num++;
			$this->note[$mid] = new Note($mid,$subject,$text,$time);
		}
		

		
		function set($noteId,$subject,$text)
		{
			$time = time();
			dbQuerySave("
			UPDATE 
				notepad
			SET 
				timestamp='".$time."' 
			WHERE 
				user_id=?
				AND id=?
			;", array($this->userId, $noteId));
			if (mysql_affected_rows()>0)
			{
				dbQuerySave("
				UPDATE 
					notepad_data
				SET 
					subject=?,
					text=?
				WHERE 
					id=?;", array($subject, $text, $noteId));
				$this->note[$noteId] = new Note($noteId,$subject,$text,$time);
			}
		}	
		
		function numNotes()
		{
			if ($this->num==-1)
			{
				$res = dbQuerySave("SELECT COUNT(id) FROM notepad WHERE user_id=?;", array($this->userId));
				$cnt = mysql_fetch_row($res);
				$this->num=$cnt[0];
			}
			return $this->num;
		}
		
		function delete($nid)
		{
			dbQuerySave("DELETE FROM notepad WHERE id=? AND user_id=?;", array($nid, $this->userId));
			if (mysql_affected_rows()>0)
			{
				dbQuerySave("DELETE FROM notepad_data WHERE id=?;", array($nid));
				unset($this->note[$nid]);
				$this->num--;
				return true;
			}
			return false;
		}		
		
		function deleteAll()
		{
			$res=dbQuerySave("SELECT id FROM notepad WHERE user_id=?;", array($this->userId));
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_row($res))
				{
					dbQuerySave("DELETE FROM notepad_data WHERE id=?;", array($arr[0]));
				}
			}	
			$res=dbQuerySave("DELETE FROM notepad WHERE user_id=?;", array($this->userId));
		}
		
	}

?>