<?PHP

	class DefList
	{
		private $userId;
		private $entityId;
		private $defs;
		
		function DefList($entityId,$userId)
		{
			$this->userId = $userId;
			$this->entityId = $entityId;
		}
		
		function add($defId,$cnt)
		{
			dbquery("
				UPDATE
					deflist
				SET
					deflist_count=deflist_count+".max($cnt,0)."
				WHERE
					deflist_user_id='".$this->userId."'
					AND deflist_entity_id='".$this->entityId."'
					AND deflist_def_id='".$defId."';
			");			
			if(mysql_affected_rows()==0)
			{
				dbquery("
					INSERT INTO
					deflist
					(
						deflist_user_id,
						deflist_entity_id,
						deflist_def_id,
						deflist_count
					)
					VALUES
					(
						'".$this->userId."',
						'".$this->entityId."',
						'".$defId."',
						'".max($cnt,0)."'
					);
				");
			}
		}
		
		function remove($defId,$cnt)
		{
			$res = dbquery("SELECT 
								deflist_id, 
								deflist_count 
							FROM 
								deflist 
							WHERE 
								deflist_def_id=".$defId." 
								AND deflist_user_id='".$this->userId."' 
								AND deflist_entity_id='".$this->entityId."';");
			$arr = mysql_fetch_row($res);

			$delable = min($cnt,$arr[1]);
			
			dbquery("UPDATE
				deflist
			SET
				deflist_count = deflist_count - ".$delable."
			WHERE 
				deflist_def_id=".$defId."
				AND deflist_id='".$arr[0]."';");

			return $delable;
		}
	
	
	}


?>