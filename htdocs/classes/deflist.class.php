<?PHP

	class DefList implements IteratorAggregate
	{
		private $userId;
		private $entityId;
		private $items = null;
		private $countArr = null;
		private $count = null;
		
		function DefList($entityId,$userId,$load=0)
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
				l.deflist_def_id as lid,
				l.deflist_count as lcnt,
				d.*
			FROM
				deflist l
			INNER JOIN
				defense d 
			ON
				d.def_id = l.deflist_def_id
				AND l.deflist_user_id=".$this->userId ."
				AND l.deflist_entity_id=".$this->entityId."
				AND l.deflist_count>0
			;");
			while ($arr = mysql_fetch_assoc($res))
			{
				$this->items[$arr['lid']] = new Defense($arr);
				$this->countArr[$arr['lid']] = $arr['lcnt'];
				$this->count += $arr['lcnt'];
			}
		}		
		
  	public function getIterator() 
  	{
  		if ($this->items == null)
  			$this->load();
    	return new ArrayIterator($this->items);
  	}
  	
		function count($item=null)
		{
			if ($this->countArr != null)
				return $item>0 ? $this->countArr[$item] : $this->count;

			if ($this->count != null)
				return $this->count;

			$res = dbquery("
			SELECT
				SUM(deflist_count)
			FROM
				deflist
			WHERE
				deflist_user_id=".$this->userId ."
				AND deflist_entity_id=".$this->entityId."
				".($item>0 ? " AND deflist_def_id=".$item."" : "")."
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
		
		function add($defId,$cnt)
		{
			$cnt = intval($cnt);
			$defId = intval($defId);
			
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
			$defId = intval($defId);
			
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

			$delable = intval(min($cnt,$arr[1]));
			
			dbquery("UPDATE
				deflist
			SET
				deflist_count = deflist_count - ".$delable."
			WHERE 
				deflist_def_id=".$defId."
				AND deflist_id='".$arr[0]."';");

			return $delable;
		}
		
		/**
		* Remove empty data
		*/
		static function cleanUp()
		{
			dbquery("DELETE FROM 
						`deflist`
					WHERE 
						`deflist_count`='0'
						;");
			$nr = mysql_affected_rows();
			add_log("4","$nr leere Verteidigungsdatensätze wurden gelöscht!");
			return $nr;
		}
	
	}


?>