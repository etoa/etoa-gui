<?PHP

	class BuildList implements IteratorAggregate
	{
		private $entityId;
		private $ownerId;

		private $entity;
		private $owner;

		/** @var BuildListItem[] */
		private $items = null;
		private $count = null;
		public static $underConstruction = false;

		private $tmpItems = array();

		private $jobs = null;

		private $totalPeopleWorking = null;

		/** @var TechList */
		public $tl = null;

		private $errorMsg;

		public static $GENTECH = 0;

		/**
		 * Constructor
		 * @param <type> $entityId
		 * @param <type> $ownerId
		 * @param <type> $load
		*
		* @access public
		 */
        public function __construct($entityId,$ownerId,$load=0)
		{
			$this->entityId = $entityId;
			$this->ownerId = $ownerId;
			if ($load>0)
				$this->load($load);
		}

		/**
		*  Returns an Iterator with every element in the buildlist,
		* to specify the selection use the $load param in the Constructor
		*
		* @return ArrayIterator with key() building_id and current() buildlistitem
		*
		* @access public
		*/
		public function getIterator()
		{
			if ($this->items == null)
				$this->load();
			return new ArrayIterator($this->items);
		}

		/**
		*  Returns an ArrayIterator with every element in the selected category,
		* use the $mode param to specify the returned buildings aswell as the $load param in the Constructor
		*
		* @param unsigned int $catId
	 	* @param string $mode {all | buildable | resable}
		*
		* @return ArrayIterator	with key() building_id and current() buildlistitem
		*
		* @access public
		*/
		public function getCatIterator($catId=0, $mode='all')
		{
			if ($this->items == null)
				$this->load();
			$catItems = array();

			foreach ($this->items as $id=>$item)
			{
				if ($item->building->typeId == $catId)
				{
					$add = true;
					if ($mode == 'buildable')
					{
						if (!$this->requirementsPassed($id) || $item->isMaxLevel())
							$add = false;
					}
					elseif ($mode == 'resable')
					{
						if (!($this->checkBuildable($id,false) == 1))
							$add = false;
					}
					if ($add)
						$catItems[$id] = $item;
				}
			}
			return new ArrayIterator($catItems);
		}

		private function load($load=1)
		{
			$this->tl = new TechList($this->ownerId);
			self::$GENTECH = $this->tl->getLevel(GEN_TECH_ID);
			$this->items = array();
			$this->count = 0;

			if ($load==3)
			{
				$sql = "SELECT
							l.*,
							i.*
						FROM
							buildlist l
						INNER JOIN
							buildings i
						ON
							l.buildlist_building_id = i.building_id
							AND l.buildlist_entity_id='".$this->entityId."'
							AND l.buildlist_current_level>='0'
						ORDER BY
							i.building_order,
							i.building_name;";
			}
			elseif ($load==2)
			{
				$sql = "SELECT
							l.*,
							i.*
						FROM
							buildings i
						LEFT JOIN
							buildlist l
						ON
							l.buildlist_building_id = i.building_id
							AND l.buildlist_entity_id='".$this->entityId."'
						WHERE i.building_show='1'
						ORDER BY
							i.building_order,
							i.building_name;";
			}
			else //this is for the cases $load==0 and $load==1
			{
				$sql = "SELECT
							l.*,
							i.*
						FROM
							buildlist l
						INNER JOIN
							buildings i
						ON
							l.buildlist_building_id = i.building_id
							AND l.buildlist_entity_id='".$this->entityId."'
							AND l.buildlist_current_level>'0'
						ORDER BY
							i.building_order,
							i.building_name;";
			}
			$res = dbquery($sql);

			if (mysql_num_rows($res)>0)
			{
				while ($arr = mysql_fetch_assoc($res))
				{
					$this->items[$arr['building_id']] = new BuildListItem($arr);
					$this->count++;

					if (($arr['buildlist_build_type']==3 || $arr['buildlist_build_type']==4) && $arr['buildlist_build_end_time']>time())
					{
						self::$underConstruction = true;
					}
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
				COUNT(buildlist_id)
			FROM
				buildlist
			WHERE
				buildlist_user_id=".$this->ownerId ."
				AND buildlist_entity_id=".$this->entityId."
			;");
			$arr = mysql_fetch_row($res);
			$this->count = $arr[0];
			return $this->count;
		}

		function item($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
				return $this->items[$bid];
			if (isset($this->tmpItems[$bid]))
				return $this->tmpItems[$bid];
			return false;
		}

		function isUnderConstruction()
		{
			if (!isset(self::$underConstruction))
				$this->load();
			return self::$underConstruction;
		}

		function getLevel($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
				return $this->items[$bid]->level;
			return 0;
		}

		function getStatus($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
				return $this->items[$bid]->buildType;
			return 0;
		}

		function getDeactivated($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
			{
				if ($this->items[$bid]->deactivated > time()) {
					 return $this->items[$bid]->deactivated;
				}
			}
			return false;
		}

		function getPeopleWorking($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
			{
				return $this->items[$bid]->peopleWorking;
			}
			return 0;
		}

		// use only for tech and buildings
		function setPeopleWorking($bid,$people,$tech=false)
		{
			if ($this->items==null)
				$this->load();

			// BUGFIX: if first part is false, check for $tech in second part!

			if ((!$tech && !$this->isUnderConstruction($bid)) || ($tech))
			{
				if (isset($this->items[$bid]))
				{
					global $cp;
					// Free: Total people on planet minus total working people on planet
					// PLUS people working in this building (these can be set again)
					$free = $cp->people - $this->totalPeopleWorking() + $this->items[$bid]->peopleWorking;
					if ($free >= $people)
					{
						return $this->items[$bid]->setPeopleWorking($people, $tech);
					}
				}
			}
			return false;
		}

		function getCooldown($bid)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
			{
				if ($this->items[$bid]->cooldown > time())
					return $this->items[$bid]->cooldown;
			}
			return false;
		}

		function setCooldown($bid,$cd)
		{
			if ($this->items==null)
				$this->load();
			if (isset($this->items[$bid]))
			{
				$this->items[$bid]->cooldown = $cd;
			}
		}

		function totalPeopleWorking()
		{
			if ($this->totalPeopleWorking>-1)

				return $this->totalPeopleWorking;
			if ($this->items!=null)
			{
				$this->totalPeopleWorking = 0;
				foreach ($this->items as $k=>&$v)
				{
					$this->totalPeopleWorking+=$v->peopleWorking;
				}
				unset($v);
				return $this->totalPeopleWorking;
			}

			$res = dbquery("SELECT
								SUM(buildlist_people_working)
							FROM
								buildlist
							WHERE
								buildlist_entity_id='".$this->entityId."';");
			$pbarr = mysql_fetch_row($res);

			$this->totalPeopleWorking = $pbarr[0];

			return $this->totalPeopleWorking;
		}

		function getBunkerRes()
		{
			if ($this->items==null)
				$this->load();
			$this->bunkerRes= 0;
			foreach ($this->items as $k=>&$v)
			{
				$this->bunkerRes+= $v->building->bunkerRes * pow($v->building->storeFactor,$v->level-1);
			}
			return $this->bunkerRes;
		}

		function getBunkerFleetCount()
		{
			if ($this->items==null)
				$this->load();
			$this->bunkerFleetCount= 0;
			foreach ($this->items as $k=>&$v)
			{
				$this->bunkerFleetCount+= $v->building->bunkerFleetCount * pow($v->building->storeFactor,$v->level-1);
			}
			return $this->bunkerFleetCount;
		}

		function getBunkerFleetSpace()
		{
			if ($this->items==null)
				$this->load();
			$this->bunkerFleetSpace= 0;
			foreach ($this->items as $k=>&$v)
			{
				$this->bunkerFleetSpace+= $v->building->bunkerFleetSpace * pow($v->building->storeFactor,$v->level-1);
			}
			return $this->bunkerFleetSpace;
		}

		function getCosts($bid,$type='build',$levelUp=0)
		{
			if ($type=='build')
			{
				return $this->items[$bid]->getBuildCosts($levelUp);
			}
			else
			{
				return $this->items[$bid]->getDemolishCosts($levelUp);
			}
		}

		function build($bid)
		{
			if ($this->checkBuildable($bid)>0)
			{
				if (isset($this->items[$bid]))
				{
					$this->errorMsg =  $this->items[$bid]->build();
					if ($this->errorMsg=="")
						return true;
					else
						return false;
				}
			}
			$this->errorMsg = "Geb&auml;de nicht baubar!";
			return false;
		}

		function demolish($bid)
		{
			if ($this->checkDemolishable($bid))
			{
				$this->errorMsg =  $this->items[$bid]->demolish();
				if ($this->errorMsg=="")
					return true;
				else
					return false;
			}
			$this->errorMsg = "Geb&auml;de nicht abreissbar!";
			return false;
		}

		function cancelBuild($bid)
		{
			if (isset($this->items[$bid]))
			{
				$this->errorMsg =  $this->items[$bid]->cancelBuild();
				if ($this->errorMsg=="")
					return true;
				else
					return false;
			}
			$this->errorMsg = "Geb&aauml;de nicht vorhanden!";
			return false;
		}

		function cancelDemolish($bid)
		{
			if (isset($this->items[$bid]))
			{
				$this->errorMsg =  $this->items[$bid]->cancelDemolish();
				if ($this->errorMsg=="")
					return true;
				else
					return false;
			}
			$this->errorMsg = "Geb&aauml;de nicht vorhanden!";
			return false;
		}

		/**
		* Check wether an item is buildable. Conditions are
		* no building under construction, enough resources, not maxed out level, enough fieldsUsed,
		* and satisfied prerequisites.
		*
		*
		*	@return <int> 1=buildable,0=not buildable but show resbox, -1= not buildable & no res box
		*/
		function checkBuildable($bid, $uncheckConstruction=false)
		{
			if (!isset($this->items[$bid]->buildableStatus))
			{
				// check all the buildings
				if (!$this->isUnderConstruction() || $uncheckConstruction)
				{
					global $cu,$cp;
					if ($this->entity == null)
					{
						if ($cp->id != $this->entityId)
							$this->entity = Entity::createFactoryById($this->entityId);
						else
							$this->entity = &$cp;
					}

					// check max level
					if (!$this->items[$bid]->isMaxLevel())
					{
						$cst = $this->items[$bid]->getBuildCosts();
						// Check costs
						if ($cst['costs0'] <= $this->entity->getRes1(0)
							&& $cst['costs1'] <= $this->entity->getRes1(1)
							&& $cst['costs2'] <= $this->entity->getRes1(2)
							&& $cst['costs3'] <= $this->entity->getRes1(3)
							&& $cst['costs4'] <= $this->entity->getRes1(4))
						{
							// check fields
							if ($this->items[$bid]->building->fields==0 || $cp->fields_used+$this->items[$bid]->building->fields <= $cp->fields+$cp->fields_extra)
							{
								if ($this->requirementsPassed($bid))
									$this->items[$bid]->buildableStatus = 1;
								else
								{
									$this->errorMsg = 'Voraussetzungen nicht erf&uuml;llt!';
									$this->items[$bid]->buildableStatus = -1;
								}
							}
							else
							{
								$this->errorMsg = 'Nicht gen&uuml;gend Felder vorhanden!';
								$this->items[$bid]->buildableStatus = 0;
							}
						}
						else
						{
							$this->errorMsg = 'Zuwenig Rohstoffe vorhanden!';
							$this->items[$bid]->buildableStatus = 0;
						}
					}
					else
					{
						$this->errorMsg = 'Maximalstufe erreicht! Kein weiterer Ausbau m&ouml;glich!';
						$this->items[$bid]->buildableStatus = -1;
					}
				}
				else
				{
					$this->errorMsg = 'Es wird gerade an einem Geb&auml;ude gebaut!';
					$this->items[$bid]->buildableStatus = 0;
				}
			}
			return $this->items[$bid]->buildableStatus;
		}

		/**
		* Check wether an item is demolishable. Conditions are
		* no building under construction and enough resources.
		*/
		function checkDemolishable($bid)
		{
			// check all the buildings
            $this->load(3);

            if(!$this->getDeactivated($bid)) {
                if (!$this->isUnderConstruction())
                {
                    global $cu,$cp;
                    if ($this->entity == null)
                    {
                        if ($cp->id != $this->entityId)
                            $this->entity = Entity::createFactoryById($this->entityId);
                        else
                            $this->entity = &$cp;
                    }

                    $cst = $this->items[$bid]->getDemolishCosts();
                    // Check costs
                    if ($cst['costs0'] <= $this->entity->getRes1(0)
                        && $cst['costs1'] <= $this->entity->getRes1(1)
                        && $cst['costs2'] <= $this->entity->getRes1(2)
                        && $cst['costs3'] <= $this->entity->getRes1(3)
                        && $cst['costs4'] <= $this->entity->getRes1(4))
                    {
                        return true;
                    }
                    else
                        $this->errorMsg = "Zuwenig Rohstoffe vorhanden!";
                }
                else
                    $this->errorMsg = "Es wird gerade an einem Geb&auml;ude gebaut!";
                return false;
			}
			else {
                $this->errorMsg = "Das Geb&auml;ude wurde deaktiviert!";
			}
		}

		public function requirementsPassed($bid=0)
		{
			if (isset($this->items[$bid]))
			{
				$req = $this->items[$bid]->building->getBuildingRequirements();
				foreach ($req as $rk=>$rv)
				{
					if (isset($this->items[$rk]) && $rv> $this->items[$rk]->level)
					{
						return false;
					}
				}
				$req = $this->items[$bid]->building->getTechRequirements();
				foreach ($req as $rk=>$rv)
				{
					if ($rv > $this->tl->getLevel($rk))
					{
						return false;
					}
				}
				return true;
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
		* Adds a construction job for the given item
		*
		* @param int Item-ID
		*/
		function addJob($itemId)
		{
			global $cp,$cu;
			if ($this->owner == null)
			{
				if ($cu->id != $this->ownerId)
					$this->owner = new User($this->ownerId);
				else
					$this->owner = &$cu;
			}
			if ($this->entity == null)
			{
				if ($cp->id != $this->entityId)
					$this->entity = Entity::createFactoryById($this->entityId);
				else
					$this->entity = &$cp;
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
					$itm = new Building($itemId);
					if (!$itm->isValid())
						return false;
					$lvl = 1;
				}
				if ($this->jobs==null)
					$this->loadJobs();
				$jobs = $this->getJobs($itemId);
				$jobcount = count($jobs);
				$lvl += $jobcount;

				$cst = $itm->getCosts($lvl);

				$this->entity->changeRes(-$cst[1],-$cst[2],-$cst[3],-$cst[4],-$cst[5]);

				$t = time();
				$startTime = $t;
				$jobs = $this->getJobs();
				$jobcount = count($jobs);
				if ($jobcount>0)
				{
					foreach ($jobs as $jv)
					{
						$startTime = max($t,$jv['timeend']);
					}
					$startTime += BUILDING_QUEUE_DELAY;
				}
				$endTime = $startTime + $this->getBuildTime($itemId,$lvl);

				dbquery("
				INSERT INTO
					building_queue
				(
					user_id,
					entity_id,
					item_id,
					time_start,
					time_end,
					res_metal,
					res_crystal,
					res_plastic,
					res_fuel,
					res_food,
					targetlevel
				)
				VALUES
				(
					".$this->ownerId.",
					".$this->entityId.",
					".$itemId.",
					".$startTime.",
					".$endTime.",
					".$cst[1].",
					".$cst[2].",
					".$cst[3].",
					".$cst[4].",
					".$cst[5].",
					".$lvl."
				);");
				$this->jobs[mysql_insert_id()] = array(
				"item"=>$itemId,
				"timestart"=>$startTime,
				"timeend"=>$endTime,
				"targetlevel"=>$lvl);

				return true;
			}
			return false;
		}

		/**
		* Loads all job items from the database
		* and stores them in the $jobs array
		*/
		private function loadJobs()
		{
			$this->jobs = array();
			$res = dbquery("
			SELECT
				id,
				item_id,
				time_start,
				time_end
			FROM
				building_queue
			WHERE
				entity_id=".$this->entityId."
				AND user_id=".$this->ownerId."
			ORDER BY
				time_start
			");
			if (mysql_num_rows($res)>0)
			{
				while ($arr = mysql_fetch_assoc($res))
				{
					$lvlp = 1 + $this->getLevel($arr['item_id']);
					// Count what the target level of this job is
					foreach ($this->jobs as &$v)
					{
						if ($v['item']==$arr['item_id'])
							$lvlp++;
					}
					$this->jobs[$arr['id']] = array(
						"item"=> $arr['item_id'],
						"timestart"=> $arr['time_start'],
						"timeend"=> $arr['time_end'],
						"targetlevel"=> $lvlp
					);
				}
			}
		}

		/**
		* Returns all jobs (of a given item)
		*
		* @param int Item-ID
		*/
		function & getJobs($itemId=0)
		{
			if ($this->jobs==null)
				$this->loadJobs();
			if ($itemId>0)
			{
				$rtn = array();
				foreach ($this->jobs as $k=>$v)
				{
					if ($v['item']==$itemId)
						$rtn[$k] = $v;
				}
				return $rtn;
			}
			return $this->jobs;
		}

		/**
		* Returns the currently active job item
		*
		* @param int Item-ID (Optional)
		*/
		function activeJob($itemId = 0)
		{
			$jb = $this->getJobs($itemId);
			if (count($jb)>0 )
			{
				foreach ($jb as $jv)
				{
					if ($jv['timestart'] < time() && $jv['timeend'] > time())
					{
						return $jv;
					}
				}
			}
			return false;
		}

		/**
		* Cancel all pending jobs of a given
		* item.
		*
		* @param int Item-ID
		*/
		function cancelJobsByItemId($itemId)
		{
			$itemJobs = $this->getJobs($itemId);
			if (count($itemJobs)>0)
			{
				$t = time();
				$kc = 0;
				foreach ($itemJobs as $jid => &$jb)
				{
					// Make sure we kill only jobs who haven't finished
					if ($jb['timeend'] > $t)
					{
						$this->cancelJobById($jid);
						$kc++;
					}
				}
				if ($kc>0)
					return $kc;
			}
			return false;
		}

		/**
		* Cancels a given job and returns
		* a part of resources if time is not up
		*
		* @param int Job-ID
		*/
		function cancelJobById($jid)
		{
			global $cp;
			if (isset($this->jobs[$jid]))
			{
				$res = dbquery("
				SELECT
					res_metal,
					res_crystal,
					res_plastic,
					res_fuel,
					res_food
				FROM
					building_queue
				WHERE
					id=".$jid.";
				");
				$arr = mysql_fetch_row($res);
				$t = time();
				if ($this->jobs[$jid]['timeend'] <= $t)
					$cashBack = 0;
				elseif ($this->jobs[$jid]['timestart'] >= $t)
					$cashBack = 1;
				else
					$cashBack = ($t - $this->jobs[$jid]['timestart']) / ($this->jobs[$jid]['timeend']-$this->jobs[$jid]['timestart']);

				if ($this->entity == null)
				{
					if ($cp->id != $this->entityId)
						$this->entity = Entity::createFactoryById($this->entityId);
					else
						$this->entity = &$cp;
				}
				if ($cashBack > 0)
					$this->entity->changeRes($arr[0]*$cashBack,$arr[1]*$cashBack,$arr[2]*$cashBack,$arr[3]*$cashBack,$arr[4]*$cashBack);

				dbquery("
				DELETE FROM
					building_queue
				WHERE
					id=".$jid.";
				");
				unset($this->jobs[$jid]);
				return true;
			}
			return false;
		}
	}

?>
