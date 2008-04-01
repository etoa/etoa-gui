<?PHP

	class Notepad
	{
		private $note;
		private $userId;
		
		function Notepad($userId,$loadAll=0)
		{
			$this->userId = $userId;
			$this->note = array();
			
			if ($loadAll==1)
			{
				$res=dbquery("
				SELECT 
					* 
				FROM 
					notepad 
				WHERE 
					note_user_id=".$this->userId." ORDER BY note_timestamp DESC;");
				if (mysql_num_rows($res)>0)
				{
					while ($arr=mysql_fetch_array($res))
					{
						$this->note[$arr['note_id']] = new Note($arr['note_subject'],$arr['note_text'],$arr['note_timestamp']);
					}
				}
			}
			
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
					* 
				FROM 
					notepad 
				WHERE 
					note_id=".$noteId.";");
				if (mysql_num_rows($res)>0)
				{
					while ($arr=mysql_fetch_array($res))
					{
						$this->note[$arr['note_id']] = new Note($arr['note_subject'],$arr['note_text'],$arr['note_timestamp']);
						return $this->note[$arr['note_id']];
					}
				}				
				return false;
			}
		}
		
		function add(Note $note)
		{
			dbquery("
			INSERT INTO 
				notepad 
			(
				note_user_id,
				note_subject,
				note_text,
				note_timestamp
			) VALUES (
				'".$this->userId."',
				'".$note->subject()."',
				'".$note->text()."',
				'".$note->timestamp()."'
			);");
			$this->note[mysql_insert_id()] = new Note($text,$subject,$time);
		}
		
		function set($noteId,$text,$subject)
		{
			$time = time();
			dbquery("
			UPDATE 
				notepad
			SET 
				note_subject='".addslashes($subject)."',
				note_text='".addslashes($text)."',
				note_timestamp='".time()."' 
			WHERE 
				note_user_id=".$this->userId." 
				AND note_id='".$noteId."'
			;");
			$this->note[$noteId] = new Note($text,$subject,$time);
		}	
		
		
		
	}

?>