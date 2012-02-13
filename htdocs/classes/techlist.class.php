<?PHP

	class TechList
	{
		private $userId;
		private $techs;
		
		
		function TechList($userId)
		{
			$this->userId = $userId;
			$this->techs = array();
		}
	
		private function load($bid)
		{
			if (!isset($this->techs[$bid]))
			{
				$res = dbquery("
				SELECT				
					techlist_current_level
				FROM 
					techlist 
				WHERE 
					techlist_tech_id=".$bid." 
					AND techlist_user_id='".$this->userId."';");
				if (mysql_num_rows($res)>0)
				{		
					$arr = mysql_fetch_row($res);
					$this->techs[$bid] = array(
						'level' => $arr[0]					);
				}			
				else
				{
					$this->techs[$bid] = false;
				}			
			}		
		}
								
			
		function getLevel($bid)
		{
			$level = 0;
			$this->load($bid);
			if ($this->techs[$bid])
			{
				$level = $this->techs[$bid]['level'];
			}
			return $level;	
		}	

	}

?>