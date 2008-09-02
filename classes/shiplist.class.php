<?PHP

	class ShipList
	{
		private $userId;
		private $entityId;
		private $ships;
		
		function ShipList($entityId,$userId)
		{
			$this->userId = $userId;
			$this->entityId = $entityId;
		}
		
		function add($shipId,$cnt)
		{
			dbquery("
				UPDATE
					shiplist
				SET
					shiplist_count=shiplist_count+".max($cnt,0)."
				WHERE
					shiplist_user_id='".$this->userId."'
					AND shiplist_entity_id='".$this->entityId."'
					AND shiplist_ship_id='".$shipId."';
			");			
			if(mysql_affected_rows()==0)
			{
				dbquery("
					INSERT INTO
					shiplist
					(
						shiplist_user_id,
						shiplist_entity_id,
						shiplist_ship_id,
						shiplist_count
					)
					VALUES
					(
						'".$this->userId."',
						'".$this->entityId."',
						'".$shipId."',
						'".max($cnt,0)."'
					);
				");
			}
		}
		
		function remove($shipId,$cnt)
		{
			$res = dbquery("SELECT shiplist_count FROM shiplist WHERE shiplist_ship_id=".$shipId.";");
			$arr = mysql_fetch_row($res);

			$delable = min($cnt,$arr[0]);
			
			dbquery("UPDATE
				shiplist
			SET
				shiplist_count = shiplist_count - ".$delable."
			WHERE 
				shiplist_ship_id=".$shipId.";");

			return $delable;
		}
	
	
	}


?>