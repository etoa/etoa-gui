<?PHP

	class Building
	{
		public $name;
		public $fields;
		
		function Building($id)
		{
			if (is_array($id))
			{
				$arr = $id;
			}
			else
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
					$arr = mysql_fetch_assoc($res);
				else
				{
					throw new EException("Gebäude $id existiert nicht!");
					return;
				}
			}
			
			$this->name = $arr['building_name'];
			$this->fieldsUsed = $arr['building_fields'];
			
		}
		
		function __toString()
		{
			return $this->name;
		}
		
		
	}

?>