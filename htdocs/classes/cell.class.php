<?PHP

	/**
	* Space cells class
	*
	* @author Nicolas Perrenoud <mrcage@etoa.ch>
	*/
	class Cell
	{
		private $id;
		private $isValid;
		private $entities;

    public $sx;
    public $sy;
    public $cx;
    public $cy;

        public function __construct($id=0)
		{
			$this->isValid=false;
			$this->entities=null;

			$res=dbquery("
			SELECT
	    	cells.sx,
	    	cells.sy,
	    	cells.cx,
	    	cells.cy
			FROM
	    	cells
			WHERE
			 	id='".intval($id)."';");
			if (mysql_num_rows($res))
			{
				$arr = mysql_fetch_row($res);
				$this->id=$id;
				$this->sx=$arr[0];
				$this->sy=$arr[1];
				$this->cx=$arr[2];
				$this->cy=$arr[3];
				$this->isValid=true;
			}
		}

		public function id()
		{
			return $this->id;
		}

		public function isValid()
		{
			return $this->isValid;
		}

		function getEntities()
		{
			if ($this->entities==null)
			{
				$this->entities=array();
				$res = dbquery("
				SELECT
					id,
					code
				FROM
					entities
				WHERE
					cell_id=".$this->id."
				ORDER BY
					pos
				");
				while ($arr=mysql_fetch_row($res))
				{
					$this->entities[] = Entity::createFactory($arr[1],$arr[0]);
				}
			}
			return $this->entities;
		}

		function __toString()
		{
			return $this->sx."/". $this->sy." : ". $this->cx."/". $this->cy;
		}

		function absX()
		{
			$cfg = Config::getInstance();
			$cx_num=$cfg->param1('num_of_cells');
			return (($this->sx - 1) * $cx_num) + $this->cx;
		}

		function absY()
		{
			$cfg = Config::getInstance();
			$cy_num=$cfg->param2('num_of_cells');
			return (($this->sy - 1) * $cy_num) + $this->cy;
		}

    function getSX() {
      return $this->sx;
    }

    function getSY() {
      return $this->sy;
    }

    function getCX() {
      return $this->cx;
    }

    function getCY() {
      return $this->cy;
    }

	}

?>
