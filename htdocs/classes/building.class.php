<?PHP

	class Building
	{
		private $id;
		private $name = "";
		private $typeId = 0;
		private $fields = 0, $maxLevel = 0;

		private $costs = array();
		private $costsFactor, $prodFactor, $demolishCostsFactor, $storeFactor;
		private $bunkerRes, $bunkerFleetCount, $bunkerFleetSpace;
		private $shortDesc, $longDesc;

		private $bRequirements = null;
		private $tRequirements = null;

		private $isValid = false;

        public function __construct($id, $small=false)
		{
			try
			{
				if (is_array($id))
				{
					$arr = $id;
				}
				else
				{
					if ($small)
						$select = " building_id, building_type, building_name ";
					else
						$select = " * ";

					$res = dbquery("
					SELECT
						".$select."
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
					}
				}

				$this->id = $arr['building_id'];
				$this->typeId = $arr['building_type_id'];
				$this->name = $arr['building_name'];

				if (!$small)
				{
					$this->shortDesc = $arr['building_shortcomment'];
					$this->longDesc = $arr['building_longcomment'];
					$this->fields = $arr['building_fields'];
					$this->maxLevel = $arr['building_last_level'];

					$this->costs[0] = $arr['building_costs_metal'];
					$this->costs[1] = $arr['building_costs_crystal'];
					$this->costs[2] = $arr['building_costs_plastic'];
					$this->costs[3] = $arr['building_costs_fuel'];
					$this->costs[4] = $arr['building_costs_food'];
					$this->costs[5] = $arr['building_costs_power'];
					$this->costsFactor = $arr['building_build_costs_factor'];
					$this->demolishCostsFactor = $arr['building_demolish_costs_factor'];
					$this->storeFactor = $arr['building_store_factor'];
          $this->prodFactor = $arr['building_production_factor'];

					$this->bunkerRes = $arr['building_bunker_res'];
					$this->bunkerFleetCount = $arr['building_bunker_fleet_count'];
					$this->bunkerFleetSpace = $arr['building_bunker_fleet_space'];
				}

				$this->isValid = true;

			}
			catch (Exception $e)
			{
				echo $e;
				return;
			}
		}

		function isValid() {return $this->isValid;}

		function __toString()
		{
			return $this->name;
		}

		public function __set($key, $val)
		{
			try
			{
				throw new EException("Properties der Klasse ".__CLASS__." sind read-only!");
				/*
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in der Klasse ".__CLASS__);
				$this->$key = $val;*/
			}
			catch (EException $e)
			{
				echo $e;
			}
		}

		public function __get($key)
		{
			try
			{
				if (!property_exists($this,$key))
					throw new EException("Property $key existiert nicht in ".__CLASS__);


				return $this->$key;
			}
			catch (EException $e)
			{
				echo $e;
				return null;
			}
		}

		function imgPathSmall()
		{
			return IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$this->id."_small.".IMAGE_EXT;
		}

		function imgPathMiddle()
		{
			return IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$this->id."_middle.".IMAGE_EXT;
		}

		function imgPathBig()
		{
			return IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$this->id.".".IMAGE_EXT;
		}

		function imgSmall()
		{
			return "<img src=\"".$this->imgPathSmall()."\" style=\"width:40px;height:40px;\"/>";
		}

		function imgMiddle()
		{
			return "<img src=\"".$this->imgPathMiddle()."\" style=\"width:120px;height:120px;\"/>";
		}

		function imgBig()
		{
			return "<img src=\"".$this->imgPathBig()."\" style=\"width:220px;height:220px;\"/>";
		}

		function getCosts($level=1)
		{
			$level = max(1,$level);
			$bc=array();
			for ($i=1;$i<=6;$i++)
      	$bc[$i] = $this->costs[$i] * pow($this->costsFactor,$level);
      return $bc;
		}

		private function loadRequirements()
		{
			$this->bRequirements = array();
			$this->tRequirements = array();
			$res = dbquery("
			SELECT
				req_building_id,
				req_tech_id,
				req_level
			FROM
				building_requirements
			WHERE
				obj_id=".$this->id."
			");
			while ($arr = mysql_fetch_row($res))
			{
				if ($arr[1]>0)
					$this->tRequirements[$arr[1]] = $arr[2];
				if ($arr[0]>0)
					$this->bRequirements[$arr[0]] = $arr[2];
			}
		}

		function getBuildingRequirements()
		{
			if ($this->bRequirements != null)
				return $this->bRequirements;
			$this->loadRequirements();
			return $this->bRequirements;
		}

		function getTechRequirements()
		{
			if ($this->tRequirements != null)
				return $this->tRequirements;
			$this->loadRequirements();
			return $this->tRequirements;
		}

		//
		// Statics
		//

		static function getItems($type=0,$show=1)
		{
			$res = dbquery("
			SELECT
				*
			FROM
				buildings
			WHERE
				1
				".($show==1 ? " AND building_show=1" : "")."
				".($type>0 ? " AND building_type_id=".$type."" : "")."
			ORDER BY
				building_order
			;");
			$rtn=array();
			while($arr = mysql_fetch_assoc($res))
			{
				$rtn[$arr['building_id']] = new Building($arr);
			}
			return $rtn;
		}

		static function getBuildTypes()
		{
			return [
				0 => "Untätig",
				1 => "Bau eingefroren",
				2 => "Abriss eingefroren",
				3 => "Wird ausgebaut",
				4 => "Wird abgerissen"
			];
		}
	}

?>
