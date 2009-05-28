<?PHP
	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	// $Author$
	// $Date$
	// $Rev$
	//
	
	/**
	* Bauhof-Modul
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

  // DEFINITIONEN //

  define('NUM_BUILDINGS_PER_ROW',5);// Gebäude pro Reihe
  define('CELL_WIDTH',120);					// Breite der Gebäudezelle in der Übersicht
	
	// Aktiviert / Deaktiviert Bildfilter
	if ($cu->properties->imageFilter==1)
	{
		$use_img_filter = true;
	}
	else
	{
		$use_img_filter = false;
	}

/* This function has to be outsourced, of course, later */



function calcBuildingWaitTime($bc,$cp)
{
	$notAvStyle=" style=\"color:red;\"";
	
	// Wartezeiten auf Ressourcen berechnen
	if ($cp->prodMetal>0) $bwait['metal']=ceil(($bc['metal']-$cp->resMetal)/$cp->prodMetal*3600);else $bwait['metal']=0;
	if ($cp->prodCrystal>0) $bwait['crystal']=ceil(($bc['crystal']-$cp->resCrystal)/$cp->prodCrystal*3600);else $bwait['crystal']=0;
	if ($cp->prodPlastic>0) $bwait['plastic']=ceil(($bc['plastic']-$cp->resPlastic)/$cp->prodPlastic*3600);else $bwait['plastic']=0;
	if ($cp->prodFuel>0) $bwait['fuel']=ceil(($bc['fuel']-$cp->resFuel)/$cp->prodFuel*3600);else $bwait['fuel']=0;
	if ($cp->prodFood>0) $bwait['food']=ceil(($bc['food']-$cp->resFood)/$cp->prodFood*3600);else $bwait['food']=0;
	$bwmax=max($bwait['metal'],$bwait['crystal'],$bwait['plastic'],$bwait['fuel'],$bwait['food']);

	// Baukosten-String
	$bcstring = "<td ";
	if ($bc['metal']>$cp->resMetal)
		$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff","<b>".nf($bc['metal']-$cp->resMetal)."</b> ".RES_METAL."<br/>Bereit in <b>".tf($bwait['metal'])."</b>");
	
	$bcstring.= ">".nf($bc['metal'])."</td><td";
	if ($bc['crystal']>$cp->resCrystal)
		$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['crystal']-$cp->resCrystal)." ".RES_CRYSTAL."<br/>Bereit in <b>".tf($bwait['crystal'])."</b>");
	
	$bcstring.= ">".nf($bc['crystal'])."</td><td";
	if ($bc['plastic']>$cp->resPlastic)
		$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['plastic']-$cp->resPlastic)." ".RES_PLASTIC."<br/>Bereit in <b>".tf($bwait['plastic'])."</b>");
	
	$bcstring.= ">".nf($bc['plastic'])."</td><td";
	if ($bc['fuel']>$cp->resFuel)
		$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['fuel']-$cp->resFuel)." ".RES_FUEL."<br/>Bereit in <b>".tf($bwait['fuel'])."</b>");
	
	$bcstring.= ">".nf($bc['fuel'])."</td><td";
	if ($bc['food']>$cp->resFood)
		$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['food']-$cp->resFood)." ".RES_FOOD."<br/>Bereit in <b>".tf($bwait['food'])."</b>");
	
	$bcstring.= ">".nf($bc['food'])."</td><td";
	if ($bc['power']> $cp->prodPower- $cp->usePower && $bc['power']>0)
		$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($bc['power']-($cp->prodPower-$cp->usePower))." Energie");
	
	$bcstring.= ">".nf($bc['power'])."</td></tr>";
	return array($bcstring,$bwmax);
}

function calcDemolishingCosts($buildingArray, $buildingCosts, $fac)
{	
	$dc=array();
	// Abrisskostenberechnung				Abrisskosten = Baukosten  * Abrisskostenfaktor
	$dc['metal'] = $fac * $buildingCosts['metal'] * $buildingArray['building_demolish_costs_factor'];
	$dc['crystal'] = $fac * $buildingCosts['crystal'] * $buildingArray['building_demolish_costs_factor'];
	$dc['plastic'] = $fac * $buildingCosts['plastic'] * $buildingArray['building_demolish_costs_factor'];
	$dc['fuel'] = $fac * $buildingCosts['fuel'] * $buildingArray['building_demolish_costs_factor'];
	$dc['food'] = $fac * $buildingCosts['food'] * $buildingArray['building_demolish_costs_factor'];
	$dc['power'] = $fac * $buildingCosts['power'] * $buildingArray['building_demolish_costs_factor'];
	return $dc;
}

