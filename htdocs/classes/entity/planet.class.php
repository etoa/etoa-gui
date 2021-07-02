<?PHP

use EtoA\Core\Configuration\ConfigurationService;

/**
* Planet class
*
* @author Nicolas Perrenoud <mrcage@etoa.ch>
*/
class Planet extends Entity implements OwnableEntity
{
	protected $id;
	protected bool $isMain;
	protected $isValid;
	protected $coordsLoaded;
	private $desc;
	private $name;
	// TODO: Make protected and ad getter
	public $resources;
	protected $temp_from;
	protected $temp_to;
	protected $pos;
	protected $starTypeName;
	protected $fields;
	protected $fieldsUsed;
	protected $fieldsBase;
	protected $fieldsExtra;
	protected $debrisField;
	protected $debrisMetal;
	protected $debrisCrystal;
	protected $debrisPlastic;

	/**
	* Constructor
	* Erwartet ein Array mit dem Inhalt des MySQL-Datensatzes, oder die ID eines Planeten
	*/
    public function __construct()
	{
		$this->exploreCode = 'e';
		$this->explore = false;
		$this->isValid = false;
		$this->isVisible = true;
	}

	public static function getById($id)
	{
		$res = dbquery("
		SELECT
			planets.*,
			cells.sx,
			cells.sy,
			cells.cx,
			cells.cy,
			cells.id as cell_id,
			pentity.pos,
			planet_types.*,
			sol_types.*
		FROM
		(
			planets
			INNER JOIN
				planet_types
				ON planets.planet_type_id = planet_types.type_id
				AND planets.id='".intval($id)."'
		)
		INNER JOIN
		(
			entities AS pentity
			INNER JOIN cells
				ON cells.id = pentity.cell_id
			INNER JOIN
				entities AS sentity
				ON cells.id = sentity.cell_id
				AND sentity.pos =0
			INNER JOIN stars
				ON stars.id = sentity.id
			INNER JOIN sol_types
				ON sol_types.sol_type_id = stars.type_id
		)
		ON planets.id = pentity.id
		LIMIT 1;
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			$p = self::getByArray($arr);
			$p->isValid = true;
			return $p;
		}
		return null;
	}

