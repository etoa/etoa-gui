<?PHP

	class ShipList implements IteratorAggregate
	{
		private $userId;
		private $entityId;
		private $items = null;
		private $countArr = null;
		private $bunkeredArr = null;
		private $count = null;
		private $special = null;

        public function __construct($entityId,$userId,$load = 0)
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
				l.shiplist_special_ship_bonus_weapon as bweapon,
				l.shiplist_special_ship_bonus_structure as bstructure,
				l.shiplist_special_ship_bonus_shield as bshield,
        l.shiplist_special_ship_bonus_heal as bheal,
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
				if ($arr['special_ship'])
				{
					$this->special[$arr['lid']] = array($arr['bstructure'],$arr['bshield'],$arr['bweapon'],$arr['bheal']);
				}
				else
				{
					$this->special[$arr['lid']] = array(0,0,0,0);
				}
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

		function getBStructure()
		{
  			if ($this->items == null)
  				$this->load();
				$i = 0;
				foreach ($this->items as $k=>&$v)
				{
					$i+= $this->special[$k][0] * $v->bStructure;
				}
				return $i;
		}

		function getBShield()
		{
  			if ($this->items == null)
  				$this->load();
				$i = 0;
				foreach ($this->items as $k=>&$v)
				{
					$i+= $this->special[$k][1] * $v->bShield;
				}
				return $i;
		}

		function getBWeapon()
		{
  			if ($this->items == null)
  				$this->load();
				$i = 0;
				foreach ($this->items as $k=>&$v)
				{
					$i+= $this->special[$k][2] * $v->bWeapon;
				}
				return $i;
		}

    function getBHeal()
		{
  			if ($this->items == null)
  				$this->load();
				$i = 0;
				foreach ($this->items as $k=>&$v)
				{
					$i+= $this->special[$k][3] * $v->bHeal;
				}
				return $i;
		}

		/**
		* Remove empty data
		*/
		static function cleanUp()
		{
			dbquery("DELETE FROM
						`shiplist`
					WHERE
						`shiplist_count`='0'
						AND `shiplist_bunkered`='0'
						AND `shiplist_special_ship`='0'
						;");
			$nr = mysql_affected_rows();
			Log::add("4", Log::INFO, "$nr leere Schiffsdatensätze wurden gelöscht!");
			return $nr;
		}


	}


?>
