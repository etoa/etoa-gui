<?PHP

use EtoA\Core\Logging\Log;

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
			return $arr[0];
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
			$cnt = ceil($cnt);
			$cnt = max($cnt,0);
			if ($cnt==0)
				error_msg("Warnung: 0 Schiffe hinzugefügt!");
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
						AND shiplist_id='".$arr[0]."'
				LIMIT 1;");

			return $delable;
		}

		function bunker($shipId,$cnt)
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

			$delable = max(0,min($cnt,$arr[1]));

			dbquery("UPDATE
						shiplist
					SET
						shiplist_bunkered = shiplist_bunkered + ".$delable.",
						shiplist_count = shiplist_count - ".$delable."
					WHERE
						shiplist_ship_id=".$shipId."
						AND shiplist_id='".$arr[0]."'
					LIMIT 1;");

			return $delable;
		}

		function leaveShelter($shipId,$cnt)
		{
			$res = dbquery("SELECT
								shiplist_id,
								shiplist_bunkered
							FROM
								shiplist
							WHERE
								shiplist_ship_id=".$shipId."
								AND shiplist_user_id='".$this->userId."'
								AND shiplist_entity_id='".$this->entityId."';");
			$arr = mysql_fetch_row($res);

			$delable = max(0,min($cnt,$arr[1]));

			dbquery("UPDATE
						shiplist
					SET
						shiplist_bunkered = shiplist_bunkered - ".$delable.",
						shiplist_count = shiplist_count + ".$delable."
					WHERE
						shiplist_ship_id=".$shipId."
						AND shiplist_id='".$arr[0]."'
					LIMIT 1;");
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
            // TODO
            global $app;

            /** @var Log */
            $log = $app['etoa.log.service'];

			dbquery("DELETE FROM
						`shiplist`
					WHERE
						`shiplist_count`='0'
						AND `shiplist_bunkered`='0'
						AND `shiplist_special_ship`='0'
						;");
			$nr = mysql_affected_rows();
			$log->add(Log::F_SYSTEM, Log::INFO, "$nr leere Schiffsdatensätze wurden gelöscht!");
			return $nr;
		}


	}


?>
