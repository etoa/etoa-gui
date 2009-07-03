<?PHP

	class AllianceTechlist implements IteratorAggregate
	{
		private $allianceId;

		private $alliance;

		private $items = null;
		private $itemStatus = null;	
		private $count = null;
		
		private $tmpItems = array();
		
		private $jobs = null;
		
		private $errorMsg;

		/**
		 * Constructor
		 * @param <type> $allianceId
		 * @param <type> $load
		 * @param <type> $alliance
		 */
		function AllianceTechlist($allianceId,$load=0,&$alliance=null)
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
				l.alliance_techlist_id,
				l.alliance_techlist_current_level,
				l.alliance_techlist_build_start_time,
				l.alliance_techlist_build_end_time,
				l.alliance_techlist_member_for,
				i.*
			FROM 
				alliance_technologies i
			LEFT JOIN
				alliance_techlist l
			ON
				l.alliance_techlist_tech_id = i.alliance_tech_id
				AND l.alliance_techlist_alliance_id='".$this->allianceId."'
				;");
			if (mysql_num_rows($res)>0)
			{		
				while ($arr = mysql_fetch_assoc($res))
				{
					$this->items[$arr['alliance_tech_id']] = new AllianceTechnology($arr);
					$this->itemStatus[$arr['alliance_tech_id']] = array(
						'listid' => $arr['alliance_techlist_id'],
						'level' => $arr['alliance_techlist_current_level'],
						'member_for' => $arr['alliance_techlist_member_for'],
						'start_time' => $arr['alliance_techlist_build_start_time'],
						'end_time' => $arr['alliance_techlist_build_end_time']
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
				COUNT(alliance_techlist_id)
			FROM
				alliance_techlist
			WHERE
				alliance_techlist_alliance_id=".$this->allianceId."
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
		
		function getMemberFor($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
				return $this->itemStatus[$bid]['member_for'];
			return 0;
		}
			
		function getLevel($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->itemStatus[$bid]))
				return $this->itemStatus[$bid]['level'];
			return 0;
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
				$itm = new AllianceTechnology($itemId);
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
				$itm = new AllianceTechnology($itemId);
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
		* @param int Item-ID
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
					$itm = new AllianceTechnology($itemId);
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
								`alliance_techlist`
							(
							 	`alliance_techlist_alliance_id`,
								`alliance_techlist_tech_id`,
								`alliance_techlist_current_level`,
								`alliance_techlist_build_start_time`,
								`alliance_techlist_build_end_time`,
								`alliance_techlist_member_for`
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
								`alliance_techlist`
							SET
								`alliance_techlist_build_start_time`='".$startTime."',
								`alliance_techlist_build_end_time`='".$endTime."',
								`alliance_techlist_member_for`='".$this->alliance->memberCount."'
							WHERE
							 	`alliance_techlist_alliance_id`='".$this->allianceId."'
								AND `alliance_techlist_tech_id`='".$itemId."'
							LIMIT 1;
							");
				}
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
						throw new EException("Forschung $itemId existiert nicht!");
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
					throw new EException("Forschung $itemId existiert nicht!");
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
					$req = $this->items[$itemId]->getTechRequirements();
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