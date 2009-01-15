<?PHP

	class Building
	{
		public $name;
		
		function Building($id)
		{
			$res = dbquery("
			SELECT 
				*
			FROM
				buildings
			WHERE
				building_id='".intval($id)."'
			LIMIT 1");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->name = $arr['building_name'];
			}
		}
		
		function __toString()
		{
			return $this->name;
		}
		
		
	}

?>