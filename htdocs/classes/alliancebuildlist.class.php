<?PHP

	class AllianceBuildList implements IteratorAggregate
	{
		private $allianceId;

		private $alliance;

		private $items = null;
		private $itemStatus = null;
		private $count = null;

		private $tmpItems = array();

		private $jobs = null;

		private $errorMsg;

		private $show;

		/**
		 * Constructor
		 * @param int $allianceId
		 * @param int|bool $load
		 * @param ?Alliance $alliance
		 */
        public function __construct($allianceId,$load=0,&$alliance=null)
		{
			$this->allianceId = $allianceId;

			if ($alliance != null)
				$this->alliance = $alliance;
			if ($load == 1)
				$this->load();
		}

		public function getIterator()
		{
			if ($this->items == null)
				$this->load();
			return new ArrayIterator($this->items);
		}

		private function load()
		{
			$this->items = array();
			$this->itemStatus = array();
			$this->count = 0;

			$res = dbquery("
			SELECT
				l.alliance_buildlist_id,
				l.alliance_buildlist_current_level,
				l.alliance_buildlist_build_start_time,
				l.alliance_buildlist_build_end_time,
				l.alliance_buildlist_cooldown,
				l.alliance_buildlist_member_for,
				i.*
			FROM
				alliance_buildings i
			LEFT JOIN
				alliance_buildlist l
			ON
				l.alliance_buildlist_building_id = i.alliance_building_id
				AND l.alliance_buildlist_alliance_id='".$this->allianceId."'
				;");
			if (mysql_num_rows($res)>0)
			{
				while ($arr = mysql_fetch_assoc($res))
				{
					$this->items[$arr['alliance_building_id']] = new AllianceBuilding($arr);
					$this->itemStatus[$arr['alliance_building_id']] = array(
						'listid' => $arr['alliance_buildlist_id'],
						'level' => $arr['alliance_buildlist_current_level'],
						'cooldown' => $arr['alliance_buildlist_cooldown'],
						'member_for' => $arr['alliance_buildlist_member_for'],
						'start_time' => $arr['alliance_buildlist_build_start_time'],
						'end_time' => $arr['alliance_buildlist_build_end_time']
					);
					$this->count++;
				}
			}
		}

		function count()
		{
			if ($this->count != null)
				return $this->count;
			if ($this->items != null)
			{
				$this->count = count($this->items);
				return $this->count;
			}
			$res = dbquery("
			SELECT
				COUNT(alliance_buildlist_id)
			FROM
				alliance_buildlist
			WHERE
				alliance_buildlist_alliance_id=".$this->allianceId."
			;");
			$arr = mysql_fetch_row($res);
			$this->count = $arr[0];
			return $this->count;
		}

		function & item($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
				return $this->items[$bid];
			if (isset($this->tmpItems[$bid]))
				return $this->tmpItems[$bid];
			return false;
		}

		function getLevel($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
				return $this->itemStatus[$bid]['level'];
			return 0;
		}

		function getMemberFor($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
				return $this->itemStatus[$bid]['member_for'];
			return 0;
		}

		function getCooldown($bid, $uid=null)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
			{
				if ($uid != null) {
					$res = dbQuerySave("
					SELECT
						cooldown_end
					FROM
						alliance_building_cooldown
					WHERE
						cooldown_user_id=?
						AND cooldown_alliance_building_id=?
						;", array($uid, $bid));
					if (mysql_num_rows($res)>0)
					{
						$arr = mysql_fetch_assoc($res);
						return $arr['cooldown_end'];
					}
				} else {
					if ($this->itemStatus[$bid]['cooldown'] > time())
						return $this->itemStatus[$bid]['cooldown'];
				}
			}
			return false;
		}

		function setCooldown($bid, $cd, $uid=null)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
			{
				if ($uid != null) {
					dbQuerySave("
					REPLACE INTO
						alliance_building_cooldown
					(
						cooldown_user_id,
						cooldown_alliance_building_id,
						cooldown_end
					) VALUES (
						?,?,?
					);", array($uid, $bid, $cd));
				} else {
					$this->itemStatus[$bid]['cooldown'] = $cd;
					$res = dbquery("
					UPDATE
						alliance_buildlist
					SET
						alliance_buildlist_cooldown=".$cd."
					WHERE
						alliance_buildlist_id=".$this->itemStatus[$bid]['listid'].";");
				}
			}
		}

		function getBuildTime($itemId,$targetLevel)
		{
			$targetLevel = max(1,$targetLevel);

			if (isset($this->items[$itemId]))
			{
				$itm = &$this->items[$itemId];
			}
			else
			{
				$itm = new AllianceBuilding($itemId);
			}

			$btime = $itm->buildTime*($this->itemStatus[$itemId]['level']+1);;

			unset($itm);
			return $btime;
		}

		/**
		* Check wether an item is buildable. Conditions are
		* enough resources, not maxed out level, enough fields,
		* and satisfied prerequisites.
		*/
		function checkBuildable($itemId)
		{
			global $cu;
			if ($this->alliance == null)
			{
				if ($cu->alliance->id != $this->allianceId)
					$this->alliance = new Alliance($this->allianceId);
				else
					$this->alliance = &$cu->alliance;
			}

			if (isset($this->items[$itemId]))
			{
				$itm = &$this->items[$itemId];
				$cst = $itm->getCosts($this->itemStatus[$itemId]['level']+1,$this->alliance->memberCount);
				$lvl = $this->itemStatus[$itemId]['level'];
			}
			else
			{
				$itm = new AllianceBuilding($itemId);
				if (!$itm->isValid())
					return false;
				$cst = $itm->getCosts(1,$this->alliance->memberCount);
				$lvl = 0;
			}

			if ($this->show($itemId))
			{
				// Check level
				if ($lvl < $itm->maxLevel)
				{
					if (!$this->isUnderConstruction())
					{
						// Check costs
						if ($cst[1] <= $this->alliance->resMetal
						&& $cst[2] <= $this->alliance->resCrystal
						&& $cst[3] <= $this->alliance->resPlastic
						&& $cst[4] <= $this->alliance->resFuel
						&& $cst[5] <= $this->alliance->resFood)
						{
							return true;
						}
						else
							$this->errorMsg = "Zuwenig Rohstoffe vorhanden!";
					}
					else
						$this->errorMsg = "Es wird bereits gebaut!";
				}
				else
					$this->errorMsg = "Maximalstufe erreicht!";
			}
			return false;
		}

		/**
		* Returns a message of the last error produced by this instance
		*/
		function getLastError()
		{
			return $this->errorMsg;
		}

		/**
		* Starts the constructions
		*
		* @param int $itemId Item-ID
		*/
		function build($itemId)
		{
			global $cu;
			if ($this->alliance == null)
			{
				if ($cu->alliance->id != $this->allianceId)
					$this->alliance = new Alliance($this->allianceId);
				else
					$this->alliance = &$cu->alliance;
			}

			if ($this->checkBuildable($itemId))
			{
				if (isset($this->items[$itemId]))
				{
					$itm = &$this->items[$itemId];
					$lvl = $this->itemStatus[$itemId]['level']+1;
				}
				else
				{
					$itm = new AllianceBuilding($itemId);
					if (!$itm->isValid())
						return false;
					$lvl = 1;
				}

				$cst = $itm->getCosts($this->itemStatus[$itemId]['level']+1,$this->alliance->memberCount);

				$this->alliance->changeRes(-$cst[1],-$cst[2],-$cst[3],-$cst[4],-$cst[5]);

				$t = time();
				$startTime = $t;
				$endTime = $startTime + $this->getBuildTime($itemId,$lvl);
				$this->itemStatus[$itemId]['start_time'] = $startTime;
				$this->itemStatus[$itemId]['end_time'] = $endTime;

				if ($this->itemStatus[$itemId]['level'] == 0)
				{
					dbquery("
							INSERT INTO
								`alliance_buildlist`
							(
							 	`alliance_buildlist_alliance_id`,
								`alliance_buildlist_building_id`,
								`alliance_buildlist_current_level`,
								`alliance_buildlist_build_start_time`,
								`alliance_buildlist_build_end_time`,
								`alliance_buildlist_member_for`
							)
							VALUES
							(
							 	'".$this->allianceId."',
								'".$itemId."',
								'0',
								'".$startTime."',
								'".$endTime."',
								'".$this->alliance->memberCount."'
							);");
				}
				else
				{
					dbquery("
							UPDATE
								`alliance_buildlist`
							SET
								`alliance_buildlist_build_start_time`='".$startTime."',
								`alliance_buildlist_build_end_time`='".$endTime."',
								`alliance_buildlist_member_for`='".$this->alliance->memberCount."'
							WHERE
							 	`alliance_buildlist_alliance_id`='".$this->allianceId."'
								AND `alliance_buildlist_building_id`='".$itemId."'
							LIMIT 1;
							");
				}

                global $app;
                /** @var \EtoA\Alliance\AllianceHistoryRepository $allianceHistoryRepository */
                $allianceHistoryRepository = $app[\EtoA\Alliance\AllianceHistoryRepository::class];
                $allianceHistoryRepository->addEntry((int) $cu->allianceId, "[b]".$cu->nick."[/b] hat das Gebäude [b]".$this->items[$itemId]->name." (".$lvl.")[/b] in Auftrag gegeben.");
				return true;
			}
			return false;
		}

		function isUnderConstruction($itemId=0)
		{
			try
			{
				if ($itemId>0)
				{
					if (isset($this->itemStatus[$itemId]))
						return ($this->itemStatus[$itemId]['end_time']>time()) ? $this->itemStatus[$itemId]['end_time'] : FALSE;
					else
						throw new EException("Gebäude $itemId existiert nicht!");
				}
				else
				{
					foreach ($this->itemStatus as $buildItem)
						if ($buildItem['end_time']>time()) return TRUE;

					return FALSE;
				}
			}
			catch (Exception $e)
			{
				echo $e;
				return;
			}
		}

		function isMaxLevel($itemId=0)
		{
			try
			{
				if (isset($this->itemStatus[$itemId]))
					return ($this->itemStatus[$itemId]['level'] < $this->items[$itemId]->maxLevel) ? FALSE : TRUE;
				else
					throw new EException("Gebäude $itemId existiert nicht!");
			}
			catch (Exception $e)
			{
				echo $e;
				return;
			}
		}

		function show($itemId=0)
		{
			try
			{
				if (isset($this->itemStatus[$itemId]))
				{
					if ($this->items[$itemId]->show == FALSE) return FALSE;
					$req = $this->items[$itemId]->getBuildingRequirements();
					foreach ($req as $rk=>$rv)
					{
						if ($rv > $this->getLevel($rk))
						{
							$this->errorMsg = "Voraussetzungen nicht erfüllt!";
							return FALSE;
						}
					}
					return TRUE;
				}
				else
					throw new EException("Gebäude $itemId existiert nicht!");
			}
			catch (Exception $e)
			{
				echo $e;
				return;
			}
		}
	}

?>
