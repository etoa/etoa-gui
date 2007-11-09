<?PHP

	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame						//
	// Ein Massive-Multiplayer-Online-Spiel					//
	// Programmiert von Nicolas Perrenoud						//
	// www.nicu.ch | mail@nicu.ch										//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// ---------------------------------------------//
	// Datei: buildings.php													//
	// Topic: Bauhof-Modul				 									//
	// Version: 0.1																	//
	// Letzte Änderung: 10.05.2006 Lamborghini			//
	//////////////////////////////////////////////////

  // DEFINITIONEN //

  define('NUM_BUILDINGS_PER_ROW',4);// Gebäude pro Reihe
  define('BUILD_BUILDING_ID',6);		// Bauhof
  define('CELL_WIDTH',175);					// Breite der Gebäudezelle in der Übersicht
	define('GEN_TECH_ID',23);				// ID der Gentechnologie

	if ($_SESSION[ROUNDID]['user']['image_filter']==1)
		$use_img_filter = true;
	else
		$use_img_filter = false;

/* This function has to be outsourced, of course, later */

function calcBuildingCosts($buildingArray, $level)
{
	$bc=array();
	$bc['metal'] = $buildingArray['building_costs_metal'] * pow($buildingArray['building_build_costs_factor'],$level);
	$bc['crystal'] = $buildingArray['building_costs_crystal'] * pow($buildingArray['building_build_costs_factor'],$level);
	$bc['plastic'] = $buildingArray['building_costs_plastic'] * pow($buildingArray['building_build_costs_factor'],$level);
	$bc['fuel'] = $buildingArray['building_costs_fuel'] * pow($buildingArray['building_build_costs_factor'],$level);
	$bc['food'] = $buildingArray['building_costs_food'] * pow($buildingArray['building_build_costs_factor'],$level);
	return $bc;
}

function calcBuildingWaitTime($bc,$c)
{
	// Wartezeiten auf Ressourcen berechnen
	if ($c->prod->metal>0) $bwait['metal']=ceil(($bc['metal']-$c->res->metal)/$c->prod->metal*3600);else $bwait['metal']=0;
	if ($c->prod->crystal>0) $bwait['crystal']=ceil(($bc['crystal']-$c->res->crystal)/$c->prod->crystal*3600);else $bwait['crystal']=0;
	if ($c->prod->plastic>0) $bwait['plastic']=ceil(($bc['plastic']-$c->res->plastic)/$c->prod->plastic*3600);else $bwait['plastic']=0;
	if ($c->prod->fuel>0) $bwait['fuel']=ceil(($bc['fuel']-$c->res->fuel)/$c->prod->fuel*3600);else $bwait['fuel']=0;
	if ($c->prod->food>0) $bwait['food']=ceil(($bc['food']-$c->res->food)/$c->prod->food*3600);else $bwait['food']=0;
	$bwmax=max($bwait['metal'],$bwait['crystal'],$bwait['plastic'],$bwait['fuel'],$bwait['food']);

	// Baukosten-String
	$bcstring.="<td";
	if ($bc['metal']>$c->res->metal)
		$bcstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff","<b>".nf($bc['metal']-$c->res->metal)."</b> ".RES_METAL."<br/>Bereit in <b>".tf($bwait['metal'])."</b>");
	else
		$bcstring.=" class=\"tbldata\"";
	$bcstring.= ">".nf($bc['metal'])."</td><td";
	if ($bc['crystal']>$c->res->crystal)
		$bcstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($bc['crystal']-$c->res->crystal)." ".RES_CRYSTAL."<br/>Bereit in <b>".tf($bwait['crystal'])."</b>");
	else
		$bcstring.=" class=\"tbldata\"";
	$bcstring.= ">".nf($bc['crystal'])."</td><td";
	if ($bc['plastic']>$c->res->plastic)
		$bcstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($bc['plastic']-$c->res->plastic)." ".RES_PLASTIC."<br/>Bereit in <b>".tf($bwait['plastic'])."</b>");
	else
		$bcstring.=" class=\"tbldata\"";
	$bcstring.= ">".nf($bc['plastic'])."</td><td";
	if ($bc['fuel']>$c->res->fuel)
		$bcstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($bc['fuel']-$c->res->fuel)." ".RES_FUEL."<br/>Bereit in <b>".tf($bwait['fuel'])."</b>");
	else
		$bcstring.=" class=\"tbldata\"";
	$bcstring.= ">".nf($bc['fuel'])."</td><td";
	if ($bc['food']>$c->res->food)
		$bcstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($bc['food']-$c->res->food)." ".RES_FOOD."<br/>Bereit in <b>".tf($bwait['food'])."</b>");
	else
		$bcstring.=" class=\"tbldata\"";
	$bcstring.= ">".nf($bc['food'])."</td></tr>";
	return array($bcstring,$bwmax);
}

function calcDemolishingCosts($buildingArray, $buildingCosts)
{
	$dc=array();
	// Abrisskostenberechnung				Abrisskosten = Baukosten  * Abrisskostenfaktor
	$dc['metal'] = $buildingCosts['metal'] * $buildingArray['building_demolish_costs_factor'];
	$dc['crystal'] = $buildingCosts['crystal'] * $buildingArray['building_demolish_costs_factor'];
	$dc['plastic'] = $buildingCosts['plastic'] * $buildingArray['building_demolish_costs_factor'];
	$dc['fuel'] = $buildingCosts['fuel'] * $buildingArray['building_demolish_costs_factor'];
	$dc['food'] = $buildingCosts['food'] * $buildingArray['building_demolish_costs_factor'];
	return $dc;
}

