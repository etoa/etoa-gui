<?PHP

	class ShipList implements IteratorAggregate
	{
		private $userId;
		private $entityId;
		private $items = null;
		private $countArr = null;
		private $bunkeredArr = null;
		private $count = null;
		
		function ShipList($entityId,$userId,$load = 0)
		{
			$this->userId = $userId;
			$this->entityId = $entityId;
			if ($load==1)
				$this->load();
		}
		
		private function load()
		{
			$this->items = array();
			$this->countArr = array();
			$this->count = 0;
			$res = dbquery("
			SELECT
				l.shiplist_ship_id as lid,
				l.shiplist_count as lcnt,
				l.shiplist_bunkered as lbcnt,
				s.*
			FROM
				shiplist l
			INNER JOIN
				ships s 
			ON
				s.ship_id = l.shiplist_ship_id
				AND l.shiplist_user_id=".$this->userId ."
				AND l.shiplist_entity_id=".$this->entityId."
				AND l.shiplist_count>0
			;");
			while ($arr = mysql_fetch_assoc($res))
			{
				$this->items[$arr['lid']] = new Ship($arr);
				$this->countArr[$arr['lid']] = $arr['lcnt'];
				$this->bunkeredArr[$arr['lid']] = $arr['lbcnt'];
				$this->count += $arr['lcnt'];
			}
		}

  	public function getIterator() 
  	{
  		if ($this->items == null)
  			$this->load();
    	return new ArrayIterator($this->items);
  	}

		function countBunkered($item)
		{
			if ($this->bunkeredArr != null)
				return $this->bunkeredArr[$item];
			return 0;
		}

		function count($item=null)
		{
			if ($this->countArr != null)
				return $item>0 ? $this->countArr[$item] : $this->count;

			if ($this->count != null)
				return $this->count;

			$res = dbquery("
			SELECT
				SUM(shiplist_count)
			FROM
				shiplist
			WHERE
				shiplist_user_id=".$this->userId ."
				AND shiplist_entity_id=".$this->entityId."
				".($item>0 ? " AND shiplist_ship_id=".$item."" : "")."
			;");
			$arr = mysql_fetch_row($res);
			$this->count = $arr[0];
			return $this->count;
		}
		
		function getTotalStrucure()
		{
  		if ($this->items == null)
  			$this->load();			
			$i = 0;
			foreach ($this->items as $k=>&$v)
			{
				$i+= $this->countArr[$k] * $v->structure;
			}
			return $i;
		}
		
		function getTotalShield()
		{
  		if ($this->items == null)
  			$this->load();			
			$i = 0;
			foreach ($this->items as $k=>&$v)
			{
				$i+= $this->countArr[$k] * $v->shield;
			}
			return $i;
		}		
		
		function getTotalWeapon()
		{
  		if ($this->items == null)
  			$this->load();			
			$i = 0;
			foreach ($this->items as $k=>&$v)
			{
				$i+= $this->countArr[$k] * $v->weapon;
			}
			return $i;
		}				
		
		function getTotalHeal()
		{
  		if ($this->items == null)
  			$this->load();			
			$i = 0;
			foreach ($this->items as $k=>&$v)
			{
				$i+= $this->countArr[$k] * $v->heal;
			}
			return $i;
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
			$res = dbquery("SELECT 
								shiplist_id, 
								shiplist_count 
							FROM 
								shiplist 
							WHERE 
								shiplist_ship_id=".$shipId." 
								AND shiplist_user_id='".$this->userId."' 
								AND shiplist_entity_id='".$this->entityId."';");
			$arr = mysql_fetch_row($res);

			$delable = min($cnt,$arr[1]);
			
			dbquery("UPDATE
				shiplist
			SET
				shiplist_count = shiplist_count - ".$delable."
			WHERE 
				shiplist_ship_id=".$shipId."
				AND shiplist_id='".$arr[0]."';");

			return $delable;
		}
		
		
	
	
	}


?>