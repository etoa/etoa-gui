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
				COUNT(fleet_id) 
			FROM 
				fleet 
			WHERE 
				fleet_user_id='".$this->userId."' 
			AND 
				(
					fleet_entity_from='".$entId."' 
					AND fleet_status=0
				) 
				OR 
				(
					fleet_entity_to='".$entId."' 
					AND fleet_status>0  
				);");
			$arr = mysql_fetch_row($res);
			return $arr[0];		
		}
	
	}

?>