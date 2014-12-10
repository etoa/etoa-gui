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
	//
	
	/**
	* Manages technology research
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

   // DEFINITIONEN //

if ($cu->properties->cssStyle=="Classic" || $cu->properties->cssStyle=="Dark") {
  define('NUM_BUILDINGS_PER_ROW',4);
  define('CELL_WIDTH','25%');
  define('TABLE_WIDTH','');
} else {
  define('NUM_BUILDINGS_PER_ROW',5);
  define('CELL_WIDTH',120);
  define('TABLE_WIDTH','auto');
}	
  
// Aktiviert / Deaktiviert Bildfilter
if ($cu->properties->imageFilter ==1) {
	$use_img_filter = true;
} else {
	$use_img_filter = false;
}

// SKRIPT //
if (isset($cp)) {
	$bl = new BuildList($cp->id(),$cu->id);

	if ($bl->getLevel(TECH_BUILDING_ID) > 0) {
		define('CURRENT_LAB_LEVEL',$bl->getLevel(TECH_BUILDING_ID));

		$tl = new TechList($cu->id);
		define("GEN_TECH_LEVEL",$tl->getLevel(GEN_TECH_ID));
		$minBuildTimeFactor = (0.1-(GEN_TECH_LEVEL/100));

		// Überschrift
		echo "<h1>Forschungslabor (Stufe ".CURRENT_LAB_LEVEL.") des Planeten ".$cp->name."</h1>";
		echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);
		
		// Forschungsliste laden && Gentech level definieren
		$tres = dbquery("
		SELECT 
			* 
		FROM 
			techlist 
		WHERE 
			techlist_user_id='".$cu->id."';");
		$builing_something=false;
		while ($tarr = mysql_fetch_array($tres)) {
			$techlist[$tarr['techlist_tech_id']]=$tarr;
            // Check, ob schon eine Technik geforscht wird
            // BUGFIX: this is tech, NO check for same planet,
            // because only one tech at the same time per user
			if ($tarr['techlist_build_type']>2) {
				$builing_something=true;
			}
		}

		$new_people_set = false;
		// people working changed
		if (isset($_POST['submit_people_form']))
		{
			$set_people = nf_back($_POST['peopleWorking']);
			if (!$builing_something && $bl->setPeopleWorking(TECH_BUILDING_ID, $set_people,true))
			{
				success_msg("Arbeiter zugeteilt!");
				$new_people_set = true;
			}
			else
			{
				error_msg('Arbeiter konnten nicht zugeteilt werden!');
			}
		}
		// reload buildlist and techlist in case the number of workers has changed.
        // re-define constants dependent on these objects
		$bl = new BuildList($cp->id(),$cu->id);
		define('CURRENT_LAB_LEVEL',$bl->getLevel(TECH_BUILDING_ID));

		$tl = new TechList($cu->id);
		define("GEN_TECH_LEVEL",$tl->getLevel(GEN_TECH_ID));
		$minBuildTimeFactor = (0.1-(GEN_TECH_LEVEL/100));

		// People working in the tech building.
		$peopleWorking = (
			($new_people_set && isset($set_people)) ?
			$set_people :
			$bl->getPeopleWorking(TECH_BUILDING_ID)
		);	

		$peopleTimeReduction = $cfg->value('people_work_done');
		$peopleFoodConsumption = $cfg->value('people_food_require');

		//level zählen welches das forschungslabor über dem angegeben level ist und faktor berechnen
		$need_bonus_level = CURRENT_LAB_LEVEL - $conf['build_time_boni_forschungslabor']['p1'];
		if ($need_bonus_level<=0) {
			$time_boni_factor=1;
		} else {
			$time_boni_factor=max($conf['build_time_boni_forschungslabor']['p2'] , 1-($need_bonus_level*($conf['build_time_boni_forschungslabor']['v']/100)));
		}

		//
		// Läd alle benötgten Daten in Arrays
		//
	
		// Load built buildings
		$blres = dbquery("
			SELECT 
				* 
			FROM 
				buildlist 
			WHERE  
				buildlist_entity_id='".$cp->id()."'
			;");
		while ($blarr = mysql_fetch_array($blres)) {
			$buildlist[$blarr['buildlist_building_id']]=$blarr['buildlist_current_level'];
		}
			
		// Load requirements
		$rres = dbquery("
			SELECT 
				* 
			FROM 
				tech_requirements;");
		while ($rarr = mysql_fetch_array($rres)) {
			if ($rarr['req_building_id']>0) 
				$b_req[$rarr['obj_id']]['b'][$rarr['req_building_id']]=$rarr['req_level'];
			if ($rarr['req_tech_id']>0) 
				$b_req[$rarr['obj_id']]['t'][$rarr['req_tech_id']]=$rarr['req_level'];
		}

		$bid = 0;
		if ((isset($_GET['id']) && intval($_GET['id']) >0) || (count($_POST)>0	&& checker_verify())) {
			if (isset($_GET['id']) && intval($_GET['id']) >0) {
				$bid = intval($_GET['id']);
			} else {				
				foreach ($_POST as $k => $v) {
					if(stristr($k,'_x')) {
						$bid = intval(preg_replace('/show_([0-9]+)_x/', '\1', $k));
						break;
					}
				}
				if ($bid==0 && isset($_POST['show'])) {
					$bid = intval($_POST['show']);
				}
				if ($bid==0 && isset($_POST['id'])) {
					$bid = intval($_POST['id']);
				}			
			}
		}

		// cache checker to add it to several forms
		ob_start();
		checker_init();
		$checker = ob_get_contents();
		ob_end_clean();

		$peopleFree = floor($cp->people) - $bl->totalPeopleWorking() + $peopleWorking;
		$peopleOptimized = 0;
		if ($bid) {
			// Forschungsdaten laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				technologies 
			WHERE  
				tech_id='".$bid."'
				AND tech_show='1';");
			if (mysql_num_rows($res)>0) {
				$currentTechData = mysql_fetch_array($res);
				$bc = array();
				$costs = array();
				$costs[0] = $currentTechData['tech_costs_metal'];
				$costs[1] = $currentTechData['tech_costs_crystal'];
				$costs[2] = $currentTechData['tech_costs_plastic'];
				$costs[3] = $currentTechData['tech_costs_fuel'];
				$costs[4] = $currentTechData['tech_costs_food'];
				$costs[5] = $currentTechData['tech_costs_power'];
				$level = 0;
				if (isset($techlist[$bid])) {
					$level = $techlist[$bid]['techlist_current_level'];
				}

				foreach ($resNames as $rk => $rn) {
					//BUGFIX by river: costsResearch factor. Still whole code is wrong, but at least consistent now.
					$bc['costs'.$rk] = $cu->specialist->costsResearch * $costs[$rk] * pow($currentTechData['tech_build_costs_factor'], $level);
				}
				$bc['costs5'] = $costs[5] * pow($currentTechData['tech_build_costs_factor'], $level);

				$bonus = $cu->race->researchTime + $cp->typeResearchtime + $cp->starResearchtime - 2;
				$bonus *= $cu->specialist->researchTime;
				
				$bc['time'] = (array_sum($bc)) / GLOBAL_TIME * RES_BUILD_TIME * $time_boni_factor;
				$bc['time'] *= $bonus;
				$maxReduction = $bc['time'] - $bc['time'] * $minBuildTimeFactor;
				
				$peopleOptimized = ceil($maxReduction / $cfg->value('people_work_done'));
			}
		}

			// create box to change people working
			$box =	'
						<input type="hidden" name="workDone" id="workDone" value="'.$cfg->value('people_work_done').'" />
						<input type="hidden" name="foodRequired" id="foodRequired" value="'.$cfg->value('people_food_require').'" />
						<input type="hidden" name="peopleFree" id="peopleFree" value="'.$peopleFree.'" />
						<input type="hidden" name="foodAvaiable" id="foodAvaiable" value="'.$cp->getRes1(4).'" />';
			if ($cu->properties->itemShow=='full' && isset($bid) && $bid>0) {
				$box .= '<input type="hidden" name="peopleOptimized" id="peopleOptimized" value="' . $peopleOptimized . '" />';
			} else {
				$box .= '<input type="hidden" name="peopleOptimized" id="peopleOptimized" value="0" />';	
			}
			$box .= '	<tr>
								<th>Eingestellte Arbeiter</th>
								<td>
									<input 	type="text" 
											name="peopleWorking" 
											id="peopleWorking" 
											value="'.nf($peopleWorking).'" 
											onkeyup="updatePeopleWorkingBox(this.value,\'-1\',\'-1\');"/>
							</td>
						</tr>';
			$box .=		'<tr>
								<th>Zeitreduktion</th>
								<td><input	type="text"
											name="timeReduction"
											id="timeReduction"
											value="'.tf($cfg->value('people_work_done') * $peopleWorking).'"
											onkeyup="updatePeopleWorkingBox(\'-1\',this.value,\'-1\');" /></td>
							</tr>
								<th>Nahrungsverbrauch</th>
								<td><input	type="text"
											name="foodUsing"
											id="foodUsing"
											value="'.nf($cfg->value('people_food_require') * $peopleWorking).'"
											onkeyup="updatePeopleWorkingBox(\'-1\',\'-1\',this.value);" /></td>
							</tr>
							<tr>
								<td colspan="2" style="text-align:center;">
									<div id="changeWorkingPeopleError" style="display:none;">&nbsp;</div>
									<input type="submit" value="Speichern" name="submit_people_form" id="submit_people_form" />&nbsp;';

			if (isset($bid) && $bid>0)
			{
				$box .= '<input type="button" value="Optimieren" onclick="updatePeopleWorkingBox(\''.$peopleOptimized.'\',\'-1\',\'^-1\');">';
			}
			$box .= '
						</td>
					</tr>';	

	    	tableStart("Labor-Infos");
			echo '<colgroup><col style="width:400px;"/><col/></colgroup>';
			// Specialist
			if ($cu->specialist->costsResearch!=1)
			{
				echo "<tr><td>Kostenreduktion durch ".$cu->specialist->name.":</td><td>".get_percent_string($cu->specialist->costsResearch)."</td></tr>";
			}
			if ($cu->specialist->researchTime!=1) 
			{
				echo "<tr><td>Forschungszeitverringerung durch ".$cu->specialist->name.":</td><td>".get_percent_string($cu->specialist->researchTime)."</td></tr>";
			}
			// Building level time bonus
	    	echo "<tr><td>Forschungszeitverringerung:</td><td>";
	    	if ($need_bonus_level>=0)
	    	{
	    		echo get_percent_string($time_boni_factor)." durch Stufe ".CURRENT_LAB_LEVEL." (-".((1-$conf['build_time_boni_forschungslabor']['p2'])*100)."% maximum)";
	    	}
	    	else
	    	{
	    		echo "Stufe ".$conf['build_time_boni_forschungslabor']['p1']." erforderlich!";
	    	}
			echo '</td></tr>';
			// Worker
		  	echo"<tr><td>Eingestellte Arbeiter:</td><td>". nf($peopleWorking);
			if (!$builing_something)
			{
				echo '&nbsp;<a href="javascript:;" onclick="toggleBox(\'changePeople\');">[&Auml;ndern]</a>';
			}
			echo '</td></tr>';
			if ($peopleWorking > 0)
			{
				echo '<tr><td>Zeitreduktion durch Arbeiter pro Auftrag:</td><td>'.tf($peopleTimeReduction*$peopleWorking).'</td></tr>';
				echo '<tr><td>Nahrungsverbrauch durch Arbeiter pro Auftrag:</td><td>'.nf($peopleFoodConsumption*$peopleWorking).'</td></tr>';
			}
			// Genetics technology level
			if (GEN_TECH_LEVEL > 0)
			{
				echo '<tr><td>Gentechnologie:</td><td>'.GEN_TECH_LEVEL.'</td></tr>';
				echo '<tr><td>Minimale Forschungszeit (mit Arbeiter):</td><td>Forschungszeit * '.$minBuildTimeFactor.'</td></tr>';
			}
	    	tableEnd();

			echo '<div id="changePeople" style="display:none;">';
			tableStart("Arbeiter im Forschungslabor zuteilen");
			echo '<form id="changeWorkingPeople" action="?page='.$page.'&amp;id='.$bid.'" method="post">
				'.$checker.$box.'</form>';
			tableEnd();
			echo '</div>';


			//
			//Forschung erforschen/abbrechen
			//
			if ($bid > 0)
			{
				$arr = $currentTechData;
				if ($arr)
				{	
					// Prüft, ob Technik schon erforscht wurde und setzt Variablen
					if(isset($techlist[$arr['tech_id']]))
					{
						$built = true;
						
						$b_level = $techlist[$arr['tech_id']]['techlist_current_level'];
						$b_status = $techlist[$arr['tech_id']]['techlist_build_type'];
						$start_time = $techlist[$arr['tech_id']]['techlist_build_start_time'];
						$end_time = $techlist[$arr['tech_id']]['techlist_build_end_time'];
						$planet_id = $techlist[$arr['tech_id']]['techlist_entity_id'];
					}
					// Tech wurde noch nicht erforscht. Es werden Default Werte vergeben
					else
					{
						$built = false;
						
						$b_level = 0;
						$b_status=0;
						$start_time = 0;
						$end_time = 0;
						$planet_id = 0;
					}
					
	
					$bc = calcTechCosts($arr,$b_level,$cu->specialist->costsResearch);
	
					$bcn['metal'] = $cu->specialist->costsResearch * $arr['tech_costs_metal'] * pow($arr['tech_build_costs_factor'],$b_level+1);
					$bcn['crystal'] = $cu->specialist->costsResearch * $arr['tech_costs_crystal'] * pow($arr['tech_build_costs_factor'],$b_level+1);
					$bcn['plastic'] = $cu->specialist->costsResearch * $arr['tech_costs_plastic'] * pow($arr['tech_build_costs_factor'],$b_level+1);
					$bcn['fuel'] = $cu->specialist->costsResearch * $arr['tech_costs_fuel'] * pow($arr['tech_build_costs_factor'],$b_level+1);
					$bcn['food'] = $cu->specialist->costsResearch * $arr['tech_costs_food'] * pow($arr['tech_build_costs_factor'],$b_level+1);
	
	
					// Bauzeit
					$bonus = $cu->race->researchTime + $cp->typeResearchtime + $cp->starResearchtime - 2;
	
					$btime = ($bc['metal']+$bc['crystal']+$bc['plastic']+$bc['fuel']+$bc['food']) / GLOBAL_TIME * RES_BUILD_TIME * $time_boni_factor;
					$btime *= $bonus * $cu->specialist->researchTime;
	
					//Nächste Stufe
					$btimen = ($bcn['metal']+$bcn['crystal']+$bcn['plastic']+$bcn['fuel']+$bcn['food']) / GLOBAL_TIME * RES_BUILD_TIME * $time_boni_factor;
					$btimen  *= $bonus * $cu->specialist->researchTime;
	
	
					// Berechnet mindest Bauzeit in beachtung von Gentechlevel
					$btime_min=$btime*$minBuildTimeFactor;
					$btime=$btime-$peopleWorking*$peopleTimeReduction;
					if ($btime < $btime_min) 
					{
						$btime=$btime_min;
					}
					$bc['food']+=$peopleWorking*$peopleFoodConsumption;
					
	
					//
					// Befehle ausführen
					//
	
					if (isset($_POST['command_build']) && $b_status==0)
					{
						if (!$builing_something)
						{
	
								if ($cp->resMetal >= $bc['metal'] && $cp->resCrystal >= $bc['crystal'] && $cp->resPlastic >= $bc['plastic']  && $cp->resFuel >= $bc['fuel']  && $cp->resFood >= $bc['food'])
								{
									$start_time = time();
									$end_time = time()+$btime;
									// if (sizeof($techlist[$arr['tech_id']])>0)
									if (isset($techlist[$arr['tech_id']]))
									{
										dbquery("
										UPDATE 
											techlist 
										SET
		                  					techlist_build_type='3',
		                  					techlist_build_start_time='".time()."',
		                  					techlist_build_end_time='".$end_time."',
		                  					techlist_entity_id='".$cp->id()."'
										WHERE
											techlist_tech_id='".$arr['tech_id']."'
											AND techlist_user_id='".$cu->id."';");
									}
									else
									{
										dbquery("
										INSERT INTO 
										techlist 
										(
											techlist_entity_id,
											techlist_build_type,
											techlist_build_start_time,
											techlist_build_end_time,
											techlist_tech_id,
											techlist_user_id
										)
										VALUES
										(
											'".$cp->id()."',
											'3',
											'".time()."',
											'".$end_time."',
											'".$arr['tech_id']."',
											'".$cu->id."'
										);");
	
									}
									$planet_id=$cp->id();
									
									//Rohstoffe vom Planeten abziehen und aktualisieren
									$cp->changeRes(-$bc['metal'],-$bc['crystal'],-$bc['plastic'],-$bc['fuel'],-$bc['food']);
									$b_status=3;
									
									//Log schreiben
									$log_text = "[b]Forschung Ausbau[/b]

[b]Erforschungsdauer:[/b] ".tf($btime)."
[b]Ende:[/b] ".date("d.m.Y H:i:s",$end_time)."
[b]Forschungslabor Level:[/b] ".CURRENT_LAB_LEVEL."
[b]Eingesetzte Bewohner:[/b] ".nf($peopleWorking)."
[b]Gen-Tech Level:[/b] ".GEN_TECH_LEVEL."
[b]Eingesetzter Spezialist:[/b] ".$cu->specialist->name."

[b]Kosten[/b]
[b]".RES_METAL.":[/b] ".nf($bc['metal'])."
[b]".RES_CRYSTAL.":[/b] ".nf($bc['crystal'])."
[b]".RES_PLASTIC.":[/b] ".nf($bc['plastic'])."
[b]".RES_FUEL.":[/b] ".nf($bc['fuel'])."
[b]".RES_FOOD.":[/b] ".nf($bc['food'])."

[b]Restliche Rohstoffe auf dem Planeten[/b]
[b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
[b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
[b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
[b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
[b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";
									
									//Log Speichern
									GameLog::add(GameLog::F_TECH, GameLog::INFO, $log_text, $cu->id,$cu->allianceId,$cp->id,$arr['tech_id'], $b_status, $b_level);
								}
								else
								{
									echo "<i>Forschung kann nicht gestartet werden, zuwenig Rohstoffe vorhanden!</i><br/><br/>";
								}
						}
						else
						{
							echo "<i>Forschung kann nicht gestartet werden, es wird bereits an einer Technologie geforscht!</i><br/><br/>";
						}
					}
	
	
					if (isset($_POST['command_cbuild']) && $b_status==3)
					{
						if (isset($techlist[$arr['tech_id']]['techlist_build_end_time']) && $techlist[$arr['tech_id']]['techlist_build_end_time'] > time())
						{
							$fac = ($end_time-time())/($end_time-$start_time);
							dbquery("
							UPDATE 
								techlist 
							SET
								techlist_build_type='0',
								techlist_build_start_time='0',
								techlist_build_end_time='0'
							WHERE 
								techlist_tech_id='".$arr['tech_id']."'
								AND techlist_user_id='".$cu->id."';");
	
							//Rohstoffe zurückgeben und aktualisieren
							$cp->changeRes($bc['metal']*$fac,$bc['crystal']*$fac,$bc['plastic']*$fac,$bc['fuel']*$fac,$bc['food']*$fac);
							$b_status=0;
							$builing_something=false;
							
							//Log schreiben
							$log_text = "[b]Forschung Abbruch[/b]

[b]Start der Forschung:[/b] ".date("d.m.Y H:i:s",$start_time)."
[b]Ende der Forschung:[/b] ".date("d.m.Y H:i:s",$end_time)."

[b]Erhaltene Rohstoffe[/b]
[b]Faktor:[/b] ".$fac."
[b]".RES_METAL.":[/b] ".nf($bc['metal']*$fac)."
[b]".RES_CRYSTAL.":[/b] ".nf($bc['crystal']*$fac)."
[b]".RES_PLASTIC.":[/b] ".nf($bc['plastic']*$fac)."
[b]".RES_FUEL.":[/b] ".nf($bc['fuel']*$fac)."
[b]".RES_FOOD.":[/b] ".nf($bc['food']*$fac)."

[b]Rohstoffe auf dem Planeten[/b]
[b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
[b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
[b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
[b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
[b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";
							
							//Log Speichern
							GameLog::add(GameLog::F_TECH, GameLog::INFO, $log_text, $cu->id,$cu->allianceId,$cp->id,$arr['tech_id'], $b_status, $b_level);

						}
						else
						{
							echo "<i>Bauauftrag kann nicht mehr abgebrochen werden, die Arbeit ist bereits fertiggestellt!</i><br/><br/>";
						}
					}
	
					if ($b_status==3)
					{
						$color="color:#0f0;";
						$status_text="Wird erforscht";
					}
					else
					{
						$color="";
						$status_text="Unt&auml;tig";
					}
	
					//
					// Forschungsdaten anzeigen
					//
					tableStart(text2html($arr['tech_name']." ".$b_level));
					echo "<tr><td width=\"220\" rowspan=\"3\" style=\"background:#000;;vertical-align:middle;\">
					".helpImageLink("research&amp;id=".$arr['tech_id'],IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$arr['tech_id'].".".IMAGE_EXT,$arr['tech_name'],"width:220px;height:220px")."
					</td>";
					echo "<td valign=\"top\" colspan=\"2\">".text2html($arr['tech_shortcomment'])."</td></tr>";
					echo "<tr><th height=\"20\" width=\"50%\">Status:</th>";
					echo "<td id=\"buildstatus\" width=\"50%\" style=\"".$color."\">$status_text</td></tr>";
					echo "<tr><th height=\"20\" width=\"50%\">Stufe:</th>";
	
					if ($b_level>0)
					{
						echo "<td id=\"buildlevel\" width=\"50%\">".$b_level."</td></tr>";
					}
					else
					{
						echo "<td id=\"buildlevel\" width=\"50%\">Noch nicht erforscht</td></tr>";
					}
					tableEnd();
	
	
					// Check requirements for this building
					$requirements_passed = true;
					$bid = $arr['tech_id'];
					if (isset($b_req[$bid]['b']) && count($b_req[$bid]['b'])>0)
					{
						foreach ($b_req[$bid]['b'] as $b => $l)
						{
							if (!isset($buildlist[$b]) || $buildlist[$b]<$l)
							{
								$requirements_passed = false;
							}
						}
					}
					if (isset($b_req[$bid]['t']) && count($b_req[$bid]['t'])>0)
					{
						foreach ($b_req[$bid]['t'] as $id => $level)
						{
							if (!isset($techlist[$id]['techlist_current_level']) || $techlist[$id]['techlist_current_level']<$level)
							{
								$requirements_passed = false;
							}
						}
					}
	
					//
					// Baumenü
					//
					echo "<form action=\"?page=$page\" method=\"post\">";
	                echo "<input type=\"hidden\" name=\"id\" value=\"".$arr['tech_id']."\">";
	                echo $checker;
	                
	                
					if ($requirements_passed)
					{
					tableStart("Forschoptionen");
					echo "<tr>
					<th width=\"16%\">Aktion</th>
					<th width=\"14%\">Zeit</th>
					<th width=\"14%\">".RES_METAL."</th>
					<th width=\"14%\">".RES_CRYSTAL."</th>
					<th width=\"14%\">".RES_PLASTIC."</th>
					<th width=\"14%\">".RES_FUEL."</th>
					<th width=\"14%\">".RES_FOOD."</th></tr>";
	
					$notAvStyle=" style=\"color:red;\"";
	
					// Bauen
					if ($b_status==0)
					{
							// Wartezeiten auf Ressourcen berechnen
							if ($cp->prodMetal>0) $bwait['metal']=ceil(($bc['metal']-$cp->resMetal)/$cp->prodMetal*3600);else $bwait['metal']=0;
							if ($cp->prodCrystal>0) $bwait['crystal']=ceil(($bc['crystal']-$cp->resCrystal)/$cp->prodCrystal*3600);else $bwait['crystal']=0;
							if ($cp->prodPlastic>0) $bwait['plastic']=ceil(($bc['plastic']-$cp->resPlastic)/$cp->prodPlastic*3600);else $bwait['plastic']=0;
							if ($cp->prodFuel>0) $bwait['fuel']=ceil(($bc['fuel']-$cp->resFuel)/$cp->prodFuel*3600);else $bwait['fuel']=0;
							if ($cp->prodFood>0) $bwait['food']=ceil(($bc['food']-$cp->resFood)/$cp->prodFood*3600);else $bwait['food']=0;
							$bwmax=max($bwait['metal'],$bwait['crystal'],$bwait['plastic'],$bwait['fuel'],$bwait['food']);
							
							// Baukosten-String
							$bcstring = "<td";
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
							$bcstring.= ">".nf($bc['food'])."</td></tr>";
		
							// Maximale Stufe erreicht
							if ($b_level>=$arr['tech_last_level'])
							{
								echo "<tr><td colspan=\"7\"><i>Keine Weiterentwicklung m&ouml;glich.</i></td></tr>";
							}
							// Es wird bereits geforscht
							elseif ($builing_something)
							{
								echo "<tr><td style=\"color:red;\">Erforschen</td><td>".tf($btime)."</td>";
								echo $bcstring;
								echo "<tr><td colspan=\"7\"><i>Es kann nichts erforscht werden da gerade an einer anderen Technik geforscht wird!</i></td></tr>";
							}
							// Zuwenig Rohstoffe vorhanden
							elseif ($cp->resMetal<$bc['metal'] || $cp->resCrystal<$bc['crystal']  || $cp->resPlastic<$bc['plastic']  || $cp->resFuel<$bc['fuel']  || $cp->resFood<$bc['food'])
							{
								echo "<tr><td style=\"color:red;\">Erforschen</td><td>".tf($btime)."</td>";
								echo $bcstring;
								echo "<tr><td colspan=\"7\"><i>Keine Weiterentwicklung m&ouml;glich, zuwenig Rohstoffe!</i></td></tr>";
							}
							// Forschen
							elseif ($b_level==0)
							{
								echo "<tr><td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td>".tf($btime)."</td>";
								echo "<td>".nf($bc['metal'])."</td><td>".nf($bc['crystal'])."</td><td>".nf($bc['plastic'])."</td><td>".nf($bc['fuel'])."</td><td>".nf($bc['food'])."</td></tr>";
							}
							// Ausbauen
							else
							{
								echo "<tr><td><input type=\"submit\" class=\"button\" name=\"command_build\" value=\"Erforschen\"></td><td>".tf($btime)."</td>";
								echo "<td>".nf($bc['metal'])."</td><td>".nf($bc['crystal'])."</td><td>".nf($bc['plastic'])."</td><td>".nf($bc['fuel'])."</td><td>".nf($bc['food'])."</td></tr>";
							}
					}
	
	
					// Bau abbrechen
					if ($b_status==3)
					{
						if ($planet_id==$cp->id())
						{
	              			echo "<tr><td><input type=\"submit\" class=\"button\" id=\"buildcancel\" name=\"command_cbuild\" value=\"Abbrechen\"  onclick=\"if (this.value=='Abbrechen'){return confirm('Wirklich abbrechen?');}\" /></td>";
	              			echo '<td id="buildtime">-</td>
					    	<td colspan="5"  id="progressbar" style="height:40px;background:#fff;text-align:center;"></td></tr>';
	              			if ($b_level<$arr['tech_last_level']-1)
		         				echo "<tr><td width=\"90\">N&auml;chste Stufe:</td><td>".tf($btimen)."</td><td>".nf($bcn['metal'])."</td><td>".nf($bcn['crystal'])."</td><td>".nf($bcn['plastic'])."</td><td>".nf($bcn['fuel'])."</td><td>".nf($bcn['food'])."</td></tr>";
		         			countDown("buildtime",$end_time,"buildcancel");
							jsProgressBar("progressbar",$start_time,$end_time);
						}
						else
						{
							echo "<tr><td colspan=\"7\">Technologie wird auf einem anderen Planeten bereits erforscht!</td></tr>";					
						}
					}
	
	
					tableEnd();
	
					if (isset($bwmax) && $bwmax>0)
						echo "Wartezeit bis gen&uuml;gend Rohstoffe zum Forschen vorhanden sind: <b>".tf($bwmax)."</b><br/><br/>";
	
	
	
						/*if ($b_status==3 || $b_status==4)
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
						}*/
					}
					else
					{
						echo "<a href=\"?page=techtree\">Voraussetzungen</a> noch nicht erfüllt!<br/><br/>";
					}
	
					echo "<input type=\"submit\" name=\"command_show\" value=\"Aktualisieren\" /> &nbsp; ";
					echo "<input type=\"button\" value=\"Zur&uuml;ck zur &Uuml;bersicht\" onclick=\"document.location='?page=$page'\" />";
					echo "</form>";
				}
				else
				{
					error_msg("Technik nich vorhanden!");
					return_btn();
				}
			}
	
			//
			// Übersicht anziegen
			//
			else
			{			
				
				
				// Load categories
				$tres = dbquery("
				SELECT
					*
				FROM
	        tech_types
				ORDER BY
					type_order ASC
				;");				
				if (mysql_num_rows($tres)>0)
				{			
	
					// Load technologies
					$bres = dbquery("
					SELECT
						tech_type_id,
						tech_id,
						tech_name,
						tech_last_level,
						tech_shortcomment,
						tech_costs_metal,
						tech_costs_crystal,
						tech_costs_plastic,
						tech_costs_fuel,
						tech_costs_food,
						tech_build_costs_factor,
						tech_show
					FROM
						technologies
					WHERE 
						tech_show='1'
					ORDER BY
						tech_order,
						tech_name
					;");	
					$tech = array();
					if (mysql_num_rows($bres)>0)			
					{
						while ($barr = mysql_fetch_Array($bres))
						{
							$tid = $barr['tech_type_id'];
							$bid = $barr['tech_id'];
							$tech[$tid][$bid]['name'] = $barr['tech_name'];
							$tech[$tid][$bid]['last_level'] = $barr['tech_last_level'];
							$tech[$tid][$bid]['shortcomment'] = $barr['tech_shortcomment'];
							$tech[$tid][$bid]['tech_costs_metal'] = $barr['tech_costs_metal'];
							$tech[$tid][$bid]['tech_costs_crystal'] = $barr['tech_costs_crystal'];
							$tech[$tid][$bid]['tech_costs_plastic'] = $barr['tech_costs_plastic'];
							$tech[$tid][$bid]['tech_costs_fuel'] = $barr['tech_costs_fuel'];
							$tech[$tid][$bid]['tech_costs_food'] = $barr['tech_costs_food'];
							$tech[$tid][$bid]['tech_build_costs_factor'] = $barr['tech_build_costs_factor'];
							$tech[$tid][$bid]['show'] = $barr['tech_show'];
						}
					}
				
					$cstr=$checker;
					echo "<form action=\"?page=$page\" method=\"post\"><div>";
					echo $cstr;
					while ($tarr = mysql_fetch_array($tres))
					{
						tableStart($tarr['type_name'],TABLE_WIDTH);
						$cnt = 0; // Counter for current row
						$scnt = 0; // Counter for shown techs
	
						// Check if techs are avalaiable in this category
						$bdata = $tech[$tarr['type_id']];
						if (isset($bdata) && count($bdata)>0)
						{
							// Run through all techs in this cat
							foreach ($bdata as $bid => $bv)
							{						
								
								// Aktuellen Level feststellen wenn Tech vorhanden
								if(isset($techlist[$bid]))
								{
									$b_level = intval($techlist[$bid]['techlist_current_level']);
									$start_time = intval($techlist[$bid]['techlist_build_start_time']);
									$end_time = intval($techlist[$bid]['techlist_build_end_time']);
								}
								else
								{
									$b_level = 0;
									$end_time = 0;
								}
								
								// Check requirements for this tech
								$requirements_passed = true;
								$b_req_info = array();
								$t_req_info = array();								
								if (isset($b_req[$bid]['t']) && count($b_req[$bid]['t'])>0)
								{
									foreach ($b_req[$bid]['t'] as $b=>$l)
									{
										if (!isset($techlist[$b]['techlist_current_level']) || $techlist[$b]['techlist_current_level']<$l)
										{
											$t_req_info[] = array($b,$l,false);
											$requirements_passed = false;
										}
										else
											$t_req_info[] = array($b,$l,true);
									}
								}
	              if (isset($b_req[$bid]['b']) && count($b_req[$bid]['b'])>0)
	              {
	              	foreach ($b_req[$bid]['b'] as $id=>$level)
	              	{
	              		if (!isset($buildlist[$id]) || $buildlist[$id]<$level)
	              		{
	              		  $requirements_passed = false;
											$b_req_info[] = array($id,$level,false);
										}
										else
											$b_req_info[] = array($id,$level,true);
	              	}
	              }
	
								if ($bv['show']==0 && $b_level>0)
								{
									$subtitle =  'Kann nicht erforscht werden';
									$tmtext = '<span style="color:#999">Es ist nicht vorgesehen dass diese Technologie erforscht werden kann!</span><br/>';
									$color = '#999';
									if($use_img_filter)
									{
										$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."&filter=na";
									}
									else
									{
										$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."";
									}		
								}
								elseif ($bv['show']==1)
								{
									// Voraussetzungen nicht erfüllt
									if (!$requirements_passed)
									{
										$subtitle =  'Voraussetzungen fehlen';
										$tmtext = '<span style="color:#999">Baue zuerst die nötigen Gebäude und erforsche die nötigen Technologien um diese Technologie zu erforschen!</span><br/>';
										
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
											$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."&filter=na";
										}
										else
										{
											$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."";
										}							
									}
									// Ist im Bau
									elseif (isset($techlist[$bid]['techlist_build_type']) && $techlist[$bid]['techlist_build_type']==3)
									{
										$subtitle =  "Forschung auf Stufe ".($b_level+1);
										$tmtext = "<span style=\"color:#0f0\">Wird erforscht!<br/>Dauer: ".tf($end_time-time())."</span><br/>";
										$color = '#0f0';
										if($use_img_filter)
										{
											$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."&filter=building";
										}
										else
										{
											$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."";
										}
									}
									// Untätig
									else
									{
	              	  // Baukostenberechnung          Baukosten = Grundkosten * (Kostenfaktor ^ Ausbaustufe)
	              	  $bc['metal'] = $bv['tech_costs_metal'] * $cu->specialist->costsResearch * pow($bv['tech_build_costs_factor'],$b_level);
	              	  $bc['crystal'] = $bv['tech_costs_crystal'] * $cu->specialist->costsResearch * pow($bv['tech_build_costs_factor'],$b_level);
	              	  $bc['plastic'] = $bv['tech_costs_plastic'] * $cu->specialist->costsResearch * pow($bv['tech_build_costs_factor'],$b_level);
	              	  $bc['fuel'] = $bv['tech_costs_fuel'] * $cu->specialist->costsResearch * pow($bv['tech_build_costs_factor'],$b_level);
	              	  $bc['food'] = $bv['tech_costs_food'] * $cu->specialist->costsResearch * pow($bv['tech_build_costs_factor'],$b_level);
										
										// Zuwenig Ressourcen
										if($b_level<$bv['last_level'] && ($cp->resMetal < $bc['metal'] || $cp->resCrystal < $bc['crystal']  || $cp->resPlastic < $bc['plastic']  || $cp->resFuel < $bc['fuel']  || $cp->resFood < $bc['food']))
										{
											$tmtext = "<span style=\"color:#f00\">Zuwenig Ressourcen f&uuml;r<br/>weitere Forschungen!</span><br/>";
											$color = '#f00';
											if($use_img_filter)
											{
												$img = "misc/imagefilter.php?file=".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."&filter=lowres";
											}
											else
											{
												$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."";
											}
										}
										else
										{
											$tmtext = "";
											$color = '#fff';
											$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid.".".IMAGE_EXT."";
										}
										
										if ($b_level==0)
										{
											$subtitle = "Noch nicht erforscht";
										}
										elseif ($b_level>=$bv['last_level'])
										{
											$subtitle = 'Vollständig erforscht';
										}
										else
										{
											$subtitle = 'Stufe '.$b_level.'';
										}
									}
	              }

	              
								// Display all buildings that are buildable or are already built
								if (($bv['show']==1) || $b_level>0)
								{			
									$img="".IMAGE_PATH."/".IMAGE_TECHNOLOGY_DIR."/technology".$bid."_middle.".IMAGE_EXT."";
	
									if (!$requirements_passed)
										$img = "misc/imagefilter.php?file=$img&filter=req";
	
									// Display row starter if needed				
									if ($cnt==0) 
									{
										echo "<tr>";
									}
									
									if ($cu->properties->cssStyle=="Classic" || $cu->properties->cssStyle=="Dark")
									{
										echo "<td style=\"color:".$color.";text-align:center;width:".CELL_WIDTH."\">
													<b>".$bv['name']."";
													if ($b_level>0) echo ' '.$b_level;
													echo "</b><br/>".$subtitle."<br/>
													<input name=\"show_".$bid."\" type=\"image\" value=\"".$bid."\" src=\"".$img."\" ".tm($bv['name'],$tmtext.$bv['shortcomment'])." style=\"width:120px;height:120px;\" />
									</td>\n";
									}
									else
									{
										echo "<td style=\"background:url('".$img."') no-repeat;width:".CELL_WIDTH."px;height:".CELL_WIDTH."px ;padding:0px;\">
											<div style=\"position:relative;height:".CELL_WIDTH."px;overflow:hidden\">
											<div class=\"buildOverviewObjectTitle\">".$bv['name']."</div>";
										echo "<a href=\"?page=$page&amp;id=".$bid."\" ".tm($bv['name'],"<b>".$subtitle."</b><br/>".$tmtext.$bv['shortcomment'])." style=\"display:block;height:180px;\"></a>";
										if ($b_level>0 || ($b_level==0 && isset($techlist[$bid]['techlist_build_type']) && $techlist[$bid]['techlist_build_type']==3)) 
										{
											echo "<div class=\"buildOverviewObjectLevel\" style=\"color:".$color."\">".$b_level."</div>";
										}
										echo "</div></td>\n";
									}

									/*
										echo "<td class=\"tbldata\" style=\"color:".$color.";text-align:center;width:".CELL_WIDTH."px\">
													<b>".$bv['name']."";
													if ($b_level>0) echo ' '.$b_level;
													echo "</b><br/>".$subtitle."<br/>
													<input name=\"show_".$bid."\" type=\"image\" value=\"".$bid."\" src=\"".$img."\" ".tm($bv['name'],$tmtext.$bv['shortcomment'])." style=\"width:120px;height:120px;\" />
									</td>\n";
									*/
									
									$cnt++;
									$scnt++;
								}
									
								// Display row finisher if needed			
								if ($cnt==NUM_BUILDINGS_PER_ROW)
								{
									echo "</tr>";
									$cnt = 0;
								}	
							}
	
							// Fill up missing cols and end row
							if ($cnt<NUM_BUILDINGS_PER_ROW && $cnt>0)
							{
								for ($x=0;$x < NUM_BUILDINGS_PER_ROW-$cnt;$x++)
								{
									echo "<td class=\"buildOverviewObjectNone\" style=\"width:".CELL_WIDTH."px;padding:0px;\">&nbsp;</td>";
								}
								echo '</tr>';
							}							
							
							// Display message if no tech can be researched
							if ($scnt==0)
							{								
								echo "<tr>
												<td class=\"tbldata\" colspan=\"".NUM_BUILDINGS_PER_ROW."\" style=\"text-align:center;border:0;width:100%\">
													<i>In dieser Kategorie kann momentan noch nichts geforscht werden!</i>
												</td>
											</tr>";								
							}	
	
						}
						else
						{
							echo "<tr><td class=\"tbldata\" colspan=\"4\" style=\"text-align:center;border:0;width:100%\"><i>In dieser Kategorie kann momentan noch nichts erforscht werden!</i></td></tr>";
						}
						tableEnd();
					}
					echo '</div></form>';				
				}
				else
				{
					echo "<i>Es k&ouml;nnen noch keine Forschungen erforscht werden!</i>";
				}
			}
			
		}
		else
		{
			echo "<h1>Forschungslabor des Planeten ".$cp->name."</h1>";
			echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);
			info_msg("Das Forschungslabor wurde noch nicht gebaut!");
		}
	}
	// ENDE SKRIPT //

	?>
