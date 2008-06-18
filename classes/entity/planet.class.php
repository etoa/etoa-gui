<?PHP
      
  /**
  * Planet class
  *
  * @author Nicolas Perrenoud <mrcage@etoa.ch>
  */
	class Planet extends Entity
	{
		protected $isValid;
		protected $coordsLoaded;
		
		/**
		* Constructor
		* Erwartet ein Array mit dem Inhalt des MySQL-Datensatzes, oder die ID eines Planeten
		*/
		function Planet($arr=null)
		{
			$this->isValid = false;
      $this->isVisible = true;
			
			if (!is_array($arr) && $arr>0)
			{
				$res = dbquery("
				SELECT
        	planets.*,
        	cells.sx,
        	cells.sy,
        	cells.cx,
        	cells.cy,
        	cells.id as cell_id,
        	entities.pos,
        	planet_types.*
				FROM 
				(
					planets
        	INNER JOIN 
          	planet_types 
            ON planets.planet_type_id = planet_types.type_id
            AND planets.id='".$arr."'
				)
        INNER JOIN 
        (	
        	entities
         	INNER JOIN cells 
          	ON cells.id = entities.cell_id
        )
        ON planets.id = entities.id
				;");

				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_assoc($res);
				}
				else
				{
					echo "Planet $arr nicht gefunden!\n";
				}
			}

			if ($arr)
			{				
				$this->id=$arr['id'];
				$this->cellId=$arr['cell_id'];
				$this->userId=$arr['planet_user_id'];
				$this->name= $arr['planet_name']!="" ? stripslashes($arr['planet_name']) : 'Unbenannt';
				$this->desc= stripslashes($arr['planet_desc']);
				$this->image=$arr['planet_image'];
				$this->updated=$arr['planet_last_updated'];
				$this->userChanged=$arr['planet_user_changed'];
				
				if ($arr['planet_user_id']>0)
				{
					$ures = dbquery("
					SELECT
						user_nick,
						user_race_id
					FROM
						users
					WHERE
						user_id=".$arr['planet_user_id']."
					");
					$uarr = mysql_Fetch_row($ures);
					$this->owner = $uarr[0];
					$this->ownerRaceId = $uarr[1];
				}
				else
				{
					$this->owner = "Niemand";	
					$this->ownerRaceId = 0;
				}
				
				
				
				$this->sx = $arr['sx'];
				$this->sy = $arr['sy'];
				$this->cx = $arr['cx'];
				$this->cy = $arr['cy'];
				$this->pos = $arr['pos'];

				$this->typeId = $arr['type_id'];
				$this->typeName = $arr['type_name'];

				$this->habitable = (boolean)$arr['type_habitable'];
				$this->collectGas = (boolean)$arr['type_collect_gas'];

				$this->typeMetal=$arr['type_f_metal'];
				$this->typeCrystal=$arr['type_f_crystal'];
				$this->typePlastic=$arr['type_f_plastic'];
				$this->typeFuel=$arr['type_f_fuel'];
				$this->typeFood=$arr['type_f_food'];
				$this->typePower=$arr['type_f_power'];
				$this->typePopulation=$arr['type_f_population'];
				$this->typeResearchtime=$arr['type_f_researchtime'];
				$this->typeBuildtime=$arr['type_f_buildtime'];

				$this->starTypeId=0;
				$this->starTypeName="Teststern";

				$this->starMetal=1;
				$this->starCrystal=1;
				$this->starPlastic=1;
				$this->starFuel=1;
				$this->starFood=1;
				$this->starPower=1;
				$this->starPopulation=1;
				$this->starResearchtime=1;
				$this->starBuildtime=1;

/*
				$this->starMetal=$arr['sol_type_f_metal'];
				$this->starCrystal=$arr['sol_type_f_crystal'];
				$this->starPlastic=$arr['sol_type_f_plastic'];
				$this->starFuel=$arr['sol_type_f_fuel'];
				$this->starFood=$arr['sol_type_f_food'];
				$this->starPower=$arr['sol_type_f_power'];
				$this->starPopulation=$arr['sol_type_f_population'];
				$this->starResearchtime=$arr['sol_type_f_researchtime'];
				$this->starBuildtime=$arr['sol_type_f_buildtime'];
*/
				$this->debrisMetal = $arr['planet_wf_metal'];
				$this->debrisCrystal = $arr['planet_wf_crystal'];
				$this->debrisPlastic = $arr['planet_wf_plastic'];

				if ($this->debrisMetal+$this->debrisCrystal+$this->debrisPlastic > 0)
					$this->debrisField = true;
				else
					$this->debrisField = false;

				$this->fields=$arr['planet_fields'];
				$this->fields_extra=$arr['planet_fields_extra'];
				$this->fields_used=$arr['planet_fields_used'];
				$this->temp_from=$arr['planet_temp_from'];
				$this->temp_to=$arr['planet_temp_to'];
				$this->people=zeroPlus($arr['planet_people']);
				$this->people_place=zeroPlus($arr['planet_people_place']);

				$this->resMetal=zeroPlus($arr['planet_res_metal']);
				$this->resCrystal=zeroPlus($arr['planet_res_crystal']);
				$this->resPlastic=zeroPlus($arr['planet_res_plastic']);
				$this->resFuel=zeroPlus($arr['planet_res_fuel']);
				$this->resFood=zeroPlus($arr['planet_res_food']);
				$this->usePower=zeroPlus($arr['planet_use_power']);

				$this->storeMetal=$arr['planet_store_metal'];
				$this->storeCrystal=$arr['planet_store_crystal'];
				$this->storePlastic=$arr['planet_store_plastic'];
				$this->storeFuel=$arr['planet_store_fuel'];
				$this->storeFood=$arr['planet_store_food'];
				
				$this->prodMetal=$arr['planet_prod_metal'];
				$this->prodCrystal=$arr['planet_prod_crystal'];
				$this->prodPlastic=$arr['planet_prod_plastic'];
				$this->prodFuel=$arr['planet_prod_fuel'];
				$this->prodFood=$arr['planet_prod_food'];
				$this->prodPower=zeroPlus($arr['planet_prod_power']);
				$this->prodPeople=$arr['planet_prod_people'];

				if ($arr['planet_user_main']==1)
					$this->isMain=true;
				else
					$this->isMain=false;

				$this->isValid = true;

			}
		}

    public function allowedFleetActions()
    {
    	$arr = array("transport","fetch","position","attack","spy");
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
		
		function type()
		{
			return $this->typeName;
		}		
		function imagePath($opt="")
		{
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
			return $this->name;
		}

		function __toString()
		{
			return $this->formatedCoords()." ".$this->name;
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
		* Displays a box with resources, power and population
		*/
		function resBox()
		{
			$style0="class=\"tbldata\"";
			$style1="class=\"tbldata\"";
			$style2="class=\"tbldata\"";
			$style3="class=\"tbldata\"";
			$style4="class=\"tbldata\"";
			$style5="class=\"tbldata\"";
			
			$store_msg=false;
			$power_msg=false;
			$place_msg=false;

			if ($this->storeMetal<=floor($this->resMetal) && floor($this->resMetal)>0)
			{
				$style0="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->storeCrystal<=floor($this->resCrystal) && floor($this->resCrystal)>0)
			{
				$style1="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->storePlastic<=floor($this->resPlastic) && floor($this->resPlastic)>0)
			{
				$style2="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->storeFuel<=floor($this->resFuel) && floor($this->resFuel)>0)
			{
				$style3="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->storeFood<=floor($this->resFood) && floor($this->resFood)>0)
			{
				$style4="class=\"tbldata2\"";
				$store_msg=true;
			}
			if ($this->people_place<=floor($this->people) && floor($this->people)>0)
			{
				$style5="class=\"tbldata2\"";
				$place_msg=true;
			}
			if(floor($this->prodPower)-floor($this->usePower)<0)
			{
				$style6="class=\"tbldata2\"";
				$power_msg=true;
				$power_rest = floor($this->prodPower)-floor($this->usePower);
			}
			else
			{
				$style6="class=\"tbldata3\"";
				$power_rest = floor($this->prodPower)-floor($this->usePower);
			}
			infobox_start("Ressourcen",1);
			echo "<tr>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_METAL." ".RES_METAL."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_CRYSTAL." ".RES_CRYSTAL."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_PLASTIC." ".RES_PLASTIC."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_FUEL." ".RES_FUEL."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_FOOD." ".RES_FOOD."</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_PEOPLE." Bewohner</td>
			<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_POWER." Energie</td>
			</tr><tr>
			<td $style0>".nf(floor($this->resMetal))." t</td>
			<td $style1>".nf(floor($this->resCrystal))." t</td>
			<td $style2>".nf(floor($this->resPlastic))." t</td>
			<td $style3>".nf(floor($this->resFuel))." t</td>
			<td $style4>".nf(floor($this->resFood))." t</td>
			<td $style5>".nf(floor($this->people))."</td>
			<td $style6>".nf($power_rest)."</td>
			</tr>";

			$text = array();
			if ($store_msg)
				array_push($text,"Speicher");
			if ($place_msg)
				array_push($text,"Wohnmodule");
			if ($power_msg)
				array_push($text,"Kraftwerke");


			if (count($text)>0)
			{
				echo "<tr><td class=\"tbldata\" colspan=\"7\" style=\"text-align:center;color:orange;\"><i>Es werden ben&ouml;tigt: ";

				$cnt=0;
                foreach ($text as $value)
                {
                	if($cnt!=0)
                		echo ", ";
                	echo "$value";
                	$cnt++;
                }
				echo "</i></td></tr>";
			}
			echo "</table><br/>";
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
		
		function assignToUser($uid,$main=0)
		{
	    $sql = "
	    UPDATE
	    	planets
	    SET
				planet_user_id=".$uid.",
				planet_user_main=".$main."
	    WHERE
	    	id='".$this->id."';";
	    dbquery($sql);		
		}
		
		function setNameAndComment($name,$comment)
		{
			dbquery("
			UPDATE 
				planets 
			SET 
				planet_name='".$name."',
				planet_desc='".addslashes($comment)."' 
			WHERE 
				id='".$this->id."';");
			$this->name=$name;
			$this->desc=$comment;			
		}
	
		function setDefaultResources()
		{
			// Set default resources
			dbquery("
			UPDATE
				planets
			SET
	      planet_res_metal='".USR_START_METAL."',
	      planet_res_crystal='".USR_START_CRYSTAL."',
	      planet_res_plastic='".USR_START_PLASTIC."',
	      planet_res_fuel='".USR_START_FUEL."',
	      planet_res_food='".USR_START_FOOD."',
	      planet_people=".USR_START_PEOPLE."
			WHERE
				id=".$this->id().";");				
		}
		
		function reset()
		{
			dbquery("
				UPDATE
					planets
				SET
					planet_user_id=0,
					planet_name='',
					planet_user_main=0,
					planet_fields_used=0,
					planet_fields_extra=0,
					planet_res_metal=0,
					planet_res_crystal=0,
					planet_res_fuel=0,
					planet_res_plastic=0,
					planet_res_food=0,
					planet_use_power=0,
					planet_last_updated=0,
					planet_prod_metal=0,
					planet_prod_crystal=0,
					planet_prod_plastic=0,
					planet_prod_fuel=0,
					planet_prod_food=0,
					planet_prod_power=0,
					planet_store_metal=0,
					planet_store_crystal=0,
					planet_store_plastic=0,
					planet_store_fuel=0,
					planet_store_food=0,
					planet_people=1,
					planet_people_place=0,
					planet_desc=''
				WHERE
					id='".$this->id."';
			");
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
		
		function chgPeople($diff)
		{
	    $this->people+=$diff;
	    $sql = "
	    UPDATE
	    	planets
	    SET
        planet_people=planet_people+".$diff."
	    WHERE
	    	id='".$this->id."';";			
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
		
		function chgRes($i,$diff)
		{
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
	}
?>