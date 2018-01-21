<?PHP

	class BuildingQueue
	{
		private $jobs = null;
		private $entityId;
		private $ownerId;

        public function __construct($entityId,$ownerId)
		{
			$this->entityId = $entityId;
			$this->ownerId = $ownerId;
		}


		private function load()
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
				while ($arr = mysql_fetch_row($res))
				{
					$lvlp = 1;
					// Count what the target level of this job is
					foreach ($this->jobs as $v)
					{
						if ($v[0]==$arr[1])
							$lvlp++;
					}
					$this->jobs[$arr[0]] = array($arr[1],$arr[2],$arr[3],$lvlp);
				}
			}
		}

		function & getJobs($itemId=0)
		{
			if ($this->jobs==null)
				$this->load();
			if ($itemId>0)
			{
				$rtn = array();
				foreach ($this->jobs as $v)
				{
					if ($v[0]==$itemId)
						$rtn[] = $v;
				}
				return $rtn;
			}
			return $this->jobs;
		}

		function activeJob($itemId = 0)
		{
			$jb = $this->getJobs($itemId);
			if (count($jb)>0 )
			{
				foreach ($jb as $jv)
				{
					if ($jv[1] < time() && $jv[2] > time())
					{
						return $jv;
					}
				}
			}
			return false;
		}

		function cancelActiveJob()
		{
			if ($this->jobs==null)
				$this->load();
			foreach ($this->jobs as $jk => $jv)
			{
				if ($jv[1] < time() && $jv[2] > time())
				{
					unset($this->jobs[$jk]);
					dbquery("
					DELETE FROM	
						building_queue
					WHERE
						id=".$jk.";					
					");
					return true;
				}
			}
			return false;
		}


	}

?>