	public static function getByArray($arr)
	{
		$p = new Planet();

		$p->id = $arr['id'];
		$p->cellId = $arr['cell_id'];
		$p->userId = $arr['planet_user_id'];
		$p->name = $arr['planet_name']!="" ? ($arr['planet_name']) : 'Unbenannt';
		$p->desc = $arr['planet_desc'];
		$p->image = $arr['planet_image'];
		$p->updated = $arr['planet_last_updated'];
		$p->userChanged = $arr['planet_user_changed'];
		$p->lastUserId = $arr['planet_last_user_id'];

		$p->owner = new User($arr['planet_user_id']);

		$p->sx = $arr['sx'];
		$p->sy = $arr['sy'];
		$p->cx = $arr['cx'];
		$p->cy = $arr['cy'];
		$p->pos = $arr['pos'];

		$p->typeId = $arr['type_id'];
		$p->typeName = $arr['type_name'];

		$p->habitable = (boolean)$arr['type_habitable'];
		$p->collectGas = (boolean)$arr['type_collect_gas'];

		$p->typeMetal = $arr['type_f_metal'];
		$p->typeCrystal = $arr['type_f_crystal'];
		$p->typePlastic = $arr['type_f_plastic'];
		$p->typeFuel = $arr['type_f_fuel'];
		$p->typeFood = $arr['type_f_food'];
		$p->typePower = $arr['type_f_power'];
		$p->typePopulation = $arr['type_f_population'];
		$p->typeResearchtime = $arr['type_f_researchtime'];
		$p->typeBuildtime = $arr['type_f_buildtime'];

		$p->starTypeId = $arr['sol_type_id'];
		$p->starTypeName = $arr['sol_type_name'];
		$p->starMetal = $arr['sol_type_f_metal'];
		$p->starCrystal = $arr['sol_type_f_crystal'];
		$p->starPlastic = $arr['sol_type_f_plastic'];
		$p->starFuel = $arr['sol_type_f_fuel'];
		$p->starFood = $arr['sol_type_f_food'];
		$p->starPower = $arr['sol_type_f_power'];
		$p->starPopulation = $arr['sol_type_f_population'];
		$p->starResearchtime = $arr['sol_type_f_researchtime'];
		$p->starBuildtime = $arr['sol_type_f_buildtime'];

		$p->debrisMetal = $arr['planet_wf_metal'];
		$p->debrisCrystal = $arr['planet_wf_crystal'];
		$p->debrisPlastic = $arr['planet_wf_plastic'];

		$p->debrisField = ($p->debrisMetal + $p->debrisCrystal + $p->debrisPlastic > 0);

		$p->fieldsBase = $arr['planet_fields'];
		$p->fieldsExtra = $arr['planet_fields_extra'];
		$p->fieldsUsed = $arr['planet_fields_used'];

		$p->fields_extra = $arr['planet_fields_extra'];
		$p->fields_used = $arr['planet_fields_used'];

		$p->fields = $p->fieldsBase + $p->fieldsExtra;

		$p->temp_from = $arr['planet_temp_from'];
		$p->temp_to = $arr['planet_temp_to'];
		$p->people = zeroPlus($arr['planet_people']);
		$p->people_place = zeroPlus($arr['planet_people_place']);

		$p->resMetal = zeroPlus(floor($arr['planet_res_metal']));
		$p->resCrystal = zeroPlus(floor($arr['planet_res_crystal']));
		$p->resPlastic = zeroPlus(floor($arr['planet_res_plastic']));
		$p->resFuel = zeroPlus(floor($arr['planet_res_fuel']));
		$p->resFood = zeroPlus(floor($arr['planet_res_food']));
		$p->usePower = zeroPlus(floor($arr['planet_use_power']));

		$p->resources = array(
			$p->resMetal,
			$p->resCrystal,
			$p->resPlastic,
			$p->resFuel,
			$p->resFood
		);

		$p->bunkerMetal = zeroPlus($arr['planet_bunker_metal']);
		$p->bunkerCrystal = zeroPlus($arr['planet_bunker_crystal']);
		$p->bunkerPlastic = zeroPlus($arr['planet_bunker_plastic']);
		$p->bunkerFuel = zeroPlus($arr['planet_bunker_fuel']);
		$p->bunkerFood = zeroPlus($arr['planet_bunker_food']);

		$p->storeMetal = $arr['planet_store_metal'];
		$p->storeCrystal = $arr['planet_store_crystal'];
		$p->storePlastic = $arr['planet_store_plastic'];
		$p->storeFuel = $arr['planet_store_fuel'];
		$p->storeFood = $arr['planet_store_food'];

		$p->prodMetal = $arr['planet_prod_metal'];
		$p->prodCrystal = $arr['planet_prod_crystal'];
		$p->prodPlastic = $arr['planet_prod_plastic'];
		$p->prodFuel = $arr['planet_prod_fuel'];
		$p->prodFood = $arr['planet_prod_food'];
		$p->prodPower = zeroPlus($arr['planet_prod_power']);
		$p->prodPeople = $arr['planet_prod_people'];

		$p->isMain = ($arr['planet_user_main']==1);

		return $p;
	}

    public function __get($var)
    {
        if($var == 'desc')
        {
            return StringUtils::encodeDBStringToPlaintext($this->desc);
        }
        if($var == 'name')
        {
            return htmlspecialchars($this->name, ENT_QUOTES, 'UTF-8', true);
        }
        return $this->$var;
    }

    public function allowedFleetActions()
    {
    	$arr = array();
    	if ($this->ownerId()>0)
			{
				$arr[] = "transport";
				$arr[] = "fetch";
				$arr[] = "position";
				$arr[] = "attack";
				$arr[] = "spy";
				$arr[] = "invade";
				$arr[] = "spyattack";
				$arr[] = "stealthattack";
				$arr[] = "fakeattack";
				$arr[] = "bombard";
				$arr[] = "antrax";
				$arr[] = "gasattack";
				$arr[] = "createdebris";
				$arr[] = "alliance";
				$arr[] = "support";
				$arr[] = "hijack";
				$arr[] = "market";
				$arr[] = "emp";
			}
    	if ($this->ownerId()==0 && $this->habitable)
    		$arr[] = "colonize";
    	if ($this->debrisField)
    		$arr[] = "collectdebris";
    	if ($this->collectGas)
    	{
    		$arr[] = "collectfuel";
    		$arr[] = "analyze";
    	}
    	$arr[] = "flight";
    	return $arr;
    }