function calcDemolishingWaitTime($dc,$c)
{
	$dwait['metal']=ceil(($dc['metal']-$c->res->metal)/$c->prod->metal*3600);
	$dwait['crystal']=ceil(($dc['crystal']-$c->res->crystal)/$c->prod->crystal*3600);
	$dwait['plastic']=ceil(($dc['plastic']-$c->res->plastic)/$c->prod->plastic*3600);
	$dwait['fuel']=ceil(($dc['fuel']-$c->res->fuel)/$c->prod->fuel*3600);
	$dwait['food']=ceil(($dc['food']-$c->res->food)/$c->prod->food*3600);
	$dwmax=max($dwait['metal'],$dwait['crystal'],$dwait['plastic'],$dwait['fuel'],$dwait['food']);

	$dwstring= "<td";
	if ($dc['metal']>$c->res->metal)
		$dwstring.=" class=\"tbldata2\" ".tm("Fehlender Rohstoff","<b>".nf($dc['metal']-$c->res->metal)."</b> ".RES_METAL."<br/>Bereit in <b>".tf($dwait['metal'])."</b>");
	else
		$dwstring.=" class=\"tbldata\"";
	$dwstring.= ">".nf($dc['metal'])."</td><td";
	if ($dc['crystal']>$c->res->crystal)
		$dwstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($dc['crystal']-$c->res->crystal)." ".RES_CRYSTAL."<br/>Bereit in <b>".tf($dwait['crystal'])."</b>");
	else
		$dwstring.=" class=\"tbldata\"";
	$dwstring.= ">".nf($dc['crystal'])."</td><td";
	if ($dc['plastic']>$c->res->plastic)
		$dwstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($dc['plastic']-$c->res->plastic)." ".RES_PLASTIC."<br/>Bereit in <b>".tf($dwait['plastic'])."</b>");
	else
		$dwstring.=" class=\"tbldata\"";
	$dwstring.= ">".nf($dc['plastic'])."</td><td";
	if ($dc['fuel']>$c->res->fuel)
		$dwstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($dc['fuel']-$c->res->fuel)." ".RES_FUEL."<br/>Bereit in <b>".tf($dwait['fuel'])."</b>");
	else
		$dwstring.=" class=\"tbldata\"";
	$dwstring.= ">".nf($dc['fuel'])."</td><td";
	if ($dc['food']>$c->res->food)
		$dwstring.= " class=\"tbldata2\" ".tm("Fehlender Rohstoff",nf($dc['food']-$c->res->food)." ".RES_FOOD."<br/>Bereit in <b>".tf($dwait['food'])."</b>");
	else
		$dwstring.=" class=\"tbldata\"";
	$dwstring.= ">".nf($dc['food'])."</td></tr>";
	return array($dwstring,$dwmax);
}

	// SKRIPT //

	if ($planets->current)
	{
		echo "<h1>Bauhof des Planeten ".$c->name."</h1>";
		$c->resBox();

		// Gebäudeliste laden
		$blres = dbquery("
		SELECT 
			* 
		FROM 
		"	.$db_table['buildlist']." 
		WHERE 
			buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."' 
			AND buildlist_planet_id='".$c->id."';");
			
		$builing_something=false;
		while ($blarr = mysql_fetch_array($blres))
		{
			$buildlist[$blarr['buildlist_building_id']]=$blarr;
			if ($blarr['buildlist_build_type']!=0) $builing_something=true;
		}

		//Gentech level laden
		$tlres = dbquery("
		SELECT
			techlist_current_level
		FROM
			".$db_table['techlist']."
		WHERE
            techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
            AND techlist_tech_id='".GEN_TECH_ID."';");
		if(mysql_num_rows($tlres)>0)
		{
			$tlarr = mysql_fetch_array($tlres);
			define("GEN_TECH_LEVEL",$tlarr['techlist_current_level']);
    }
    else
    {
    	define("GEN_TECH_LEVEL",0);
    }

		// Felder von bauender Def laden
		$res_def =	dbquery("
		SELECT 
			SUM(d.def_fields * dl.deflist_build_count) AS planet_def_fields_needed 
		FROM 
			".$db_table['defense']." AS d
			INNER JOIN
			".$db_table['deflist']." AS dl
			ON
			d.def_id = dl.deflist_def_id 
			AND dl.deflist_planet_id='".$c->id."';");
		$arr=mysql_fetch_array($res_def);
		if ($arr['planet_def_fields_needed']>0)
		{
			$def_field_needed = $arr['planet_def_fields_needed'];
    }

/********************
* Gebäudedetail     *
********************/

		//Gebäude ausbauen/abreissen/abbrechen
		if (($_POST['submit_info']!="" || $_POST['command_build']!="" || $_POST['command_demolish']!=""  || $_POST['command_cbuild']!="" || $_POST['command_cdemolish']!="") && $_POST['id']!="" && checker_verify())
		{
			// Gebäudedaten laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['buildings']." 
			WHERE 
				building_show='1' 
				AND building_id='".$_POST['id']."';");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				if ($buildlist[$arr['building_id']]['buildlist_current_level']!=null)
				{
					$b_level=$buildlist[$arr['building_id']]['buildlist_current_level'];
				}
				else
				{
					$b_level=0;
				}

				if ($buildlist[$arr['building_id']]['buildlist_build_type']!="")
				{
					$b_status=$buildlist[$arr['building_id']]['buildlist_build_type'];
				}
				else
				{
					$b_status=0;
				}

        $bc = calcBuildingCosts($arr,$b_level);
        $bcn = calcBuildingCosts($arr,$b_level+1);
				$dc = calcDemolishingCosts($arr, $bc);

				// Bauzeit
				$btime_global_factor = $conf['global_time']['v'];
				$btime_build_factor = $conf['build_build_time']['v'];
				$bonus = $c->race->buildtime + $c->type->buildtime + $c->sol->type->buildtime-2;

				$btime = ($bc['metal']+$bc['crystal']+$bc['plastic']+$bc['fuel']+$bc['food']) / 12 * $btime_global_factor * $btime_build_factor;
				$btime *= $bonus;

				$btimen = ($bcn['metal']+$bcn['crystal']+$bcn['plastic']+$bcn['fuel']+$bcn['food']) / 12 * $btime_global_factor * $btime_build_factor;
				$btimen  *= $bonus;

				$dtime = ($dc['metal']+$dc['crystal']+$dc['plastic']+$dc['fuel']+$dc['food']) / 12 * $btime_global_factor * $btime_build_factor;
				$dtime  *= $bonus;

				if ($buildlist[BUILD_BUILDING_ID]['buildlist_people_working']>0)
				{
					$btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
					$btime=$btime-($buildlist[BUILD_BUILDING_ID]['buildlist_people_working']*3);
					if ($btime<$btime_min) $btime=$btime_min;
					$bc['food']+=$buildlist[BUILD_BUILDING_ID]['buildlist_people_working']*12;
				}

				$start_time = $buildlist[$arr['building_id']]['buildlist_build_start_time'];
				$end_time = $buildlist[$arr['building_id']]['buildlist_build_end_time'];

				//
				// Befehle ausführen
				//

				//Gebäude ausbauen
				if ($_POST['command_build']!="" && $b_status==0)
				{
					if (!$builing_something)
					{

						if ($c->fields_used+$arr['building_fields']+$def_field_needed <= $c->fields+$c->fields_extra || $arr['building_fields']==0)
						{
							if ($c->res->metal >= $bc['metal'] && $c->res->crystal >= $bc['crystal'] && $c->res->plastic >= $bc['plastic']  && $c->res->fuel >= $bc['fuel']  && $c->res->food >= $bc['food'])
							{
								$end_time = time()+$btime;
								
								//Gebäude bereits vorhanden
								if (sizeof($buildlist[$arr['building_id']])>0)
								{
									dbquery("
									UPDATE 
										".$db_table['buildlist']." 
									SET
										buildlist_build_type='1',
										buildlist_build_start_time='".time()."',
										buildlist_build_end_time='".$end_time."'
									WHERE 
										buildlist_building_id='".$arr['building_id']."'
										AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
										AND buildlist_planet_id='".$c->id."';");
								}
								//Gebäude noch nicht vorhanden
								else
								{
									dbquery("
									INSERT INTO 
									".$db_table['buildlist']." 
									(
										buildlist_build_type,
										buildlist_build_start_time,
										buildlist_build_end_time,
										buildlist_building_id,
										buildlist_user_id,
										buildlist_planet_id
									) 
									VALUES 
									( 
										'1',
										'".time()."',
										'".$end_time."',
										'".$arr['building_id']."',
										'".$_SESSION[ROUNDID]['user']['id']."',
										'".$c->id."'
									);");

								}
								
								//Rohstoffe vom Planeten abziehen und aktualisieren
								$c->changeRes(-$bc['metal'],-$bc['crystal'],-$bc['plastic'],-$bc['fuel'],-$bc['food']);
								$b_status=1;
								
								
								//Log schreiben
								$log_text = "
								<b>Gebäude Ausbau</b><br><br>
								<b>User:</b> [USER_ID=".$_SESSION[ROUNDID]['user']['id'].";USER_NICK=".$_SESSION[ROUNDID]['user']['nick']."]<br>
								<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
								<b>Gebäude:</b> ".$arr['building_name']."<br>
								<b>Gebäude Level:</b> ".$b_level." (vor Ausbau)<br>
								<b>Bau dauer:</b> ".tf($btime)."<br>
								<b>Ende:</b> ".date("Y-m-d H:i:s",$end_time)."<br>
								<b>Eingesetzte Bewohner:</b> ".nf($buildlist[BUILD_BUILDING_ID]['buildlist_people_working'])."<br>
								<b>Gen-Tech Level:</b> ".GEN_TECH_LEVEL."<br><br>
								<b>Kosten</b><br>
								<b>".RES_METAL.":</b> ".nf($bc['metal'])."<br>
								<b>".RES_CRYSTAL.":</b> ".nf($bc['crystal'])."<br>
								<b>".RES_PLASTIC.":</b> ".nf($bc['plastic'])."<br>
								<b>".RES_FUEL.":</b> ".nf($bc['fuel'])."<br>
								<b>".RES_FOOD.":</b> ".nf($bc['food'])."<br><br>
								<b>Restliche Rohstoffe auf dem Planeten</b><br><br>
								<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
								<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
								<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
								<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
								<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br><br>
								";
								
								//Log Speichern
								add_log_game_building($log_text,$_SESSION[ROUNDID]['user']['id'],$_SESSION[ROUNDID]['user']['alliance_id'],$c->id,$arr['building_id'],$b_status,time());
								
							}
							else
								echo "<i>Bauauftrag kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!</i><br/><br/>";
						}
						else
							echo "<i>Bauauftrag kann nicht gestartet werden, zuwenig Felder vorhanden!</i><br/><br/>";
					}
					else
						echo "<i>Bauauftrag kann nicht gestartet werden, es wird bereits an einem Geb&auml;ude gearbeitet!</i><br/><br/>";
				}

				//Gebäude abbrechen
				if ($_POST['command_demolish']!="" && $b_status==0)
				{
					if (!$builing_something)
					{
						if ($c->res->metal >= $dc['metal'] && $c->res->crystal >= $dc['crystal'] && $c->res->plastic >= $dc['plastic']  && $c->res->fuel >= $dc['fuel']  && $c->res->food >= $dc['food'])
						{
							$end_time = time()+$dtime;
							dbquery("
							UPDATE 
								".$db_table['buildlist']." 
							SET
								buildlist_build_type='2',
								buildlist_build_start_time='".time()."',
								buildlist_build_end_time='".$end_time."'
							WHERE 
								buildlist_building_id='".$arr['building_id']."'
								AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
								AND buildlist_planet_id='".$c->id."';");
								
							//Rohstoffe vom Planeten abziehen und aktualisieren
							$c->changeRes(-$dc['metal'],-$dc['crystal'],-$dc['plastic'],-$dc['fuel'],-$dc['food']);
							$b_status=2;
							
							
							//Log schreiben
							$log_text = "
							<b>Gebäude Abriss</b><br><br>
							<b>User:</b> [USER_ID=".$_SESSION[ROUNDID]['user']['id'].";USER_NICK=".$_SESSION[ROUNDID]['user']['nick']."]<br>
							<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
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
							<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
							<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
							<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
							<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
							<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br><br>
							";
							
							//Log Speichern
							add_log_game_building($log_text,$_SESSION[ROUNDID]['user']['id'],$_SESSION[ROUNDID]['user']['alliance_id'],$c->id,$arr['building_id'],$b_status,time());	
							
						}
						else
							echo "<i>Abbruchauftrag kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!</i><br/><br/>";
					}
					else
						echo "<i>Abbruchauftrag kann nicht gestartet werden, es wird bereits an einem Geb&auml;ude gearbeitet!</i><br/><br/>";
				}

				//Bauauftrag abbrechen
				if ($_POST['command_cbuild']!="" && $b_status==1)
				{
					if ($buildlist[$arr['building_id']]['buildlist_build_end_time'] > time())
					{
						$fac = ($end_time-time())/($end_time-$start_time);
						dbquery("
						UPDATE 
							".$db_table['buildlist']." 
						SET
							buildlist_build_type=0,
							buildlist_build_start_time=0,
							buildlist_build_end_time=0
						WHERE 
							buildlist_building_id='".$arr['building_id']."'
							AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
							AND buildlist_planet_id='".$c->id."';");
							
						//Rohstoffe vom Planeten abziehen und aktualisieren
						$c->changeRes($bc['metal']*$fac,$bc['crystal']*$fac,$bc['plastic']*$fac,$bc['fuel']*$fac,$bc['food']*$fac);
						$b_status=0;
						$builing_something=false;
						
						//Log schreiben
						$log_text = "
						<b>Gebäudebau Abbruch</b><br><br>
						<b>User:</b> [USER_ID=".$_SESSION[ROUNDID]['user']['id'].";USER_NICK=".$_SESSION[ROUNDID]['user']['nick']."]<br>
						<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
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
						<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
						<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
						<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br><br>
						";
						
						//Log Speichern
						add_log_game_building($log_text,$_SESSION[ROUNDID]['user']['id'],$_SESSION[ROUNDID]['user']['alliance_id'],$c->id,$arr['building_id'],$b_status,time());								
					}
					else
						echo "<i>Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!</i><br/><br/>";
				}

				//Abbruchauftrag abbrechen
				if ($_POST['command_cdemolish']!="" && $b_status==2)
				{
					if ($buildlist[$arr['building_id']]['buildlist_build_end_time'] > time())
					{
						$fac = ($end_time-time())/($end_time-$start_time);
						dbquery("
						UPDATE 
							".$db_table['buildlist']." 
						SET
							buildlist_build_type=0,
							buildlist_build_start_time=0,
							buildlist_build_end_time=0
						WHERE 
							buildlist_building_id='".$arr['building_id']."'
							AND buildlist_user_id='".$_SESSION[ROUNDID]['user']['id']."'
							AND buildlist_planet_id='".$c->id."';");
						
						//Rohstoffe vom Planeten abziehen und aktualisieren
						$c->changeRes($dc['metal']*$fac,$dc['crystal']*$fac,$dc['plastic']*$fac,$dc['fuel']*$fac,$dc['food']*$fac);
						$b_status=0;
						$builing_something=false;
						
						//Log schreiben
						$log_text = "
						<b>Gebäudeabbruch Abbruch</b><br><br>
						<b>User:</b> [USER_ID=".$_SESSION[ROUNDID]['user']['id'].";USER_NICK=".$_SESSION[ROUNDID]['user']['nick']."]<br>
						<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
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
						<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
						<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
						<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
						<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
						<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br><br>
						";
						
						//Log Speichern
						add_log_game_building($log_text,$_SESSION[ROUNDID]['user']['id'],$_SESSION[ROUNDID]['user']['alliance_id'],$c->id,$arr['building_id'],$b_status,time());							
					}
					else
						echo "<i>Abbruchauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!</i><br/><br/>";
				}


				if ($b_status==1 && $b_level>0)
				{
					$color="color:#0f0;";
					$status_text="Wird ausgebaut";
				}
				elseif ($b_status==1)
				{
					$color="color:#0f0;";
					$status_text="Wird gebaut";
				}
				elseif ($b_status==2)
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
				infobox_start(text2html($arr['building_name']),1);
				echo "<tr>
                  <td width=\"220\" rowspan=\"4\" class=\"tbldata\"><a href=\"?page=help&amp;site=buildings&amp;id=".$arr['building_id']."\"><img src=\"".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" border=\"0\" /></a></td>
                  <td valign=\"top\" class=\"tbldata\" colspan=\"2\">".$arr['building_shortcomment']."</td>
				     </tr>
				     <tr>
                  <td class=\"tbltitle\" width=\"50%\" height=\"20\">Platzverbrauch pro Ausbaustufe:</td>
                  <td class=\"tbldata\" width=\"50%\">".$arr['building_fields'];
                  if ($arr['building_fields']>1)
                      echo " Felder</td>";
                  else
                      echo " Feld</td>";
      echo "</tr>";
				echo "<tr>
                  <td class=\"tbltitle\" height=\"20\" width=\"50%\">Status:</td>
                  <td id=\"buildstatus\" class=\"tbldata\" width=\"50%\" style=\"".$color."\">$status_text</td>
				     </tr>
				     <tr>
                  <td class=\"tbltitle\" height=\"20\" width=\"50%\">Stufe:</td>";
          if ($b_level>0)
              echo "<td class=\"tbldata\" id=\"buildlevel\" width=\"50%\">$b_level</td></tr>";
          else
              echo "<td class=\"tbldata\" id=\"buildlevel\" width=\"50%\">Noch nicht gebaut</td>
						</tr>";
				infobox_end(1);


				//
				// Baumenü
				//
				echo "<form action=\"?page=$page\" method=\"post\">";
        echo "<input type=\"hidden\" name=\"id\" value=\"".$arr['building_id']."\">";
        checker_init();
				infobox_start("Bauoptionen",1);
				echo "<tr>
                <td class=\"tbltitle\" width=\"16%\">Aktion</td>
                <td class=\"tbltitle\" width=\"14%\">Zeit</th>
                <td class=\"tbltitle\" width=\"14%\">".RES_METAL."</td>
                <td class=\"tbltitle\" width=\"14%\">".RES_CRYSTAL."</td>
                <td class=\"tbltitle\" width=\"14%\">".RES_PLASTIC."</td>
                <td class=\"tbltitle\" width=\"14%\">".RES_FUEL."</td>
                <td class=\"tbltitle\" width=\"14%\">".RES_FOOD."</td>
						</tr>";

				// Bauen
				if ($b_status==0)
				{
					$bWaitArray = calcBuildingWaitTime($bc,$c);

					// Maximale Stufe erreicht
					if ($b_level>=$arr['building_last_level'])
					{
						echo "<tr>
										<td colspan=\"7\" class=\"tbldata\">
											<i>Kein weiterer Ausbau m&ouml;glich.</i>
										</td>
									</tr>";
					}
					// Es wird bereits an einem Gebäude gebaut
					elseif ($builing_something)
					{
						echo "<tr>
										<td class=\"tbldata\" style=\"color:red;\">Bauen</td>
										<td class=\"tbldata\">".tf($btime)."</td>";
						echo $bWaitArray[0];
						//echo "<td class=\"tbldata\">".nf($bc['metal'])."</td><td class=\"tbldata\">".nf($bc['crystal'])."</td><td class=\"tbldata\">".nf($bc['plastic'])."</td><td class=\"tbldata\">".nf($bc['fuel'])."</td><td class=\"tbldata\">".nf($bc['food'])."</td></tr>";
						echo "<tr>
										<td class=\"tbldata\" colspan=\"7\">
											<i>Es kann nichts gebaut werden da gerade an einem anderen Geb&auml;ude gearbeitet wird!</i>
										</td>
									</tr>";
					}
					// Zuwenig Felder vorhanden
					elseif ($c->fields_used+$arr['building_fields']+$def_field_needed > $c->fields+$c->fields_extra)
					{
						echo "<tr>
										<td class=\"tbldata\" style=\"color:red;\">Bauen</td>
										<td class=\"tbldata\">".tf($btime)."</td>";
						echo $bWaitArray[0];
						//echo "<td class=\"tbldata\">".nf($bc['metal'])."</td><td class=\"tbldata\">".nf($bc['crystal'])."</td><td class=\"tbldata\">".nf($bc['plastic'])."</td><td class=\"tbldata\">".nf($bc['fuel'])."</td><td class=\"tbldata\">".nf($bc['food'])."</td></tr>";
						echo "<tr>
										<td class=\"tbldata\" colspan=\"7\">
											<i>Kein Ausbau m&ouml;glich, da es zuwenig Platz (".($c->fields_used+$arr['building_fields']+$def_field_needed - $c->fields+$c->fields_extra)." ben&ouml;tigt) f&uuml;r dieses Geb&auml;ude hat!</i>
										</td>
									</tr>";
					}
					// Zuwenig Rohstoffe vorhanden
					elseif ($c->res->metal<$bc['metal'] || $c->res->crystal<$bc['crystal']  || $c->res->plastic<$bc['plastic']  || $c->res->fuel<$bc['fuel']  || $c->res->food<$bc['food'])
					{
						echo "<tr>
										<td class=\"tbldata\" style=\"color:red;\">Bauen</td>
										<td class=\"tbldata\">".tf($btime)."</td>";
						echo $bWaitArray[0];
						echo "<tr>
										<td class=\"tbldata\" colspan=\"7\">
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
											<td class=\"tbldata\">
												<input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Bauen\"
											</td>
											<td class=\"tbldata\">".tf($btime)."</td>";
						}
						// Ausbauen
						else
						{
							echo "<tr>
											<td class=\"tbldata\">
												<input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Ausbauen\">
											</td>
											<td class=\"tbldata\">".tf($btime)."</td>";
						}
						
								echo "<td class=\"tbldata\">".nf($bc['metal'])."</td>
											<td class=\"tbldata\">".nf($bc['crystal'])."</td>
											<td class=\"tbldata\">".nf($bc['plastic'])."</td>
											<td class=\"tbldata\">".nf($bc['fuel'])."</td>
											<td class=\"tbldata\">".nf($bc['food'])."</td>
										</tr>";
					}
				}

				// Abreissen
				if ($b_level>0 && $arr['building_demolish_costs_factor']!=-1 && $b_status==0)
				{
					$dWaitArray = calcDemolishingWaitTime($dc,$c);
					// Es wird bereits an einem Gebäude gebaut
					if ($builing_something)
					{
						echo "<tr>
										<td class=\"tbldata\" style=\"color:red;\">Abreissen</td>
										<td class=\"tbldata\">".tf($dtime)."</td>";
						echo $dWaitArray[0];
						echo "<tr>
										<td class=\"tbldata\" colspan=\"7\">
												<i>Kein Abriss m&ouml;glich, es wird gerade an einem anderen Geb&auml;ude gearbeitet!</i>
										</td>
									</tr>";
					}
					// Zuwenig Rohstoffe
					elseif ($c->res->metal<$dc['metal'] || $c->res->crystal<$dc['crystal']  || $c->res->plastic<$dc['plastic']  || $c->res->fuel<$dc['fuel']  || $c->res->food<$dc['food'])
					{
						echo "<tr>
										<td class=\"tbldata\" style=\"color:red;\">Abreissen</td>
										<td class=\"tbldata\">".tf($dtime)."</td>";
						echo $dWaitArray[0];
						echo "<tr>
										<td class=\"tbldata\" colspan=\"7\">
											<i>Kein Abriss m&ouml;glich, zuwenig Rohstoffe!</i>
										</td>
									</tr>";
					}
					else
					{
						echo "<tr>
										<td class=\"tbldata\">
											<input type=\"submit\" class=\"button\" name=\"command_demolish\" value=\"Abreissen\">
										</td>
										<td class=\"tbldata\">".tf($dtime)."</td>
										<td class=\"tbldata\">".nf($dc['metal'])."</td>
										<td class=\"tbldata\">".nf($dc['crystal'])."</td>
										<td class=\"tbldata\">".nf($dc['plastic'])."</td>
										<td class=\"tbldata\">".nf($dc['fuel'])."</td>
										<td class=\"tbldata\">".nf($dc['food'])."</td>
									</tr>";
					}
				}

				// Bau abbrechen
				if ($b_status==1)
				{
	      	echo "<tr>
	      					<td class=\"tbldata\">
	      						<input type=\"submit\" class=\"button\" id=\"buildcancel\" name=\"command_cbuild\" value=\"Abbrechen\">
	      					</td>
	      					<td class=\"tbldata\" id=\"buildtime\">-</td>
	      					<td colspan=\"5\" class=\"tbldata\">&nbsp;</td>
	      				</tr>";
	      	if ($b_level<$arr['building_last_level']-1)
	      	{
	         	echo "<tr>
	         					<td class=\"tbldata\" width=\"90\">N&auml;chste Stufe:</td>
	         					<td class=\"tbldata\">".tf($btimen)."</td>
	         					<td class=\"tbldata\">".nf($bcn['metal'])."</td>
	         					<td class=\"tbldata\">".nf($bcn['crystal'])."</td>
	         					<td class=\"tbldata\">".nf($bcn['plastic'])."</td>
	         					<td class=\"tbldata\">".nf($bcn['fuel'])."</td>
	         					<td class=\"tbldata\">".nf($bcn['food'])."</td>
	         				</tr>";
	         }
				}

				// Abriss abbrechen
				if ($b_status==2)
				{
	      	echo "<tr>
	      					<td class=\"tbldata\">
	      						<input type=\"submit\" class=\"button\" id=\"buildcancel\" name=\"command_cdemolish\" value=\"Abbrechen\">
	      					</td>
	      					<td class=\"tbldata\" id=\"buildtime\">-</td>
	      					<td class=\"tbldata\" colspan=\"5\">&nbsp;</td>
	      				</tr>";
				}
				infobox_end(1);

				if ($bWaitArray[1]>0)
				{
					echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Bau vorhanden sind: <b>".tf($bWaitArray[1])."</b><br/>";
				}
				if ($dWaitArray[1]>0)
				{
					echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Abriss vorhanden sind: <b>".tf($dWaitArray[1])."</b><br/>";
				}
				echo "<br/>";

					if ($b_status==1 || $b_status==2)
					{
						?>
							<script type="text/javascript">
								function setCountdown()
								{
									var ts;
									cTime = <?PHP echo time();?>;
									b_level = <?PHP echo $b_level;?>;
									te = <?PHP if($end_time) echo $end_time; else echo 0;?>;
									tc = cTime + cnt;
									window.status = tc;
									ts = te - tc;

									if(b_level>0)
									{
										b_level=b_level+1;
									}
									else
									{
										b_level=1;
									}

									if (ts>=0)
									{
										t = Math.floor(ts / 3600 / 24);
										h = Math.floor(ts / 3600);
										m = Math.floor((ts-(h*3600))/60);
										s = Math.floor((ts-(h*3600)-(m*60)));
										nv = h+"h "+m+"m "+s+"s";
									}
									else
									{
										nv = "-";
										document.getElementById('buildstatus').firstChild.nodeValue="Fertig";
										document.getElementById('buildlevel').firstChild.nodeValue=b_level;
										document.getElementById("buildcancel").name = "submit_info";
							  		document.getElementById("buildcancel").value = "Aktualisieren";
									}
									document.getElementById('buildtime').firstChild.nodeValue=nv;
									cnt = cnt + 1;
									setTimeout("setCountdown()",1000);
								}
								if (document.getElementById('buildtime')!=null)
								{
									cnt = 0;
									setCountdown();
								}
							</script>
						<?PHP
					}

				echo "<input type=\"submit\" name=\"submit_info\" value=\"Aktualisieren\" /> &nbsp; ";
				echo "<input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
				echo "</form>";
			}
			else
				echo "<b>Fehler:</b> Geb&auml;ude nich vorhanden!<br/><br/><a href=\"?page=$page\">&Uuml;bersicht</a>";
		}

/********************
* Übersicht         *
********************/

		else
		{
			$tres = dbquery("SELECT * FROM ".$db_table['techlist']." WHERE techlist_user_id='".$_SESSION[ROUNDID]['user']['id']."';");
			while ($tarr = mysql_fetch_array($tres))
			{
				$techlist[$tarr['techlist_tech_id']]=$tarr['techlist_current_level'];
			}
			$rres = dbquery("SELECT * FROM ".$db_table['building_requirements'].";");
			while ($rarr = mysql_fetch_array($rres))
			{
				if ($rarr['req_req_building_id']>0) 
				{
					$b_req[$rarr['req_building_id']]['b'][$rarr['req_req_building_id']]=$rarr['req_req_building_level'];
				}
				
				if ($rarr['req_req_tech_id']>0) 
				{
					$b_req[$rarr['req_building_id']]['t'][$rarr['req_req_tech_id']]=$rarr['req_req_tech_level'];
				}
			}

			$tres = dbquery("
			SELECT
				bt.*,

				b.building_id,
				b.building_name,
				b.building_type_id,
				b.building_costs_metal,
				b.building_costs_crystal,
				b.building_costs_plastic,
				b.building_costs_fuel,
				b.building_costs_food,
				b.building_build_costs_factor,
				b.building_last_level
			FROM
        ".$db_table['building_types']." AS bt
        INNER JOIN
        ".$db_table['buildings']." AS b
        ON b.building_type_id=bt.type_id
				AND b.building_show=1
			GROUP BY
				b.building_id
			ORDER BY
				bt.type_order ASC,
				b.building_order ASC,
				b.building_name ASC;");
			if (mysql_num_rows($tres)>0)
			{
				$types = array();
				$obj = array();
				while ($tarr=mysql_fetch_array($tres))
				{
					$types[$tarr['type_id']]=$tarr['type_name'];
					$obj[$tarr['type_id']][$tarr['building_id']]=$tarr;
				}
				$cstr=checker_init();
				foreach ($types as $tid => $tname)
				{
					infobox_start(text2html($tname),1,1);

						echo "<colgroup>";
						for ($x=0;$x<NUM_BUILDINGS_PER_ROW;$x++)
							echo "<col width=\"120\">";
						echo "</colgroup>";

						$data = array();
						foreach ($obj[$tid] as $arr)
						{
							$show_this_building = 1;
							if (count($b_req[$arr['building_id']]['b'])>0)
							{
								foreach ($b_req[$arr['building_id']]['b'] as $b=>$l)
								{
									if ($buildlist[$b]['buildlist_current_level']<$l)
									{
										$show_this_building = 0;
									}
								}
							}
							
							if (count($b_req[$arr['building_id']]['t'])>0)
							{
								foreach ($b_req[$arr['building_id']]['t'] as $id=>$level)
								{
									if ($techlist[$id]<$level)
									{
										$show_this_building = 0;
									}
								}
							}
							
							if ($show_this_building==1)
							{
								array_push($data,$arr);
							}
						}
						$cnt = 0;
						$tcnt = 1;
						$rcnt = 1;

						if (count($data)>0)
						{
							foreach ($data as $arr)
							{
								// Aktuellen Level feststellen
								$b_level=intval($buildlist[$arr['building_id']]['buildlist_current_level']);
								$end_time=$buildlist[$arr['building_id']]['buildlist_build_end_time'];

								// Ist im Bau
								if ($buildlist[$arr['building_id']]['buildlist_build_type']==1)
								{
									$class="tbldata3";
									$subtitle =  "Ausbau auf Stufe ".($b_level+1);
									$tmtext = "<span style=\"color:#0f0\">Wird ausgebaut!<br/>Dauer: ".tf($end_time-time())."</span>";
									if($use_img_filter)
									{
										$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_middle.".IMAGE_EXT."&filter=building";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_middle.".IMAGE_EXT."";
									}
								}
								// Wird abgerissen
								elseif ($buildlist[$arr['building_id']]['buildlist_build_type']==2)
								{
									$class="tbldata4";
									$subtitle = "Abriss auf Stufe ".($b_level-1);
									$tmtext = "<span style=\"color:#f90\">Wird abgerissen!<br/>Dauer: ".tf($end_time-time())."</span>";
									if($use_img_filter)
									{
										$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_middle.".IMAGE_EXT."&filter=destructing";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_middle.".IMAGE_EXT."";
									}
								}
								// Untätig
								else
								{
									// Zuwenig Ressourcen
                	$bc = calcBuildingCosts($arr,$b_level);
									if($c->res->metal<$bc['metal'] || $c->res->crystal<$bc['crystal']  || $c->res->plastic<$bc['plastic']  || $c->res->fuel<$bc['fuel']  || $c->res->food<$bc['food'])
									{
										$class="tbldata2";
										$tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r<br/>weiteren Ausbau!</span>";
										if($use_img_filter)
										{
											$img = "inc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_middle.".IMAGE_EXT."&filter=lowres";
										}
										else
										{
											$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_middle.".IMAGE_EXT."";
										}
									}
									else
									{
										$class="tbldata";
										$tmtext = "Unt&auml;tig";
										$img="".IMAGE_PATH."/".IMAGE_BUILDING_DIR."/building".$arr['building_id']."_middle.".IMAGE_EXT."";
									}
									
									if ($b_level==0)
									{
										$subtitle = "Noch nicht gebaut";
									}
									else
									{
										$subtitle = "Stufe ".$b_level;
									}
								}


								echo "<form action=\"?page=$page\" method=\"post\">";
								echo $cstr;
								if ($cnt==0) 
								{
									echo "<tr>";
								}
								echo "<td class=\"".$class."\" style=\"text-align:center;width:".CELL_WIDTH."px\">
												<b>".text2html($arr['building_name'])."</b><br/>".$subtitle."
												<input type=\"hidden\" name=\"action\" value=\"info\">
												<input type=\"hidden\" name=\"id\" value=\"".$arr['building_id']."\">
												<button name=\"submit_info\" type=\"submit\" value=\"submit_info\" ".tm(text2html($arr['building_name']),"$tmtext<br/>Klicken um Details und Bauoptionen anzuzeigen").">
													<img src=\"".$img."\" width=\"120\" height=\"120\" alt=\"".text2html($arr['building_name'])."\">
												</button>
											</td>
										</form>
								</td>\n";

								if (count($data)==$tcnt)
								{
									for ($x=0;$x<(NUM_BUILDINGS_PER_ROW*$rcnt)-count($data);$x++)
									{
										echo "<td class=\"tbldata\" style=\"width:".CELL_WIDTH."px\">&nbsp;</td>";
									}
								}
								if ($cnt==NUM_BUILDINGS_PER_ROW-1)
								{
									echo "</tr>";
									$cnt = -1;
									$rcnt++;
								}
								$cnt++;
								$tcnt++;
							}
						}
						else
							echo "<tr>
											<td class=\"tbldata\" colspan=\"".NUM_BUILDINGS_PER_ROW."\" style=\"text-align:center;border:0;width:100%\">
												<i>In dieser Kategorie kann momentan noch nichts gebaut werden!</i>
											</td>
										</tr>";
					infobox_end(1);
				}
			}
			else
				echo "<i>Es k&ouml;nnen noch keine Geb&auml;ude gebaut werden!</i>";
		}

	}
	// ENDE SKRIPT //

	?>
