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
				$res=dbquery("
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
					AND user_id=".$this->userId." 
				ORDER BY 
					timestamp DESC;");
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
				$res=dbquery("
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
					AND n.user_id=".$this->userId."
					AND n.id=".$noteId.";");
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
			dbquery("
			INSERT INTO 
				notepad 
			(
				user_id,
				timestamp				
			) 
			VALUES 
			(
				'".$this->userId."',
				'".$time."'
			);");
			$mid = mysql_insert_id();
			dbquery("
			INSERT INTO 
				notepad_data
			(
				id,
				subject,
				text				
			) 
			VALUES 
			(
				".$mid.",
				'".addslashes($subject)."',
				'".addslashes($text)."'
			);");			
			$this->num++;
			$this->note[$mid] = new Note($mid,$subject,$text,$time);
		}
		

		
		function set($noteId,$subject,$text)
		{
			$time = time();
			dbquery("
			UPDATE 
				notepad
			SET 
				timestamp='".$time."' 
			WHERE 
				user_id=".$this->userId." 
				AND id='".$noteId."'
			;");
			if (mysql_affected_rows()>0)
			{
				dbquery("
				UPDATE 
					notepad_data
				SET 
					subject='".addslashes($subject)."',
					text='".addslashes($text)."'
				WHERE 
					id='".$noteId."'
				;");			
				$this->note[$noteId] = new Note($noteId,$subject,$text,$time);
			}
		}	
		
		function numNotes()
		{
			if ($this->num==-1)
			{
				$res=dbquery("SELECT COUNT(id) FROM notepad WHERE user_id=".$this->userId.";");
				$cnt = mysql_fetch_row($res);
				$this->num=$cnt[0];
			}
			return $this->num;
		}
		
		function delete($nid)
		{
			dbquery("DELETE FROM notepad WHERE id=".$nid." && user_id=".$this->userId.";");
			if (mysql_affected_rows()>0)
			{
				dbquery("DELETE FROM notepad_data WHERE id=".$nid.";");
				unset($this->note[$nid]);
				$this->num--;
				return true;
			}
			return false;
		}		
		
		function deleteAll()
		{
			$res=dbquery("SELECT id FROM notepad WHERE user_id=".$this->userId.";");
			if (mysql_num_rows($res)>0)
			{
				while ($arr=mysql_fetch_row($res))
				{
					dbquery("DELETE FROM notepad_data WHERE id=".$arr[0]);
				}
			}	
			$res=dbquery("DELETE FROM notepad WHERE user_id=".$this->userId.";");
		}
		
	}

?>