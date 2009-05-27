<?PHP
      
  /**
  * Planet class
  *
  * @author Nicolas Perrenoud <mrcage@etoa.ch>
  */
	class Planet extends Entity implements OwnableEntity
	{
		protected $isValid;
		protected $coordsLoaded;

		// TODO: Make protected and ad getter
		public $resources;
		
		/**
		* Constructor
		* Erwartet ein Array mit dem Inhalt des MySQL-Datensatzes, oder die ID eines Planeten
		*/
		function Planet($arr=null)
		{
			$this->exploreCode = 'e';
			$this->explore = false;
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
        	pentity.pos,
        	planet_types.*,
			sol_types.*
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
				
				$this->owner = new User($arr['planet_user_id']);
				
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

				$this->starTypeId=$arr['sol_type_id'];
				$this->starTypeName=$arr['sol_type_name'];
				$this->starMetal=$arr['sol_type_f_metal'];
				$this->starCrystal=$arr['sol_type_f_crystal'];
				$this->starPlastic=$arr['sol_type_f_plastic'];
				$this->starFuel=$arr['sol_type_f_fuel'];
				$this->starFood=$arr['sol_type_f_food'];
				$this->starPower=$arr['sol_type_f_power'];
				$this->starPopulation=$arr['sol_type_f_population'];
				$this->starResearchtime=$arr['sol_type_f_researchtime'];
				$this->starBuildtime=$arr['sol_type_f_buildtime'];
				
				$this->debrisMetal = $arr['planet_wf_metal'];
				$this->debrisCrystal = $arr['planet_wf_crystal'];
				$this->debrisPlastic = $arr['planet_wf_plastic'];

				if ($this->debrisMetal+$this->debrisCrystal+$this->debrisPlastic > 0)
					$this->debrisField = true;
				else
					$this->debrisField = false;

				$this->fieldsBase = $arr['planet_fields'];
				$this->fieldsExtra = $arr['planet_fields_extra'];
				$this->fieldsUsed=$arr['planet_fields_used'];
				
				$this->fields_extra=$arr['planet_fields_extra'];
				$this->fields_used=$arr['planet_fields_used'];
				
				$this->fields = $this->fieldsBase + $this->fieldsExtra;
								
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

				$this->resources = array($this->resMetal,$this->resCrystal,$this->resPlastic,$this->resFuel,$this->resFood);

				$this->bunkerMetal = zeroPlus($arr['planet_bunker_metal']);
				$this->bunkerCrystal = zeroPlus($arr['planet_bunker_crystal']);
				$this->bunkerPlastic = zeroPlus($arr['planet_bunker_plastic']);
				$this->bunkerFuel = zeroPlus($arr['planet_bunker_fuel']);
				$this->bunkerFood = zeroPlus($arr['planet_bunker_food']);

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
		
		function ownerMain() { return $this->isMain; }
		
		
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
		function resBox($small=0)
		{
			$cfg = Config::getInstance();

			$style0="";
			$style1="";
			$style2="";
			$style3="";
			$style4="";
			$style5="";
			
			$store_err=array();

			if ($small==0)
			{
				$store_msg=false;
				$power_msg=false;
				$place_msg=false;
	
				if ($this->storeMetal<=floor($this->resMetal) && floor($this->resMetal)>0)
				{
					$style0="resfullcolor";
					$store_msg=true;
				}
				else
					$style0="resmetalcolor";
				if ($this->storeCrystal<=floor($this->resCrystal) && floor($this->resCrystal)>0)
				{
					$style1="resfullcolor";
					$store_msg=true;
				}
				else	
					$style1="rescrystalcolor";
				if ($this->storePlastic<=floor($this->resPlastic) && floor($this->resPlastic)>0)
				{
					$style2="resfullcolor";
					$store_msg=true;
				}
				else
					$style2="resplasticcolor";
				if ($this->storeFuel<=floor($this->resFuel) && floor($this->resFuel)>0)
				{
					$style3="resfullcolor";
					$store_msg=true;
				}
				else
					$style3="resfuelcolor";
				if ($this->storeFood<=floor($this->resFood) && floor($this->resFood)>0)
				{
					$style4="resfullcolor";
					$store_msg=true;
				}
				else
					$style4="resfoodcolor";
				if ($this->people_place<=floor($this->people) && floor($this->people)>0)
				{
					$style5="resfullcolor";
					$place_msg=true;
				}
				else
					$style5="respeoplecolor";
				if(floor($this->prodPower)-floor($this->usePower)<0)
				{
					$style6="resfullcolor";
					$power_msg=true;
					$power_rest = floor($this->prodPower)-floor($this->usePower);
				}
				else
				{
					$style6="respowercolor";
					$power_rest = floor($this->prodPower)-floor($this->usePower);
				}
				tableStart("Ressourcen");
				echo "<tr>
				<th class=\"resBoxTitleCell\"><div class=\"resmetal\">".RES_METAL."</div></th>
				<th class=\"resBoxTitleCell\"><div class=\"rescrystal\">".RES_CRYSTAL."</div></th>
				<th class=\"resBoxTitleCell\"><div class=\"resplastic\">".RES_PLASTIC."</div></th>
				<th class=\"resBoxTitleCell\"><div class=\"resfuel\">".RES_FUEL."</div></th>
				<th class=\"resBoxTitleCell\"><div class=\"resfood\">".RES_FOOD."</div></th>
				<th class=\"resBoxTitleCell\"><div class=\"respeople\">Bewohner</div></th>
				<th class=\"resBoxTitleCell\"><div class=\"respower\">Energie</div></th>
				</tr><tr>
				<td class=\"$style0\">".nf(floor($this->resMetal))."</td>
				<td class=\"$style1\">".nf(floor($this->resCrystal))."</td>
				<td class=\"$style2\">".nf(floor($this->resPlastic))."</td>
				<td class=\"$style3\">".nf(floor($this->resFuel))."</td>
				<td class=\"$style4\">".nf(floor($this->resFood))."</td>
				<td class=\"$style5\">".nf(floor($this->people))."</td>
				<td class=\"$style6\">".nf($power_rest)."</td>
				</tr>";

				/*	
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
				}*/
				/*
				echo "<tr>
					<td colspan=\"7\" id=\"resprogress\" style=\"height:10px;background:#fff;text-align:center;\"></td>";
				jsProgressBar("resprogress",$this->updated,($this->updated + $cfg->value("res_update")),650);
					*/
				tableEnd();
			}
			else
			{
				if ($this->storeMetal<=floor($this->resMetal) && floor($this->resMetal)>0)
				{
					$style0="resfullcolor";
					$store_msg[1] = tm("Speicher voll","Produktion gestoppt, bitte Speicher ausbauen!");
					$store_err[1]=true;
				}
				if ($this->storeCrystal<=floor($this->resCrystal) && floor($this->resCrystal)>0)
				{
					$style1="resfullcolor";
					$store_msg[2] = tm("Speicher voll","Produktion gestoppt, bitte Speicher ausbauen!");
					$store_err[2]=true;
				}
				if ($this->storePlastic<=floor($this->resPlastic) && floor($this->resPlastic)>0)
				{
					$style2=" resfullcolor";
					$store_msg[3] = tm("Speicher voll","Produktion gestoppt, bitte Speicher ausbauen!");
					$store_err[3]=true;
				}
				if ($this->storeFuel<=floor($this->resFuel) && floor($this->resFuel)>0)
				{
					$style3=" resfullcolor";
					$store_msg[4] = tm("Speicher voll","Produktion gestoppt, bitte Speicher ausbauen!");
					$store_err[4]=true;
				}
				if ($this->storeFood<=floor($this->resFood) && floor($this->resFood)>0)
				{
					$style4=" resfullcolor";
					$store_msg[5] = tm("Speicher voll","Produktion gestoppt, bitte Speicher ausbauen!");
					$store_err[5]=true;
				}
				if ($this->people_place<=floor($this->people) && floor($this->people)>0)
				{
					$style5=" resfullcolor";
					$store_msg[6] = tm("Wohnraum voll","Wachstum gestoppt, bitte Wohnraum ausbauen!");
					$store_err[6]=true;
				}
				if(floor($this->prodPower)-floor($this->usePower)<0)
				{
					$style6=" resfullcolor";
					$store_msg[7] = tm("Zuwenig Energie","Produktion verringert, bitte Kraftwerk ausbauen!");
					$store_err[7] = true;
					$power_rest = floor($this->prodPower)-floor($this->usePower);
				}
				else
				{
					$style6="";
					$store_msg[7] = "";
					$store_err[7] = "";
					$power_rest = floor($this->prodPower)-floor($this->usePower);
				}				
				echo "<div id=\"resbox\">
				<div id=\"resboxheader\">Resourcen</div>
				<div id=\"resboxcontent\">			
				<span class=\"resmetal ".$style0."\" ".mTT(RES_METAL,"<img src=\"images/resources/metal.png\" style=\"float:left;margin-right:5px;\"/> <b>Vorhanden:</b> ".nf($this->resMetal)."<br/><b>Speicher:</b> ".nf($this->storeMetal)."<br style=\"clear:both;\"/>").">".nf($this->resMetal,0,1)."</span>
				<span class=\"rescrystal ".$style1."\" ".mTT(RES_CRYSTAL,"<img src=\"images/resources/crystal.png\" style=\"float:left;margin-right:5px;\"/> <b>Vorhanden:</b> ".nf($this->resCrystal)."<br/><b>Speicher:</b> ".nf($this->storeCrystal)."<br style=\"clear:both;\"/>").">".nf($this->resCrystal,0,1)."</span>
				<span class=\"resplastic ".$style2."\" ".mTT(RES_PLASTIC,"<img src=\"images/resources/plastic.png\" style=\"float:left;margin-right:5px;\"/> <b>Vorhanden:</b> ".nf($this->resPlastic)."<br/><b>Speicher:</b> ".nf($this->storePlastic)."<br style=\"clear:both;\"/>").">".nf($this->resPlastic,0,1)."</span>
				<span class=\"resfuel ".$style3."\" ".mTT(RES_FUEL,"<img src=\"images/resources/fuel.png\" style=\"float:left;margin-right:5px;\"/> <b>Vorhanden:</b> ".nf($this->resFuel)."<br/><b>Speicher:</b> ".nf($this->storeFuel)."<br style=\"clear:both;\"/>").">".nf($this->resFuel,0,1)."</span>
				<span class=\"resfood ".$style4."\" ".mTT(RES_FOOD,"<img src=\"images/resources/food.png\" style=\"float:left;margin-right:5px;\"/> <b>Vorhanden:</b> ".nf($this->resFood)."<br/><b>Speicher:</b> ".nf($this->storeFood)."<br style=\"clear:both;\"/>").">".nf($this->resFood,0,1)."</span>
				<span class=\"respeople ".$style5."\" ".mTT("Bevölkerung","<img src=\"images/resources/people.png\" style=\"float:left;margin-right:5px;\"/> <b>Vorhanden:</b> ".nf($this->people)."<br/><b>Platz:</b> ".nf($this->people_place)."<br style=\"clear:both;\"/>").">".nf($this->people,0,1)."</span>
				<span class=\"respower ".$style6."\" ".mTT(RES_POWER,"<img src=\"images/resources/power.png\" style=\"float:left;margin-right:5px;\"/> <b>Produktion:</b> ".nf($this->prodPower)."<br/><b>Verfügbar:</b> ".nf($power_rest)."<br/><b>Verbrauch:</b> ".nf($this->usePower)."<br style=\"clear:both;\"/>").">".nf($power_rest,0,1)."</span>
				</div>
				</div>";			
			}
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
				planet_name='".addslashes($name)."',
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
        			planet_people=planet_people+".$diff."
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

		/**
		 * Change resource on planet
		 * 
		 * @deprecated See new function below
		 */
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

		/**
		 *
		 * @global <type> $resNames
		 * @param <type> $data
		 */
		function addRes($data)
		{
			global $resNames;

			$str = "";
			foreach ($resNames as $rk => $rn)
			{
				if (isset($data[$rk]) && $data[$rk]>0)
				{
					$diff = $data[$rk];
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

		function subRes($data)
		{
			global $resNames;

			$str = "";
			foreach ($resNames as $rk => $rn)
			{
				if (isset($data[$rk]) && $data[$rk]>0)
				{
					$diff = $data[$rk];

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

		/**
		* Set this planet as the users main
		* planet and remove main flag from all other 
		* planets of this user
		*/
		function setMain()
		{
			if (!$this->isMain)
			{
				$this->isMain=true;
				dbquery("
					UPDATE
						planets
					SET
						planet_user_main=0
					WHERE
						planet_user_id='".$this->userId."'
				");				
				dbquery("
					UPDATE
						planets
					SET
						planet_user_main=1
					WHERE
						id='".$this->id."'
				");			
				return true;
			}
			return false;
		}

		function unsetMain()
		{
			if ($this->isMain)
			{
				$this->isMain=false;
				dbquery("
					UPDATE
						planets
					SET
						planet_user_main=0
					WHERE
						id='".$this->id."'
				");			
				return true;
			}
			return false;
		}
		
		
		/**
		* ۢrnimmt einen Planeten (Invasion)
		*
		* @param int $new_user_id User ID des 'ۢernehmers'
		* @athor Lamborghini
		*/
		function chown($new_user_id)
		{
			$this->name = "Unbenannt";
			$this->userId = $new_user_id;
			$this->changed = time();
			
      // Planet �hmen
			dbquery("
				UPDATE
					planets
				SET
					planet_user_id='".$this->userId."',
					planet_name='".$this->name."',
					planet_user_changed=".$this->changed.",
					planet_user_main=0
				WHERE
					id='".$this->id."';
			");

      // Geb㴤e �hmen
      dbquery("
			UPDATE
				buildlist
			SET
				buildlist_user_id='".$this->userId."'
			WHERE
				buildlist_entity_id='".$this->id."';
			");
	
	
	    // Bestehende Schiffs-Eintr㦥 l�en
	    dbquery("
				DELETE FROM
					shiplist
				WHERE
					shiplist_entity_id='".$this->id."';
			");
	    dbquery("
				DELETE FROM
					ship_queue
				WHERE
					queue_entity_id='".$this->id."';
			");		
			
	
	
	    // Bestehende Verteidigungs-Eintr㦥 l�en
	    dbquery("
				DELETE FROM
					deflist
				WHERE
					deflist_entity_id='".$this->id."';
			");
	    dbquery("
				DELETE FROM
					def_queue
				WHERE
					queue_entity_id='".$this->id."';
			");
	
		}		
		
		public function getFleetTargetForwarder()
		{
			return null;
		}
			
	}
?>