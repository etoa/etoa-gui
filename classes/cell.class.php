<?PHP

	class Cell
	{
		private $id;
		private $isValid;
		private $entities;
		
		public function Cell($id=0)
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
					type
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
		
		
		
		
		/*
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
		*
		function Cell($data)
		{			
			$this->valid = false;
			$this->typeLoaded = false;

			$this->solSys = false;
			$this->asteroid = false;
			$this->nebula = false;
			$this->wormhole = false;

			if (is_array($data))
			{
				if (count($data)==4)
				{
					$this->sectorX = intval($data[0]);
					$this->sectorY = intval($data[1]);
					$this->x = intval($data[2]);
					$this->y = intval($data[3]);
					$this->setAbs();
					$res = dbquery("SELECT 
						cell_id 
					FROM 
						space_cells 
					WHERE 
						cell_sx=".intval($this->sectorX)."
						AND cell_sy=".intval($this->sectorY)."
						AND cell_cx=".intval($this->x)."
						AND cell_cy=".intval($this->y)."
						;");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_row($res);		
						$this->id = $arr[0];			
						$this->valid = true;
					}
					else
					{
						$this->error = "Koordinaten ungültig!";
					}
				}
				elseif (count($data)==2)
				{
					$this->absX = $data[0];
					$this->absY = $data[1];
					$this->sectorX = floor($this->absX / CELL_NUM_X)+1;
					$this->sectorY = floor($this->absY / CELL_NUM_Y)+1;
					$this->x = $this->absX % CELL_NUM_X;
					$this->y = $this->absY % CELL_NUM_Y;
					$res = dbquery("SELECT 
						cell_id 
					FROM 
						space_cells 
					WHERE 
						cell_sx=".intval($this->sectorX)."
						AND cell_sy=".intval($this->sectorY)."
						AND cell_cx=".intval($this->x)."
						AND cell_cy=".intval($this->y)."
						;");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_row($res);		
						$this->id = $arr[0];			
						$this->valid = true;
					}
					else
					{
						$this->error = "Absolute Koordinaten ungültig!";
					}					
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
						$this->valid = true;
					}
					else
					{
						$this->error = "ID ungültig!";
					}					
				}
				else
				{
					error_msg("Falsche Anzahl (".count($data).") an Argumenten für Zelle!");
				}
				if ($this->absX < 1 || $this->absY < 1 || $this->x < 1 || $this->y < 1 || $this->sectorX < 1 || $this->sectorY < 1)
				{
					error_msg("Ungültige Koordinaten ".$this."!");
				}
			}
			elseif (get_class($data)=="Planet")
			{
				$this->sectorX = $data->sx;
				$this->sectorY = $data->sy;
				$this->x = $data->cx;
				$this->y = $data->cy;
				$this->setAbs();
				$this->valid = true;
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
						$this->valid = true;
					}
					else
					{
						$this->error = "ID ungültig!";
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
		*
		private function setAbs()
		{
			$this->absX = (($this->sectorX-1) * CELL_NUM_X) + $this->x;
			$this->absY = (($this->sectorY-1) * CELL_NUM_Y) + $this->y;						
		}
		
		/**
		* toString method which returns the formated cell coordinates
		*
		public function __toString()
		{
			return $this->sectorX."/".$this->sectorY." : ".$this->x."/".$this->y;
		}
		
		public function isValid()
		{
			return $this->valid;
		}
		
		public function getError()
		{
			return $this->error;
		}		
		
		/**
		* Returns a formated string of absolute cell coordinates
		*
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
		
		function loadType()
		{
			if ($this->valid && !$this->typeLoaded)
			{
				$res = dbquery("
				SELECT 
					cell_solsys_num_planets,
					cell_solsys_solsys_sol_type,
					cell_asteroid,
					cell_nebula,
					cell_wormhole_id,
					cell_solsys_name 	
				FROM
					space_cells
				WHERE
					cell_id=".$this->id."
				
				");
				$arr = mysql_fetch_row($res);
				if ($arr[0]>0 && $arr[1]>0)
				{
					$this->solSys = true;
					$this->numPlanets = $arr[0];
					$this->solType = $arr[1];
					$this->solName = $arr[5] != "" ? $arr[5] : "Unbenannter Stern";
				}
				elseif ($arr[2]==1)
				{
					$this->asteroid = true;
				}
				elseif ($arr[3]==1)
				{
					$this->nebula = true;
				}
				elseif ($arr[4]>0)
				{
					$this->wormhole = true;
					$this->wormholePartner = $arr[4];
				}	
				$this->typeLoaded = true;			
			}
		}
		
		function getType($color=0)
		{
			if (!$this->typeLoaded)
			{
				$this->loadType();
			}
			
			if ($this->solSys)
			{
				$clr = "#fd1";
				$msg = "Sonnensystem ".$this->solName." (".$this->numPlanets." Planeten)";
			}
			elseif ($this->asteroid)
			{
				$clr = "#920";
				$msg = "Asteroidenfeld";
			}			
			elseif ($this->nebula)
			{
				$clr = "#93a";
				$msg = "Nebel";
			}			
			else
			{
				$clr = "#006";
				$msg = "Unerforschter Raum";
			}
			
			if ($color>0)
			{
				return "<span style=\"color:".$clr.";\">".$msg."</span>";
			}
			return $msg;
		}*/
	}

?>