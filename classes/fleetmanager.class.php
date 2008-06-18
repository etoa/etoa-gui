<?PHP

	class FleetManager
	{
		private $userId;
		
		function FleetManager($userId)
		{
			$this->userId = $userId;
		}
	
		function countControlledByEntity($entId)
		{
			$res = dbquery("
			SELECT 
				COUNT(id) 
			FROM 
				fleet 
			WHERE 
				user_id='".$this->userId."' 
			AND 
				(
					entity_from='".$entId."' 
					AND status=0
				) 
				OR 
				(
					entity_to='".$entId."' 
					AND status>0  
				);");
			$arr = mysql_fetch_row($res);
			return $arr[0];		
		}
	
	}

?>