		function id()
		{
			return $this->id;
		}

		function entityCode()
		{
			return "p";
		}


		function entityCodeString()
		{
			return "Planet";
		}

		function ownerId()
		{
			return $this->userId;
		}

		function owner()
		{
			return $this->owner;
		}

		function ownerMain()
		{
			return $this->isMain;
		}

		function type()
		{
			return $this->typeName;
		}
		function imagePath($opt="")
		{
			defineImagePaths();
			if ($opt=="b")
			{
				return IMAGE_PATH."/planets/planet".$this->image.".".IMAGE_EXT;
			}
			if ($opt=="m")
			{
				return IMAGE_PATH."/planets/planet".$this->image."_middle.".IMAGE_EXT;
			}
			return IMAGE_PATH."/planets/planet".$this->image."_small.".IMAGE_EXT;
		}

		function name()
		{
			return $this->__get('name');//htmlspecialchars($this->name);
		}

        function getNoBrDesc()
        {
            return htmlspecialchars($this->desc, ENT_QUOTES, 'UTF-8', true);
        }

		function __toString()
		{
			return $this->formatedCoords()." ".$this->name();
		}

		function cellId()
		{
			return $this->cellId;
		}

		/**
		* Returns current cell and stellar system
		*
		* @return string
		*/
		function getSectorSolsys()
		{
			return $this->sx."/".$this->sy." : ".$this->cx."/".$this->cy;
		}

		function userChanged()
		{
			return $this->userChanged;
		}

		/**
		* Returns current coordinates
		*
		* @return string
		*/
		function getCoordinates()
		{
			return $this->formatedCoords();
		}

		/**
		* Changes resources on a planet
		*/
		function changeRes($m,$c,$p,$fu,$fo,$pw=0)
		{
		    $sql = "
		    UPDATE
		    	planets
		    SET
                planet_res_metal=planet_res_metal+".$m.",
                planet_res_crystal=planet_res_crystal+".$c.",
                planet_res_plastic=planet_res_plastic+".$p.",
                planet_res_fuel=planet_res_fuel+".$fu.",
                planet_res_food=planet_res_food+".$fo."
		    WHERE
		    	id='".$this->id."';";
		    dbquery($sql);
		    $this->resMetal+=$m;
		    $this->resCrystal+=$c;
		    $this->resPlastic+=$p;
		    $this->resFuel+=$fu;
		    $this->resFood+=$fo;
		}

		/**
		* Calculate bonus power production based on temperature
		*/
		function solarPowerBonus()
		{
			$v = floor(($this->temp_from + $this->temp_to)/4);
			if ($v <= -100)
			{
				$v = -99;
			}
			return $v;
		}

		/**
		* Calculate bonus power production based on temperature
		*/
		function fuelProductionBonus()
		{
			$v = floor(($this->temp_from + $this->temp_to)/25);
			return -$v;
		}

		/**
		* Calculate bonus power production based on temperature
		*/
		static function getSolarPowerBonus($t_min,$t_max)
		{
			$v = floor(($t_max + $t_min)/4);
			if ($v <= -100)
			{
				$v = -99;
			}
			return $v;
		}

		/**
		* Calculate bonus power production based on temperature
		*/
		function getFuelProductionBonus()
		{
			$v = floor(($this->temp_from + $this->temp_to)/25);
			return $v/100;
		}

		function assignToUser($uid,$main=0)
		{
	    $sql = "
	    UPDATE
	    	planets
	    SET
				planet_user_id=".intval($uid).",
				planet_user_main=".intval($main)."
	    WHERE
	    	id='".$this->id."';";
	    dbquery($sql);
		}

