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
	}

?>
