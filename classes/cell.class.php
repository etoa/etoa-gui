<?PHP

	class Cell
	{
		private $x;
		private $y;
		private $sectorX;
		private $sectorY;
		private $absX;
		private $absY;
		private $id;
		
		/**
		* Constructor
		* The constructor expects a set of cell coordinates.
		* These can be given as
		* - A single cell id
		* - An array of coordinates (4 items for sector x & y and cell x & y, or two items of absolute coorinates)
		* - A planet object
		* 
		*
		* @param mixed Cell-Coordinades
		*/
		function Cell(&$data)
		{			
			if (is_array($data))
			{
				if (count($data)==4)
				{
					$this->sectorX = intval($data[0]);
					$this->sectorY = intval($data[1]);
					$this->x = intval($data[2]);
					$this->y = intval($data[3]);
					$this->setAbs();
				}
				elseif (count($data)==2)
				{
					$this->absX = $data[0];
					$this->absY = $data[1];
					$this->sectorX = floor($this->absX / CELL_NUM_X)+1;
					$this->sectorY = floor($this->absY / CELL_NUM_Y)+1;
					$this->x = $this->absX % CELL_NUM_X;
					$this->y = $this->absY % CELL_NUM_Y;
				}				
				elseif (count($data)==1)
				{
					$res = dbquery("SELECT 
						cell_sx,
						cell_sy,
						cell_cx,
						cell_cy 
					FROM 
						space_cells 
					WHERE 
						cell_id=".intval($data[0]).";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_row($res);
						$this->id = $data[0];
						$this->sectorX = intval($arr[0]);
						$this->sectorY = intval($arr[1]);
						$this->x = intval($arr[2]);
						$this->y = intval($arr[3]);
						$this->setAbs();
					}
					else
					{
						throw new Exception("Ungültige Zellen-ID ".$data[0]."!");
					}
				}
				else
				{
					throw new Exception("Falsche Anzahl (".count($data).") an Argumenten für Zelle!");
				}
				if ($this->absX < 1 || $this->absY < 1 || $this->x < 1 || $this->y < 1 || $this->sectorX < 1 || $this->sectorY < 1)
				{
					throw new Exception("Ungültige Koordinaten ".$this."!");
				}
			}
			elseif (get_class($data)=="Planet")
			{
				$this->sectorX = $data->sx;
				$this->sectorY = $data->sy;
				$this->x = $data->cx;
				$this->y = $data->cy;
				$this->setAbs();
			}
			else
			{
				$id=intval($data);
				if ($id>0)
				{
					$res = dbquery("SELECT 
						cell_sx,
						cell_sy,
						cell_cx,
						cell_cy 
					FROM 
						space_cells 
					WHERE 
						cell_id=".intval($data).";");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_row($res);
						$this->id = $data;
						$this->sectorX = intval($arr[0]);
						$this->sectorY = intval($arr[1]);
						$this->x = intval($arr[2]);
						$this->y = intval($arr[3]);
						$this->setAbs();
					}
					else
					{
						throw new Exception("Ungültige Zellen-ID ".$data."!");
					}					
				}
				else
				{
					throw new Exception("Ungültige Zellen-ID $id!");
				}				
			}			
		}
		
		/**
		* Sets the absolute coordinates
		*/
		private function setAbs()
		{
			$this->absX = (($this->sectorX-1) * CELL_NUM_X) + $this->x;
			$this->absY = (($this->sectorY-1) * CELL_NUM_Y) + $this->y;						
		}
		
		/**
		* toString method which returns the formated cell coordinates
		*/
		public function __toString()
		{
			return $this->sectorX."/".$this->sectorY." : ".$this->x."/".$this->y;
		}
		
		/**
		* Returns a formated string of absolute cell coordinates
		*/
		function absString()
		{
			return $this->absX."/".$this->absY;
		}		
		
		function distance(Cell $cell)
		{
			$dx = abs($cell->absX - $this->absX);	// Get difference on x axis in absolute coordinates
			$dy = abs($cell->absY - $this->absY); // Get difference on y axis in absolute coordinates
			$sd = sqrt(pow($dx,2)+pow($dy,2));		// Use Pythagorean theorem to get the absolute length
			$sae = $sd * CELL_LENGTH;							// Multiply with AE units per cell
			return $sae;			
		}
	}

?>