<?PHP

	class Technology
	{
		public $name;

        public function __construct($id)
		{
			$res = dbquery("
			SELECT
				*
			FROM
				technologies
			WHERE
				tech_id='".intval($id)."'
			LIMIT 1");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->name = $arr['tech_name'];
			}
		}

		function __toString()
		{
			return $this->name;
		}

		static function getItems($type=0,$show=1)
		{
			$res = dbquery("
			SELECT
				*
			FROM
				technologies
			WHERE
				1
				".($show==1 ? " AND tech_show=1" : "")."
				".($type>0 ? " AND tech_type_id=".$type."" : "")."
			ORDER BY
				tech_order
			;");
			$rtn=array();
			while($arr = mysql_fetch_assoc($res))
			{
				$rtn[$arr['tech_id']] = new Technology($arr['tech_id']);
			}
			return $rtn;
		}


	}

?>