function calcDemolishingWaitTime($dc,$cp)
{
	$notAvStyle=" style=\"color:red;\"";
	
	if ($cp->prodMetal>0)
		$dwait['metal']=ceil(($dc['metal']-$cp->resMetal)/$cp->prodMetal*3600);
	else
		$dwait['metal']=0;
	if ($cp->prodCrystal>0)
		$dwait['crystal']=ceil(($dc['crystal']-$cp->resCrystal)/$cp->prodCrystal*3600);
	else
		$dwait['crystal']=0;
	if ($cp->prodPlastic>0)
		$dwait['plastic']=ceil(($dc['plastic']-$cp->resPlastic)/$cp->prodPlastic*3600);
	else
		$dwait['plastic']=0;
	if ($cp->prodFuel>0)
		$dwait['fuel']=ceil(($dc['fuel']-$cp->resFuel)/$cp->prodFuel*3600);
	else
		$dwait['fuel']=0;
	if ($cp->prodFood>0)
		$dwait['food']=ceil(($dc['food']-$cp->resFood)/$cp->prodFood*3600);
	else
		$dwait['food']=0;
	$dwmax=max($dwait['metal'],$dwait['crystal'],$dwait['plastic'],$dwait['fuel'],$dwait['food']);
	
	$dwstring = "<td";
	if ($dc['metal']>$cp->resMetal)
		$dwstring.= $notAvStyle." ".tm("Fehlender Rohstoff","<b>".nf($dc['metal']-$cp->resMetal)."</b> ".RES_METAL."<br/>Bereit in <b>".tf($dwait['metal'])."</b>");
	
	$dwstring.= ">".nf($dc['metal'])."</td><td";
	if ($dc['crystal']>$cp->resCrystal)
		$bcstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($dc['crystal']-$cp->resCrystal)." ".RES_CRYSTAL."<br/>Bereit in <b>".tf($dwait['crystal'])."</b>");
	
	$dwstring.= ">".nf($dc['crystal'])."</td><td";
	if ($dc['plastic']>$cp->resPlastic)
		$dwstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($dc['plastic']-$cp->resPlastic)." ".RES_PLASTIC."<br/>Bereit in <b>".tf($dwait['plastic'])."</b>");
	
	$dwstring.= ">".nf($dc['plastic'])."</td><td";
	if ($dc['fuel']>$cp->resFuel)
		$dwstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($dc['fuel']-$cp->resFuel)." ".RES_FUEL."<br/>Bereit in <b>".tf($dwait['fuel'])."</b>");
	
	$dwstring.= ">".nf($dc['fuel'])."</td><td";
	if ($dc['food']>$cp->resFood)
		$dwstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($dc['food']-$cp->resFood)." ".RES_FOOD."<br/>Bereit in <b>".tf($dwait['food'])."</b>");
	
	$dwstring.= ">".nf($dc['food'])."</td><td";
	if ($dc['power']> $cp->prodPower- $cp->usePower && $dc['power']>0)
		$dwstring.= $notAvStyle." ".tm("Fehlender Rohstoff",nf($dc['power']-($cp->prodPower-$cp->usePower))." Energie");
	
	$dwstring.= ">".nf($dc['power'])."</td></tr>";
	return array($dwstring,$dwmax);
}

	// SKRIPT //

	if (isset($cp))
	{
		echo "<h1>Bauhof des Planeten ".$cp->name()."</h1>";
		$cp->resBox($cu->properties->smallResBox);


		//
		// Lädt alle benötigten Daten in Arrays
		//

		// Gebäudeliste laden
		$sql ="
		SELECT 
			*
		FROM 
			buildlist
		WHERE  
			buildlist_entity_id='".$cp->id()."';";
		
		$blres = dbquery($sql);
		$builing_something=false;
		while ($blarr = mysql_fetch_array($blres))
		{
			$buildlist[$blarr['buildlist_building_id']]=$blarr;
			if ($blarr['buildlist_build_type']>2)
			{ 
				$builing_something=true;
			}
		}

		// Technologieliste laden
		$tres = dbquery("
		SELECT 
			* 
		FROM 
			techlist
		WHERE 
			techlist_user_id='".$cu->id."'
		;");
		while ($tarr = mysql_fetch_array($tres))
		{
			$techlist[$tarr['techlist_tech_id']]=$tarr['techlist_current_level'];
		}

		// Load gene technology level
		$tl = new TechList($cu->id);
		define("GEN_TECH_LEVEL",$tl->getLevel(GEN_TECH_ID));
		$minBuildTimeFactor = (0.1-(GEN_TECH_LEVEL/100));
	
		// Load working people data
		$bl = new BuildList($cp->id(),$cu->id);
		$peopleWorking = $bl->getPeopleWorking(BUILD_BUILDING_ID);	
		$peopleTimeReduction = $cfg->value('people_work_done');
		$peopleFoodConsumption = $cfg->value('people_food_require');
	
	
		// Requirements
		$rres = dbquery("
		SELECT 
			* 
		FROM 
			building_requirements
		;");
		while ($rarr = mysql_fetch_array($rres))
		{
			if ($rarr['req_building_id']>0) 
			{
				$b_req[$rarr['obj_id']]['b'][$rarr['req_building_id']]=$rarr['req_level'];
			}
			
			if ($rarr['req_tech_id']>0) 
			{
				$b_req[$rarr['obj_id']]['t'][$rarr['req_tech_id']]=$rarr['req_level'];
			}
		}

		// TODO: Use def queue
		/*
		// Felder von bauender Def laden
		$res_def =	dbquery("
		SELECT 
			SUM(d.def_fields * dl.deflist_build_count) AS planet_def_fields_needed 
		FROM 
			defense AS d
			INNER JOIN
			deflist AS dl
			ON
			d.def_id = dl.deflist_def_id
		WHERE
			dl.deflist_entity_id='".$cp->id()."';");
		if(mysql_num_rows($res_def)>0)
		{
			$arr=mysql_fetch_array($res_def);
			if ($arr['planet_def_fields_needed']>0)
			{
				$def_field_needed = $arr['planet_def_fields_needed'];
    	}
    	else
    	{
    		$def_field_needed = 0;
    	}
    }
    else
    {
    	$def_field_needed = 0;
    }*/
    $def_field_needed = 0;

  	iBoxStart("Bauhof-Infos");
  	echo "<div style=\"text-align:left;\">";
	if ($cu->specialist->buildTime!=1) {
		echo "<b>Bauzeitverringerung durch ".$cu->specialist->name.":</b> ".get_percent_string($cu->specialist->buildTime)."<br>";
	}
  	echo "<b>Eingestellte Arbeiter:</b> ".nf($peopleWorking)."<br/>
  	<b>Zeitreduktion durch Arbeiter pro Auftrag:</b> ".tf($peopleTimeReduction*$peopleWorking)."<br/>
  	<b>Nahrungsverbrauch durch Arbeiter pro Auftrag:</b> ".nf($peopleFoodConsumption*$peopleWorking)."<br/>
  	<b>Gentechnologie:</b> ".GEN_TECH_LEVEL."<br/>
  	<b>Minimale Bauzeit (mit Arbeiter):</b> Bauzeit * ".$minBuildTimeFactor;
	if ($cu->specialist->costsBuilding!=1)
	{
		echo "<br/><br/><b>Kostenreduktion durch ".$cu->specialist->name.":</b> ".get_percent_string($cu->specialist->costsBuilding);
	}
  	echo "</div>";   	
  	iBoxEnd();


/********************
* Gebäudedetail     *
********************/

		//Gebäude ausbauen/abreissen/abbrechen
		if ((isset($_GET['id']) && $_GET['id'] >0) || (count($_POST)>0 && checker_verify()))
		{
			$bid = 0;
			if (isset($_GET['id']) && $_GET['id'] >0)
			{
				$bid = $_GET['id'];
			}
			else
			{
				foreach ($_POST as $k => $v)
				{
					if(stristr($k,'_x'))
					{
						$bid = eregi_replace('show_([0-9]+)_x', '\1', $k);
						break;
					}
				}
				if ($bid==0 && isset($_POST['show']))
				{
					$bid = $_POST['show'];
				}
				if ($bid==0 && isset($_POST['id']))
				{
					$bid = $_POST['id'];
				}			
			}
			
			// Gebäudedaten laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				buildings 
			WHERE 
				building_show='1' 
				AND building_id='".$bid."';");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				
				// Prüft, ob Gebäude schon gebaut wurde und setzt Variablen
				if(isset($buildlist[$arr['building_id']]))
				{
					$built = true;
					
					$b_level = $buildlist[$arr['building_id']]['buildlist_current_level'];
					$b_status = $buildlist[$arr['building_id']]['buildlist_build_type'];
					$start_time = $buildlist[$arr['building_id']]['buildlist_build_start_time'];
					$end_time = $buildlist[$arr['building_id']]['buildlist_build_end_time'];
				}
				// Gebäude wurde noch nicht gebaut. Es werden Default Werte vergeben
				else
				{
					$built = false;
					
					$b_level = 0;
					$b_status=0;
					$start_time = 0;
					$end_time = 0;
				}
					

        $bc = calcBuildingCosts($arr,$b_level,$cu->specialist->costsBuilding);
        $bcn = calcBuildingCosts($arr,$b_level+1,$cu->specialist->costsBuilding);
				$dc = calcDemolishingCosts($arr, $bc,$cu->specialist->costsBuilding);

				// Bauzeit
				$bonus = $cu->race->buildTime + $cp->typeBuildtime + $cp->starBuildtime + $cu->specialist->buildTime - 3;

				$btime = ($bc['metal']+$bc['crystal']+$bc['plastic']+$bc['fuel']+$bc['food']) / GLOBAL_TIME * BUILD_BUILD_TIME;
				$btime *= $bonus;

				$btimen = ($bcn['metal']+$bcn['crystal']+$bcn['plastic']+$bcn['fuel']+$bcn['food']) / GLOBAL_TIME * BUILD_BUILD_TIME;
				$btimen  *= $bonus;

				$dtime = ($dc['metal']+$dc['crystal']+$dc['plastic']+$dc['fuel']+$dc['food']) / GLOBAL_TIME * BUILD_BUILD_TIME;
				$dtime  *= $bonus;

				if ($peopleWorking > 0)
				{
					$btime_min = $btime * $minBuildTimeFactor;
					$btime = $btime-($peopleWorking * $peopleTimeReduction);
					if ($btime < $btime_min) 
						$btime = $btime_min;
					$bc['food']+= $peopleWorking * $peopleFoodConsumption;
				}


				//
				// Befehle ausführen
				//

				//Gebäude ausbauen
				if (isset($_POST['command_build']) && $b_status==0)
				{
					if (!$builing_something)
					{

						if ($cp->fields_used+$arr['building_fields']+$def_field_needed <= $cp->fields+$cp->fields_extra || $arr['building_fields']==0)
						{
							if ($cp->resMetal >= $bc['metal'] && $cp->resCrystal >= $bc['crystal'] && $cp->resPlastic >= $bc['plastic']  && $cp->resFuel >= $bc['fuel']  && $cp->resFood >= $bc['food'])
							{
								$start_time = time();
								$end_time = time()+$btime;
								
								//Gebäude bereits vorhanden
								if (isset($buildlist[$arr['building_id']])>0)
								{
									dbquery("
									UPDATE 
										buildlist 
									SET
										buildlist_build_type='3',
										buildlist_build_start_time='".time()."',
										buildlist_build_end_time='".$end_time."'
									WHERE
										buildlist_entity_id='".$cp->id()."'
										AND buildlist_building_id='".$arr['building_id']."';");
								}
								//Gebäude noch nicht vorhanden
								else
								{
									dbquery("
									INSERT INTO 
									buildlist 
									(
										buildlist_build_type,
										buildlist_build_start_time,
										buildlist_build_end_time,
										buildlist_building_id,
										buildlist_user_id,
										buildlist_entity_id
									) 
									VALUES 
									( 
										'3',
										'".time()."',
										'".$end_time."',
										'".$arr['building_id']."',
										'".$cu->id."',
										'".$cp->id()."'
									);");

								}
								
								//Rohstoffe vom Planeten abziehen und aktualisieren
								$cp->changeRes(-$bc['metal'],-$bc['crystal'],-$bc['plastic'],-$bc['fuel'],-$bc['food']);
								$b_status=3;
								
								
								//Log schreiben
								$log_text = "
								<b>Gebäude Ausbau</b><br><br>
								<b>User:</b> [USER_ID=".$cu->id.";USER_NICK=".$cu->nick."]<br>
								<b>Planeten:</b> [PLANET_ID=".$cp->id().";PLANET_NAME=".$cp->name."]<br>
								<b>Gebäude:</b> ".$arr['building_name']."<br>
								<b>Gebäude Level:</b> ".$b_level." (vor Ausbau)<br>
								<b>Bau dauer:</b> ".tf($btime)."<br>
								<b>Ende:</b> ".date("Y-m-d H:i:s",$end_time)."<br>
								<b>Eingesetzte Bewohner:</b> ".nf($peopleWorking)."<br>
								<b>Gen-Tech Level:</b> ".GEN_TECH_LEVEL."<br>
								<b>Eingesetzter Spezialist:</b> ".$cu->specialist->name."<br><br>
								<b>Kosten</b><br>
								<b>".RES_METAL.":</b> ".nf($bc['metal'])."<br>
								<b>".RES_CRYSTAL.":</b> ".nf($bc['crystal'])."<br>
								<b>".RES_PLASTIC.":</b> ".nf($bc['plastic'])."<br>
								<b>".RES_FUEL.":</b> ".nf($bc['fuel'])."<br>
								<b>".RES_FOOD.":</b> ".nf($bc['food'])."<br><br>
								<b>Restliche Rohstoffe auf dem Planeten</b><br><br>
								<b>".RES_METAL.":</b> ".nf($cp->resMetal)."<br>
								<b>".RES_CRYSTAL.":</b> ".nf($cp->resCrystal)."<br>
								<b>".RES_PLASTIC.":</b> ".nf($cp->resPlastic)."<br>
								<b>".RES_FUEL.":</b> ".nf($cp->resFuel)."<br>
								<b>".RES_FOOD.":</b> ".nf($cp->resFood)."<br><br>
								";
								
								//Log Speichern
								add_log_game_building($log_text,$cu->id,$cu->allianceId,$cp->id(),$arr['building_id'],$b_status,time());
								
							}
							else
								error_msg("Bauauftrag kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!");
						}
						else
							error_msg("Bauauftrag kann nicht gestartet werden, zuwenig Felder vorhanden!");
					}
					else
						error_msg("Bauauftrag kann nicht gestartet werden, es wird bereits an einem Geb&auml;ude gearbeitet!");
				}

				//Gebäude abbrechen
				if (isset($_POST['command_demolish']) && $b_status==0)
				{
					if (!$builing_something)
					{
						if ($cp->resMetal >= $dc['metal'] && $cp->resCrystal >= $dc['crystal'] && $cp->resPlastic >= $dc['plastic']  && $cp->resFuel >= $dc['fuel']  && $cp->resFood >= $dc['food'])
						{
							$end_time = time()+$dtime;
							$start_time = time();
							dbquery("
							UPDATE 
								buildlist 
							SET
								buildlist_build_type='4',
								buildlist_build_start_time='".time()."',
								buildlist_build_end_time='".$end_time."'
							WHERE 
								buildlist_entity_id='".$cp->id()."'
								AND buildlist_building_id='".$arr['building_id']."';");
								
							//Rohstoffe vom Planeten abziehen und aktualisieren
							$cp->changeRes(-$dc['metal'],-$dc['crystal'],-$dc['plastic'],-$dc['fuel'],-$dc['food']);
							$b_status=4;
							
							
							//Log schreiben
							$log_text = "
							<b>Gebäude Abriss</b><br><br>
							<b>User:</b> [USER_ID=".$cu->id.";USER_NICK=".$cu->nick."]<br>
							<b>Planeten:</b> [PLANET_ID=".$cp->id().";PLANET_NAME=".$cp->name."]<br>
							<b>Gebäude:</b> ".$arr['building_name']."<br>
							<b>Gebäude Level:</b> ".$b_level." (vor Abriss)<br>
							<b>Abriss dauer:</b> ".tf($dtime)."<br>
							<b>Ende:</b> ".date("Y-m-d H:i:s",$end_time)."<br><br>
							<b>Kosten</b><br>
							<b>".RES_METAL.":</b> ".nf($dc['metal'])."<br>
							<b>".RES_CRYSTAL.":</b> ".nf($dc['crystal'])."<br>
							<b>".RES_PLASTIC.":</b> ".nf($dc['plastic'])."<br>
							<b>".RES_FUEL.":</b> ".nf($dc['fuel'])."<br>
							<b>".RES_FOOD.":</b> ".nf($dc['food'])."<br><br>
							<b>Restliche Rohstoffe auf dem Planeten</b><br><br>
							<b>".RES_METAL.":</b> ".nf($cp->resMetal)."<br>
							<b>".RES_CRYSTAL.":</b> ".nf($cp->resCrystal)."<br>
							<b>".RES_PLASTIC.":</b> ".nf($cp->resPlastic)."<br>
							<b>".RES_FUEL.":</b> ".nf($cp->resFuel)."<br>
							<b>".RES_FOOD.":</b> ".nf($cp->resFood)."<br><br>
							";
							
							//Log Speichern
							add_log_game_building($log_text,$cu->id,$cu->allianceId,$cp->id(),$arr['building_id'],$b_status,time());	
							
						}
						else
							error_msg("Abbruchauftrag kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!");;
					}
					else
						error_msg("Abbruchauftrag kann nicht gestartet werden, es wird bereits an einem Geb&auml;ude gearbeitet!");
				}

				//Bauauftrag abbrechen
				if (isset($_POST['command_cbuild']) && $b_status==3)
				{
					if ($buildlist[$arr['building_id']]['buildlist_build_end_time'] > time())
					{
						$fac = ($end_time-time())/($end_time-$start_time);
						dbquery("
						UPDATE 
							buildlist 
						SET
							buildlist_build_type=0,
							buildlist_build_start_time=0,
							buildlist_build_end_time=0
						WHERE 
							buildlist_entity_id='".$cp->id()."'
							AND buildlist_building_id='".$arr['building_id']."';");
							
						//Rohstoffe vom Planeten abziehen und aktualisieren
						$cp->changeRes($bc['metal']*$fac,$bc['crystal']*$fac,$bc['plastic']*$fac,$bc['fuel']*$fac,$bc['food']*$fac);
						$b_status=0;
						$builing_something=false;
						
						//Log schreiben
						$log_text = "
						<b>Gebäudebau Abbruch</b><br><br>
						<b>User:</b> [USER_ID=".$cu->id.";USER_NICK=".$cu->nick."]<br>
						<b>Planeten:</b> [PLANET_ID=".$cp->id().";PLANET_NAME=".$cp->name."]<br>
						<b>Gebäude:</b> ".$arr['building_name']."<br>
						<b>Gebäude Level:</b> ".$b_level." (nach Abbruch)<br>
						<b>Start des Gebädes:</b> ".date("Y-m-d H:i:s",$start_time)."<br>
						<b>Ende des Gebädes:</b> ".date("Y-m-d H:i:s",$end_time)."<br><br>
						<b>Erhaltene Rohstoffe</b><br>
						<b>Faktor:</b> ".$fac."<br>
						<b>".RES_METAL.":</b> ".nf($bc['metal']*$fac)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($bc['crystal']*$fac)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($bc['plastic']*$fac)."<br>
						<b>".RES_FUEL.":</b> ".nf($bc['fuel']*$fac)."<br>
						<b>".RES_FOOD.":</b> ".nf($bc['food']*$fac)."<br><br>
						<b>Rohstoffe auf dem Planeten</b><br><br>
						<b>".RES_METAL.":</b> ".nf($cp->resMetal)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($cp->resCrystal)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($cp->resPlastic)."<br>
						<b>".RES_FUEL.":</b> ".nf($cp->resFuel)."<br>
						<b>".RES_FOOD.":</b> ".nf($cp->resFood)."<br><br>
						";
						
						//Log Speichern
						add_log_game_building($log_text,$cu->id,$cu->allianceId,$cp->id(),$arr['building_id'],$b_status,time());								
					}
					else
						error_msg("Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!");
				}

				//Abbruchauftrag abbrechen
				if (isset($_POST['command_cdemolish']) && $b_status==4)
				{
					if ($buildlist[$arr['building_id']]['buildlist_build_end_time'] > time())
					{
						$fac = ($end_time-time())/($end_time-$start_time);
						dbquery("
						UPDATE 
							buildlist 
						SET
							buildlist_build_type=0,
							buildlist_build_start_time=0,
							buildlist_build_end_time=0
						WHERE 
							buildlist_entity_id='".$cp->id()."'
							AND buildlist_building_id='".$arr['building_id']."';");
						
						//Rohstoffe vom Planeten abziehen und aktualisieren
						$cp->changeRes($dc['metal']*$fac,$dc['crystal']*$fac,$dc['plastic']*$fac,$dc['fuel']*$fac,$dc['food']*$fac);
						$b_status=0;
						$builing_something=false;
						
						//Log schreiben
						$log_text = "
						<b>Gebäudeabbruch Abbruch</b><br><br>
						<b>User:</b> [USER_ID=".$cu->id.";USER_NICK=".$cu->nick."]<br>
						<b>Planeten:</b> [PLANET_ID=".$cp->id().";PLANET_NAME=".$cp->name."]<br>
						<b>Gebäude:</b> ".$arr['building_name']."<br>
						<b>Gebäude Level:</b> ".$b_level." (nach Abbruch)<br>
						<b>Start des Gebädes:</b> ".date("Y-m-d H:i:s",$start_time)."<br>
						<b>Ende des Gebädes:</b> ".date("Y-m-d H:i:s",$end_time)."<br><br>
						<b>Erhaltene Rohstoffe</b><br>
						<b>Faktor:</b> ".$fac."<br>
						<b>".RES_METAL.":</b> ".nf($dc['metal']*$fac)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($dc['crystal']*$fac)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($dc['plastic']*$fac)."<br>
						<b>".RES_FUEL.":</b> ".nf($dc['fuel']*$fac)."<br>
						<b>".RES_FOOD.":</b> ".nf($dc['food']*$fac)."<br><br>
						<b>Rohstoffe auf dem Planeten</b><br><br>
						<b>".RES_METAL.":</b> ".nf($cp->resMetal)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($cp->resCrystal)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($cp->resPlastic)."<br>
						<b>".RES_FUEL.":</b> ".nf($cp->resFuel)."<br>
						<b>".RES_FOOD.":</b> ".nf($cp->resFood)."<br><br>
						";
						
						//Log Speichern
						add_log_game_building($log_text,$cu->id,$cu->allianceId,$cp->id(),$arr['building_id'],$b_status,time());							
					}
					else
						error_msg("Abbruchauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!");
				}


				if ($b_status==3 && $b_level>0)
				{
					$color="color:#0f0;";
					$status_text="Wird ausgebaut";
				}
				elseif ($b_status==32)
				{
					$color="color:#0f0;";
					$status_text="Wird gebaut";
				}
				elseif ($b_status==4)
				{
					$color="color:#f80;";
					$status_text="Wird abgerissen";
				}
				else
				{
					$color="";
					$status_text="Unt&auml;tig";
				}

				//
				// Gebäudedaten anzeigen
				//
				$path = IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id'].".".IMAGE_EXT;
				$title = $arr['building_name'].' <span id="buildlevel">';
				$title.= $b_level > 0 ? $b_level : '';
				$title.= '</span>';
				tableStart($title);
				echo "<tr>
                  <td rowspan=\"4\" style=\"width:220px;background:#000;vertical-align:middle;\">
                 		".helpImageLink("buildings&amp;id=".$arr['building_id'],$path,$arr['building_name'],"width:220px;height:220px")."
                  </td>
                  <td colspan=\"2\" style=\"vertical-align:top;height:150px;\">
                  	".$arr['building_longcomment']."
                 	</td>
				     </tr>";
       	$f = $arr['building_fields'];
				echo "<tr>
                  <th style=\"width:250px;height:20px;\">Platzverbrauch pro Ausbaustufe:</th>
                  <td>".$f." ".($f!=1 ? 'Felder' : 'Feld')."</td>
      	</tr>";
      	$f = $arr['building_fields'] * $b_level;
				echo "<tr>
                  <th style=\"width:250px;height:20px;\">Platzverbrauch total:</th>
       						<td>".$f." ".($f!=1 ? 'Felder' : 'Feld')."</td>
						</tr>";
				echo "<tr>
                  <th style=\"width:250px;height:20px;\">Status:</th>
                  <td style=\"".$color."\" id=\"buildstatus\" >$status_text</td>
				     </tr>";
				tableEnd();


				// Check requirements for this building
				$requirements_passed = true;
				$bid = $arr['building_id'];
				if (isset($b_req[$bid]['b']) && count($b_req[$bid]['b'])>0)
				{
					foreach ($b_req[$bid]['b'] as $b => $l)
					{
						if (!isset($buildlist[$b]['buildlist_current_level']) || $buildlist[$b]['buildlist_current_level']<$l)
						{
							$requirements_passed = false;
						}
					}
				}								
				if (isset($b_req[$bid]['t']) && count($b_req[$bid]['t'])>0)
				{
					foreach ($b_req[$bid]['t'] as $id => $level)
					{
						if (!isset($techlist[$id]) || $techlist[$id]<$level)
						{
							$requirements_passed = false;
						}
					}
				}


				//
				// Baumenü
				//
				echo "<form action=\"?page=$page\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"".$arr['building_id']."\">";
        checker_init();
        
        // Voraussetzungen sind erfüllt
        if ($requirements_passed)
        {
					tableStart("Bauoptionen");
					echo "<tr>
	                <th width=\"16%\">Aktion</td>
	                <th width=\"14%\">".RES_ICON_TIME." Zeit</th>
	                <th width=\"14%\">".RES_ICON_METAL."</td>
	                <th width=\"14%\">".RES_ICON_CRYSTAL."</td>
	                <th width=\"14%\">".RES_ICON_PLASTIC."</td>
	                <th width=\"14%\">".RES_ICON_FUEL."</td>
	                <th width=\"14%\">".RES_ICON_FOOD."</td>
	                <th width=\"14%\">".RES_ICON_POWER."</td>
							</tr>";
	
					// Bauen
					if ($b_status==0)
					{
						$bWaitArray = calcBuildingWaitTime($bc,$cp);
	
						// Maximale Stufe erreicht
						if ($b_level>=$arr['building_last_level'])
						{
							echo "<tr>
											<td colspan=\"8\">
												<i>Kein weiterer Ausbau m&ouml;glich.</i>
											</td>
										</tr>";
						}
						// Es wird bereits an einem Gebäude gebaut
						elseif ($builing_something)
						{
							echo "<tr>
											<td style=\"color:red;\">Bauen</td>
											<td>".tf($btime)."</td>";
							echo $bWaitArray[0];
							echo "<tr>
											<td colspan=\"8\">
												<i>Es kann nichts gebaut werden da gerade an einem anderen Geb&auml;ude gearbeitet wird!</i>
											</td>
										</tr>";
						}
						// Zuwenig Felder vorhanden
						elseif ($arr['building_fields']>0 && ($cp->fields_used+$arr['building_fields']+$def_field_needed > $cp->fields+$cp->fields_extra))
						{
							echo "<tr>
											<td style=\"color:red;\">Bauen</td>
											<td>".tf($btime)."</td>";
							echo $bWaitArray[0];
							echo "<tr>
											<td colspan=\"8\">
												<i>Kein Ausbau m&ouml;glich, da es zuwenig Platz (Total: ".($cp->fields+$cp->fields_extra).", reserviert: ".($cp->fields_used+$def_field_needed).", benötigt: ".$arr['building_fields'].") f&uuml;r dieses Geb&auml;ude hat!</i>
											</td>
										</tr>";
						}
						// Zuwenig Rohstoffe vorhanden
						elseif ($cp->resMetal < $bc['metal'] || 
						$cp->resCrystal < $bc['crystal']  || 
						$cp->resPlastic < $bc['plastic']  || 
						$cp->resFuel < $bc['fuel']  || 
						$cp->resFood < $bc['food'] || 
						($cp->prodPower - $cp->usePower < $bc['power'] && $bc['power']>0)
						)
						{
							echo "<tr>
											<td style=\"color:red;\">Bauen</td>
											<td>".tf($btime)."</td>";
							echo $bWaitArray[0];
							echo "<tr>
											<td colspan=\"8\">
												<i>Kein Ausbau m&ouml;glich, zuwenig Rohstoffe!</i>
											</td>
										</tr>";
						}
						else
						{
							// Bauen
							if ($b_level==0)
							{
								echo "<tr>
												<td>
													<input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Bauen\"
												</td>
												<td>".tf($btime)."</td>";
							}
							// Ausbauen
							else
							{
								echo "<tr>
												<td>
													<input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Ausbauen\">
												</td>
												<td>".tf($btime)."</td>";
							}
							
									echo "<td>".nf($bc['metal'])."</td>
												<td>".nf($bc['crystal'])."</td>
												<td>".nf($bc['plastic'])."</td>
												<td>".nf($bc['fuel'])."</td>
												<td>".nf($bc['food'])."</td>
												<td>".nf($bc['power'])."</td>
											</tr>";
						}
					}
	
					// Abreissen
					if ($b_level>0 && $arr['building_demolish_costs_factor']!=0 && $b_status==0)
					{
						$dWaitArray = calcDemolishingWaitTime($dc,$cp);
						// Es wird bereits an einem Gebäude gebaut
						if ($builing_something)
						{
							echo "<tr>
											<td style=\"color:red;\">Abreissen</td>
											<td>".tf($dtime)."</td>";
							echo $dWaitArray[0];
							echo "<tr>
											<td colspan=\"8\">
													<i>Kein Abriss m&ouml;glich, es wird gerade an einem anderen Geb&auml;ude gearbeitet!</i>
											</td>
										</tr>";
						}
						// Zuwenig Rohstoffe
						elseif ($cp->resMetal < $dc['metal'] || 
						$cp->resCrystal < $dc['crystal']  || 
						$cp->resPlastic < $dc['plastic']  || 
						$cp->resFuel < $dc['fuel']  || 
						$cp->resFood < $dc['food'] || 
						($cp->prodPower - $cp->usePower < $dc['power'] && $dc['power']>0)
						)
						{
							echo "<tr>
											<td style=\"color:red;\">Abreissen</td>
											<td>".tf($dtime)."</td>";
							echo $dWaitArray[0];
							echo "<tr>
											<td colspan=\"8\">
												<i>Kein Abriss m&ouml;glich, zuwenig Rohstoffe!</i>
											</td>
										</tr>";
						}
						else
						{
							echo "<tr>
											<td>
												<input type=\"submit\" class=\"button\" name=\"command_demolish\" value=\"Abreissen\">
											</td>
											<td>".tf($dtime)."</td>
											<td>".nf($dc['metal'])."</td>
											<td>".nf($dc['crystal'])."</td>
											<td>".nf($dc['plastic'])."</td>
											<td>".nf($dc['fuel'])."</td>
											<td>".nf($dc['food'])."</td>
											<td>".nf($dc['power'])."</td>
										</tr>";
						}
					}
	
					// Bau abbrechen
					if ($b_status==3)
					{
		      	echo "<tr>
		      					<td id=\"buildcancel\">
		      						<input type=\"submit\" class=\"button\" name=\"command_cbuild\" value=\"Bau abbrechen\" onclick=\"if (this.value=='Bau abbrechen'){return confirm('Wirklich abbrechen?');}\" />
		      					</td>
		      					<td id=\"buildtime\">-</td>
		      					<td colspan=\"6\" id=\"buildprogress\" style=\"height:25px;background:#fff;text-align:center;\"></td>
		      				</tr>";
		      	if ($b_level < $arr['building_last_level']-1)
		      	{
		         	echo "<tr>
		         					<td width=\"90\">N&auml;chste Stufe:</td>
		         					<td>".tf($btimen)."</td>
		         					<td>".nf($bcn['metal'])."</td>
		         					<td>".nf($bcn['crystal'])."</td>
		         					<td>".nf($bcn['plastic'])."</td>
		         					<td>".nf($bcn['fuel'])."</td>
		         					<td>".nf($bcn['food'])."</td>
		         					<td>".nf($bcn['power'])."</td>
		         				</tr>";
		         }
					}
	
					// Abriss abbrechen
					if ($b_status==4)
					{
		      	echo "<tr>
		      					<td id=\"buildcancel\">
		      						<input type=\"submit\" class=\"button\" name=\"command_cdemolish\" value=\"Abriss abbrechen\" onclick=\"if (this.value=='Abriss abbrechen'){return confirm('Wirklich abbrechen?');}\" />
		      					</td>
		      					<td id=\"buildtime\">-</td>
		      					<td colspan=\"6\"  id=\"buildprogress\" style=\"height:25px;background:#fff;text-align:center;\"></td>
		      				</tr>";
					}
					tableEnd();
					
					
	
					if (isset($bWaitArray) && $bWaitArray[1]>0)
					{
						echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Bau vorhanden sind: <b>".tf($bWaitArray[1])."</b><br/>";
					}
					if (isset($dWaitArray) && $dWaitArray[1]>0)
					{
						echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Abriss vorhanden sind: <b>".tf($dWaitArray[1])."</b><br/>";
					}
					echo "<br/>";
	
					if ($b_status==3 || $b_status==4)
					{
						countDown("buildtime",$end_time,"buildcancel");
						jsProgressBar("buildprogress",$start_time,$end_time);
					}
				
				}
				else
				{
					echo '<div>Gebäude kann nicht (aus)gebaut werden, <a href="?page=techtree">Voraussetzungen</a> nicht erfüllt!<br/><br/></div>';
				}

				echo "<input type=\"submit\" name=\"command_show\" value=\"Aktualisieren\" /> &nbsp; ";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
				echo "</form>";
			}
			else {
				error_msg("Geb&auml;ude nicht vorhanden!");
				return_btn();
			}
		}

/********************
* Übersicht         *
********************/

		else
		{
	
		
			$tres = dbquery("
			SELECT
				*
			FROM
        building_types
			ORDER BY
				type_order ASC
			;");				
			if (mysql_num_rows($tres)>0)
			{

				// Gebäude laden
				$bres = dbquery("
				SELECT
					building_type_id,
					building_id,
					building_name,
					building_last_level,
					building_shortcomment,
					building_costs_metal,
					building_costs_crystal,
					building_costs_plastic,
					building_costs_fuel,
					building_costs_food,
					building_costs_power,
					building_build_costs_factor,
					building_show
				FROM
					buildings
				WHERE
					building_show='1'
				ORDER BY
					building_order,
					building_name
				;");	
				$building = array();
				if (mysql_num_rows($bres)>0)			
				{
					while ($barr = mysql_Fetch_Array($bres))
					{
						$tid = $barr['building_type_id'];
						$bid = $barr['building_id'];
						$building[$tid][$bid]['name'] = $barr['building_name'];
						$building[$tid][$bid]['last_level'] = $barr['building_last_level'];
						$building[$tid][$bid]['shortcomment'] = $barr['building_shortcomment'];
						$building[$tid][$bid]['building_costs_metal'] = $barr['building_costs_metal'];
						$building[$tid][$bid]['building_costs_crystal'] = $barr['building_costs_crystal'];
						$building[$tid][$bid]['building_costs_plastic'] = $barr['building_costs_plastic'];
						$building[$tid][$bid]['building_costs_fuel'] = $barr['building_costs_fuel'];
						$building[$tid][$bid]['building_costs_food'] = $barr['building_costs_food'];
						$building[$tid][$bid]['building_costs_power'] = $barr['building_costs_power'];
						$building[$tid][$bid]['building_build_costs_factor'] = $barr['building_build_costs_factor'];
						$building[$tid][$bid]['show'] = $barr['building_show'];
					}
				}
				
				// Jede Kategorie durchgehen
				$cstr=checker_init();
				echo "<form action=\"?page=$page\" method=\"post\"><div>";
				echo $cstr;

				while ($tarr = mysql_fetch_array($tres))
				{
					tableStart($tarr['type_name'],'','padding:0px;');
					echo "<tr style=\"padding:0px;\"><td style=\"padding:0px;\">";

						$cnt = 0; // Counter for current row
						$scnt = 0; // Counter for shown buildings

						$bdata = $building[$tarr['type_id']];
						if (isset($bdata) && count($bdata)>0)
						{
							foreach ($bdata as $bid => $bv)
							{
								// Aktuellen Level feststellen
								if(isset($buildlist[$bid]['buildlist_current_level']))
								{
									$b_level = intval($buildlist[$bid]['buildlist_current_level']);
									$end_time = intval($buildlist[$bid]['buildlist_build_end_time']);
									$start_time = intval($buildlist[$bid]['buildlist_build_start_time']);
								}
								else
								{
									$b_level=0;
									$end_time=0;
								}


								// Check requirements for this building
								$requirements_passed = true;
								$b_req_info = array();
								$t_req_info = array();
								if (isset($b_req[$bid]['b']) && count($b_req[$bid]['b'])>0)
								{
									foreach ($b_req[$bid]['b'] as $b => $l)
									{
										if (!isset($buildlist[$b]['buildlist_current_level']) || $buildlist[$b]['buildlist_current_level']<$l)
										{
											$b_req_info[] = array($b,$l,false);
											$requirements_passed = false;
										}
										else
											$b_req_info[] = array($b,$l,true);
									}
								}								
								if (isset($b_req[$bid]['t']) && count($b_req[$bid]['t'])>0)
								{
									foreach ($b_req[$bid]['t'] as $id => $level)
									{
										if (!isset($techlist[$id]) || $techlist[$id]<$level)
										{
											$requirements_passed = false;
											$t_req_info[] = array($id,$level,false);
										}
										else
											$t_req_info[] = array($id,$level,true);
									}
								}

								// Voraussetzungen nicht erfüllt
								if (!$requirements_passed)
								{
									$subtitle =  'Voraussetzungen fehlen';
									$tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Gebäude zu bauen!</span><br/>';
									foreach ($b_req_info as $v)
									{
										$b = new Building($v[0]);
										$tmtext .= "<div style=\"color:".($v[2]?'#0f0':'#f30')."\">".$b." Stufe ".$v[1]."</div>";
										unset($b);
									}
									foreach ($t_req_info as $v)
									{
										$b = new Technology($v[0]);
										$tmtext .= "<div style=\"color:".($v[2]?'#0f0':'#f30')."\">".$b." Stufe ".$v[1]."</div>";
										unset($b);
									}
									
									$color = '#999';
									if($use_img_filter)
									{
										$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."&filter=na";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."";
									}							
									
								}
								// Ist im Bau
								elseif (isset($buildlist[$bid]['buildlist_build_type']) && $buildlist[$bid]['buildlist_build_type']==3)
								{
									$subtitle =  "Ausbau auf Stufe ".($b_level+1);
									$tmtext = "<span style=\"color:#0f0\">Wird ausgebaut<br/>Dauer: ".tf($end_time-time())."</span><br/>";
									$color = '#0f0';
									if($use_img_filter)
									{
										$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."&filter=building";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."";
									}
								}
								// Wird abgerissen
								elseif (isset($buildlist[$bid]['buildlist_build_type']) && $buildlist[$bid]['buildlist_build_type']==4)
								{
									$subtitle = "Abriss auf Stufe ".($b_level-1);
									$tmtext = "<span style=\"color:#f90\">Wird abgerissen!<br/>Dauer: ".tf($end_time-time())."</span><br/>";
									$color = '#f90';
									if($use_img_filter)
									{
										$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."&filter=destructing";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."";
									}
								}
								// Untätig
								else
								{
									// Zuwenig Ressourcen
									
                	$bc = calcBuildingCosts($bv,$b_level,$cu->specialist->costsBuilding);
									if($b_level<$bv['last_level'] && $cp->resMetal < $bc['metal'] || $cp->resCrystal < $bc['crystal']  || $cp->resPlastic < $bc['plastic']  || $cp->resFuel < $bc['fuel']  || $cp->resFood < $bc['food'])
									{
										$tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r weiteren Ausbau!</span><br/>";
										$color = '#f00';
										
										if($use_img_filter)
										{
											$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."&filter=lowres";
										}
										else
										{
											$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."";
										}
									}
									else
									{
										$tmtext = "";
										$color = '#fff';
										$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid.".".IMAGE_EXT."";
									}
									
									if ($b_level==0)
									{
										$subtitle = "Noch nicht gebaut";
									}
									elseif ($b_level>=$bv['last_level'])
									{
										$subtitle = 'Vollständig ausgebaut';
									}
									else
									{
										$subtitle = 'Stufe '.$b_level;
									}
								}

								// Display all buildings that are buildable or are already built
								if (($bv['show']==1) || $b_level>0)
								{			
									$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$bid."_middle.".IMAGE_EXT."";

									if (!$requirements_passed)
										$img = "misc/imagefilter.php?file=$img&filter=req";

									echo "<div style=\"background:url('".$img."') no-repeat;background-position:center\" class=\"buildOverviewObjectContainer\"   ".tm($bv['name'],"<b>".$subtitle."</b><br/>".$tmtext.$bv['shortcomment']).">
									<div class=\"buildOverviewBGImage\">
									<a class=\"buildOverviewLink\" href=\"?page=$page&amp;id=".$bid."\">
									<div class=\"buildOverviewObjectTitle\">".$bv['name']."</div>";
									if ($b_level>0 || ($b_level==0 && isset($buildlist[$bid]['buildlist_build_type']) && $buildlist[$bid]['buildlist_build_type']==3)) 
									{
										echo "<div class=\"buildOverviewObjectLevel\" style=\"color:".$color."\">".$b_level."</div>";
									}
									echo "<div class=\"buildOverviewObjectSubTitle\">".$subtitle."</div>";
									echo "<div class=\"buildOverviewObjectImage\"><input type=\"image\" value=\"".$bid."\" src=\"".$img."\" /></div>";
									echo "</a>";
									echo "</div></div>";
									$cnt++;
									$scnt++;
								}
							}							
							
							if ($scnt==0)
							{								
								echo "<div style=\"text-align:center;border:0;width:100%\">
										<i>In dieser Kategorie kann momentan noch nichts gebaut werden!</i>
									</div>";							
							}						
						}
						else
						{
							echo "<div style=\"text-align:center;border:0;width:100%\">
									<i>In dieser Kategorie kann momentan noch nichts gebaut werden!</i>
								</div>";
						}
					echo "</td></tr>";
					tableEnd();
				}				
				echo '</div></form>';
			}
			else
			{
				error_msg("Es k&ouml;nnen noch keine Geb&auml;ude gebaut werden!");
			}
		}

	}
	// ENDE SKRIPT //

	?>