		function setNameAndComment($name,$comment)
		{
			$name = str_replace("'", '', $name);
			$name = stripBBCode($name);
			$comment = str_replace("'", '', $comment);

			dbquery("
			UPDATE
				planets
			SET
				planet_name='".mysql_real_escape_string($name)."',
				planet_desc='".mysql_real_escape_string($comment)."'
			WHERE
				id='".$this->id."';");
			$this->name=$name;
			$this->desc=$comment;
		}

		function setDefaultResources()
		{
            // TODO
            global $app;

            /** @var ConfigurationService */
            $config = $app[ConfigurationService::class];

			// Set default resources
			dbquery("
			UPDATE
				planets
			SET
	      planet_res_metal='".$config->getInt('user_start_metal')."',
	      planet_res_crystal='".$config->getInt('user_start_crystal')."',
	      planet_res_plastic='".$config->getInt('user_start_plastic')."',
	      planet_res_fuel='".$config->getInt('user_start_fuel')."',
	      planet_res_food='".$config->getInt('user_start_food')."',
	      planet_people=".$config->getInt('user_start_people')."
			WHERE
				id=".$this->id().";");
		}

		//
		// Getters
		//
		function resMetal() { return $this->resMetal; }
		function resCrystal() { return $this->resCrystal; }
		function resPlastic() { return $this->resPlastic; }
		function resFuel() { return $this->resFuel; }
		function resFood() { return $this->resFood; }
		function usePower() { return $this->usePower; }
		function people() { return $this->people; }

		function ownerPoints() { return $this->owner->points; }
		function ownerHoliday() { return $this->owner->holiday; }
		function ownerLocked() { return $this->owner->locked; }
		function ownerAlliance() { return $this->owner->allianceId; }

		function chgPeople($diff)
		{
		    $sql = "
			    UPDATE
	    			planets
	    		SET
        			planet_people=planet_people+".intval($diff)."
	    		WHERE
	    			id='".$this->id."';";
			dbquery($sql);
		}

		function getRes($i)
		{
			switch ($i)
			{
				case 1:
					return $this->resMetal;
				case 2:
					return $this->resCrystal;
				case 3:
					return $this->resPlastic;
				case 4:
					return $this->resFuel;
				case 5:
					return $this->resFood;
			}
		}

		//Added getter with 0-5 like everywhere else
		function getRes1($i)
		{
			switch ($i)
			{
				case 0:
					return $this->resMetal;
				case 1:
					return $this->resCrystal;
				case 2:
					return $this->resPlastic;
				case 3:
					return $this->resFuel;
				case 4:
					return $this->resFood;
			}
		}

		function getProd($i)
		{
			switch ($i)
			{
				case 0:
					return $this->prodMetal;
				case 1:
					return $this->prodCrystal;
				case 2:
					return $this->prodPlastic;
				case 3:
					return $this->prodFuel;
				case 4:
					return $this->prodFood;
			}
		}

		/**
		 * Change resource on planet
		 *
		 * @deprecated See new function below
		 */
		function chgRes($i,$diff)
		{
            $diff = intval($diff);

			switch ($i)
			{
				case 1:
					$str = "planet_res_metal=planet_res_metal+".$diff."";
		    	$this->resMetal+=$diff;
					break;
				case 2:
					$str = "planet_res_crystal=planet_res_crystal+".$diff."";
		    	$this->resCrystal+=$diff;
					break;
				case 3:
					$str = "planet_res_plastic=planet_res_plastic+".$diff."";
		   	 	$this->resPlastic+=$diff;
					break;
				case 4:
					$str = "planet_res_fuel=planet_res_fuel+".$diff."";
		    	$this->resFuel+=$diff;
					break;
				case 5:
					$str = "planet_res_food=planet_res_food+".$diff."";
				default:
					$str = '';
		    $this->resFood+=$diff;
					break;
			}
	    $sql = "
	    UPDATE
	    	planets
	    SET
        ".$str."
	    WHERE
	    	id='".$this->id."';";
	   	dbquery($sql);
		}

		/**
		 *
		 * @global string[] $resNames
		 * @param array $data
		 */
		function addRes($data)
		{
			global $resNames;

			$str = "";
			foreach ($resNames as $rk => $rn)
			{
				if (isset($data[$rk]) && intval($data[$rk])>0)
				{
					$diff = intval($data[$rk]);
					// compatilility...
					// todo: one day, planet table resourcse shold also be enumerated
					if ($str!="")
						$str.=",";
					switch ($rk)
					{
						case 0:
							$str.= "planet_res_metal=planet_res_metal+".$diff."";
							$this->resMetal+=$diff;
							break;
						case 1:
							$str.= "planet_res_crystal=planet_res_crystal+".$diff."";
							$this->resCrystal+=$diff;
							break;
						case 2:
							$str.= "planet_res_plastic=planet_res_plastic+".$diff."";
							$this->resPlastic+=$diff;
							break;
						case 3:
							$str.= "planet_res_fuel=planet_res_fuel+".$diff."";
							$this->resFuel+=$diff;
							break;
						case 4:
							$str.= "planet_res_food=planet_res_food+".$diff."";
							$this->resFood+=$diff;
							break;
					}
		    	$this->resources[$rk] += $diff;
				}
			}
			if ($str!="")
			{
				$sql = "
				UPDATE
					planets
				SET
					".$str."
				WHERE
					id='".$this->id."';";
				dbquery($sql);
				return true;
			}
			return false;
		}

		function checkRes($data)
		{
			global $resNames;

			foreach ($resNames as $rk => $rn)
			{
				if (isset($data[$rk]) && $data[$rk]>=0)
				{
					if ($this->resources[$rk] - intval($data[$rk]) < 0)
						return false;
				}
			}
			return true;
		}

		function subRes($data)
		{
			global $resNames;

			$str = "";
			foreach ($resNames as $rk => $rn)
			{
				if (isset($data[$rk]) && intval($data[$rk])>0)
				{
					$diff = intval($data[$rk]);

					if ($this->resources[$rk] - $diff < 0)
						return false;

					// todo: one day, planet table resourcse shold also be enumerated
					if ($str!="")
						$str.=",";
					switch ($rk)
					{
						case 0:
							$str.= "planet_res_metal=planet_res_metal-".$diff."";
							$this->resMetal-=$diff;
							break;
						case 1:
							$str.= "planet_res_crystal=planet_res_crystal-".$diff."";
							$this->resCrystal-=$diff;
							break;
						case 2:
							$str.= "planet_res_plastic=planet_res_plastic-".$diff."";
							$this->resPlastic-=$diff;
							break;
						case 3:
							$str.= "planet_res_fuel=planet_res_fuel-".$diff."";
							$this->resFuel-=$diff;
							break;
						case 4:
							$str.= "planet_res_food=planet_res_food-".$diff."";
							$this->resFood-=$diff;
							break;
					}
		    	$this->resources[$rk] -= $diff;
				}
			}
			if ($str!="")
			{
				$sql = "
				UPDATE
					planets
				SET
					".$str."
				WHERE
					id='".$this->id."';";
				dbquery($sql);
				return true;
			}
			return false;
		}


		function chgBunker($i,$amount)
		{
            $amount = intval($amount);

			switch ($i)
			{
				case 1:
					$str = "planet_bunker_metal=".$amount."";
                    $this->bunkerMetal=$amount;
					break;
				case 2:
					$str = "planet_bunker_crystal=".$amount."";
                    $this->bunkerCrystal=$amount;
					break;
				case 3:
					$str = "planet_bunker_plastic=".$amount."";
                    $this->bunkerPlastic=$amount;
					break;
				case 4:
					$str = "planet_bunker_fuel=".$amount."";
                    $this->bunkerFuel=$amount;
					break;
				case 5:
					$str = "planet_bunker_food=".$amount."";
		    $this->bunkerFood=$amount;
					break;
				default:
					return;
			}
			$sql = "
	   			UPDATE
	    			planets
	    		SET
		        	".$str."
	    		WHERE
	    			id='".$this->id."';";
	   		dbquery($sql);
		}

		function reloadRes()
		{
			$res = dbquery("
						   SELECT
						   		planet_res_metal,
								planet_res_crystal,
								planet_res_plastic,
								planet_res_fuel,
								planet_res_food
							FROM
								planets
							WHERE
								id='".$this->id."'
							LIMIT 1;");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_assoc($res);
				$this->resMetal = floor($arr['planet_res_metal']);
				$this->resCrystal = floor($arr['planet_res_crystal']);
				$this->resPlastic = floor($arr['planet_res_plastic']);
				$this->resFuel = floor($arr['planet_res_fuel']);
				$this->resFood = floor($arr['planet_res_food']);
			}
		}

		public function getFleetTargetForwarder()
		{
			return null;
		}

		public function getResourceLog()
		{
			return $this->resMetal.":".$this->resCrystal.":".$this->resPlastic.":".$this->resFuel.":".$this->resFood.":".$this->people.":0,w,".$this->debrisMetal.":".$this->debrisCrystal.":".$this->debrisPlastic;
		}

		public function lastUserCheck()
		{
			$t = $this->userChanged()+COLONY_DELETE_THRESHOLD;
			if ($t > time())
			{
				return $this->lastUserId;
			}
			return 0;
		}

	}
?>
