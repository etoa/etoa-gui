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
	
	// Zeigt eigene Rohstoffe an
	echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);
	
	echo "<h2><a href=\"?page=".$page."&amp;action=".$_GET['action']."\">Allianzbasis</a></h2>";
	
	// Schiffswerft gebaut?
	$shipyard = ($cu->alliance->buildlist->getLevel(ALLIANCE_SHIPYARD_ID)>=1) ? TRUE : FALSE;
	$research = ($cu->alliance->buildlist->getLevel(ALLIANCE_RESEARCH_ID)>=1) ? TRUE : FALSE;
	
	//
	// Navigation
	//
	
	// Speichert Tab
	if(isset($_GET['action2']))
	{
		$action2 = $_GET['action2'];
	}
	else
	{
		$action2 = "buildings";
	}
	
	// Stellt standart Links dar
	/*
	echo "<a href=\"javascript:;\" onclick=\"showTab('tabBuildings')\">Gebäude</a> | 
	<a href=\"javascript:;\" onclick=\"showTab('tabResearch')\">Technologien</a> | 
	<a href=\"javascript:;\" onclick=\"showTab('tabStorage')\">Speicher</a>";
	*/
	
	$ddm = new DropdownMenu(1);
	$ddm->add('b','Gebäude',"showTab('tabBuildings');");
	if ($research) {
		$ddm->add('r','Technologien',"showTab('tabResearch');");
	}
	$ddm->add('s','Speicher',"showTab('tabStorage');");
	if($shipyard) {
		$ddm->add('sw','Schiffswerft',"showTab('tabShipyard');");
	}
	echo $ddm; 
	
	echo "<br>";
		
		
	//
	// Funktionen				
	//
	
	echo "<script type=\"text/javascript\">
	
	// Wechselt zwischen den Verschiedenen Tabs
	function showTab(idx)
	{
		document.getElementById('tabBuildings').style.display='none';
		document.getElementById('tabResearch').style.display='none';
		document.getElementById('tabStorage').style.display='none';
		document.getElementById('tabShipyard').style.display='none';
		
		document.getElementById(idx).style.display='';
	}
	
	// Schreibt definierte Zahlen in die Einzahlen-Felder und wechselt auf diese Seite
	function setSpends(metal, crystal, plastic, fuel, food)
	{
		document.getElementById('spend_metal').value=metal;
		document.getElementById('spend_crystal').value=crystal;
		document.getElementById('spend_plastic').value=plastic;
		document.getElementById('spend_fuel').value=fuel;
		document.getElementById('spend_food').value=food;
		
		// Wechselt Tab
		showTab('tabStorage');
		
		// Wenn zu wenig Rohstoffe auf dem aktuellen Planeten sind, wird eine Nachricht ausgegeben
		if(".$cp->resMetal."<metal
				|| ".$cp->resCrystal."<crystal
				|| ".$cp->resPlastic."<plastic
				|| ".$cp->resFuel."<fuel
				|| ".$cp->resFood."<food)
		{
			alert('Du hast nicht genügend Rohstoffe auf dem aktuellen Planeten!');
		}
	}
	
	// Ändert Rohstoff Box Zahlen
	function changeResBox(metal, crystal, plastic, fuel, food)
	{
		document.getElementById('resBoxMetal').innerHTML=FormatNumber('return',metal,'','','');
		document.getElementById('resBoxCrystal').innerHTML=FormatNumber('return',crystal,'','','');
		document.getElementById('resBoxPlastic').innerHTML=FormatNumber('return',plastic,'','','');
		document.getElementById('resBoxFuel').innerHTML=FormatNumber('return',fuel,'','','');
		document.getElementById('resBoxFood').innerHTML=FormatNumber('return',food,'','','');
	}
	
	</script>";	
 	
	
 	//
 	// Einzahlen
 	//
 	
 	if(isset($_POST['storage_submit']) && checker_verify())
 	{
 		// Formatiert Eingaben 
 		$metal = nf_back($_POST['spend_metal']);
		$crystal = nf_back($_POST['spend_crystal']);
		$plastic = nf_back($_POST['spend_plastic']);
		$fuel = nf_back($_POST['spend_fuel']);
		$food = nf_back($_POST['spend_food']);
		
		// Prüft, ob Rohstoffe angegeben wurden
		if($metal>0 
		   || $crystal>0
		   || $plastic>0
		   || $fuel>0
		   || $food>0)
		{
			// Prüft, ob Rohstoffe noch vorhanden sind
			if($cp->getRes(1) >= $metal
				&& $cp->getRes(2) >= $crystal
				&& $cp->getRes(3) >= $plastic
				&& $cp->getRes(4) >= $fuel
				&& $cp->getRes(5) >= $food) 	
			{
				// Rohstoffe vom Planet abziehen
				$res = array($metal,$crystal,$plastic,$fuel,$food);
				$cp->subRes($res);
				  
				// Rohstoffe der Allianz gutschreiben
				$cu->alliance->changeRes($metal,$crystal,$plastic,$fuel,$food);
				
				// Spende speichern
				dbquery("
						INSERT INTO
							alliance_spends
				 		(
						 	alliance_spend_alliance_id,
							alliance_spend_user_id,
							alliance_spend_metal,
							alliance_spend_crystal,
							alliance_spend_plastic,
							alliance_spend_fuel,
							alliance_spend_food,
							alliance_spend_time
						)
						VALUES
				  		(
						 	'".$cu->allianceId."',
				 			'".$cu->id."',
				  			'".$metal."',
				  			'".$crystal."',
				  			'".$plastic."',
				  			'".$fuel."',
				  			'".$food."',
				  			'".time()."'
						)");

				ok_msg("Rohstoffe erfolgreich eingezahlt!");
			}
			else
				error_msg("Es sind zu wenig Rohstoffe auf dem Planeten!");
		}
		else
			error_msg("Du hast keine Rohstoffe angegeben!");
	}
 	
 	// Einzahlungs Filter aktivieren

	// Default Werte setzen
	$sum = false;
	$limit = 10;
	$user = 0;
	if(isset($_POST['filter_submit']) && checker_verify())
	{
		// Summierung der Einzahlungen
		if(isset($_POST['output']) && $_POST['output']==1)
	  {
	  	$sum = true;
	  }
		
		// Limit
		if(isset($_POST['limit']) && $_POST['limit']>0)
		{
			$limit = $_POST['limit'];
		}
		
		// User
		if(isset($_POST['user_spends']) && $_POST['user_spends']>0)
	  {
	  	$user = $_POST['user_spends'];
	  }
	}
	
	//
	// Läd Daten
	//	
	
	// Allianzschiffe (wenn Schiffswerft gebaut)
	if($shipyard)
	{
		
		$res = dbquery("
		SELECT
			ship_id,
			ship_name,
			ship_longcomment,
			ship_speed,
			ship_time2start,
			ship_time2land,
			ship_structure,
			ship_shield,
			ship_weapon,
			ship_max_count,
			ship_alliance_shipyard_level,
			ship_alliance_costs
		FROM
			ships
		WHERE
			ship_alliance_shipyard_level<='".$cu->alliance->buildlist->getLevel(ALLIANCE_SHIPYARD_ID)."'
			AND ship_alliance_shipyard_level>0
		ORDER BY
			ship_alliance_shipyard_level;");
		while($arr=mysql_fetch_assoc($res))		
		{
			$ships[$arr['ship_id']] = $arr;
		}
	}
	
	// Userschiffe laden (wenn Schiffswerft gebaut=
	// Gebaute Schiffe laden
	$res = dbquery("
	SELECT
		shiplist_ship_id,
		shiplist_entity_id,
		shiplist_count
	FROM
		shiplist
	WHERE
		shiplist_user_id='".$cu->id."';");
	while ($arr = mysql_fetch_assoc($res))
	{
		$shiplist[$arr['shiplist_ship_id']][$arr['shiplist_entity_id']]=$arr['shiplist_count'];
	}
	
	// Bauliste von allen Planeten laden und nach Schiffe zusammenfassen
	$res = dbquery("
	SELECT
		queue_id,
		queue_ship_id,
		SUM(queue_cnt) AS cnt
	FROM
		ship_queue
	WHERE
		queue_user_id='".$cu->id."'
		AND queue_endtime>'".$time."'
	GROUP BY
		queue_ship_id;");
	while ($arr = mysql_fetch_assoc($res))
	{
		$queue_total[$arr['queue_ship_id']] = $arr['cnt'];
	}
	
	// Flotten laden und nach Schiffe zusammenfassen
	$res = dbquery("
		SELECT
			fs_ship_id,
			SUM(fs.fs_ship_cnt) AS cnt
		FROM
			fleet AS f
		INNER JOIN
			fleet_ships AS fs
		ON f.id=fs.fs_fleet_id
		WHERE
			f.user_id='".$cu->id."'
		GROUP BY
			fs.fs_ship_id;");
	while ($arr = mysql_fetch_assoc($res))
	{
		$fleet[$arr['fs_ship_id']] = $arr['cnt'];
	}
	
		
	//
	// Schiffe kaufen
	//
		
	if(isset($_POST['ship_submit']) && checker_verify())
	{
		if ($cu->alliance->checkActionRightsNA("buildminister") || $cu->id==$_POST['user_buy_ship'])
		{
			// Prüft, ob ein User gewählt wurde
			if($_POST['user_buy_ship']>0)
			{
				// Gebaute Schiffe laden
				$res = dbquery("
				SELECT
					shiplist_ship_id,
					shiplist_entity_id,
    				shiplist_count,
					shiplist_bunkered
				FROM
    				shiplist
				WHERE
  					shiplist_user_id='".$_POST['user_buy_ship']."';");
				while ($arr = mysql_fetch_assoc($res))
				{
					$shiplist[$arr['shiplist_ship_id']][$arr['shiplist_entity_id']]=$arr['shiplist_count']+$arr['shiplist_bunkered'];
				}
				
				// Bauliste von allen Planeten laden und nach Schiffe zusammenfassen
				$res = dbquery("
				SELECT
    				queue_id,
    				queue_ship_id,
    				SUM(queue_cnt) AS cnt
				FROM
    				ship_queue
				WHERE
		  			queue_user_id='".$_POST['user_buy_ship']."'
  					AND queue_endtime>'".$time."'
  				GROUP BY
    				queue_ship_id;");
				while ($arr = mysql_fetch_assoc($res))
				{
					$queue_total[$arr['queue_ship_id']] = $arr['cnt'];
				}
				
				// Flotten laden und nach Schiffe zusammenfassen
				$res = dbquery("
    			  	SELECT
      					fs_ship_id,
       					SUM(fs.fs_ship_cnt) AS cnt
					FROM
       					fleet AS f
					INNER JOIN
         				fleet_ships AS fs
       				ON f.id=fs.fs_fleet_id
	    			WHERE
	      				f.user_id='".$_POST['user_buy_ship']."'
	    			GROUP BY
	    				fs.fs_ship_id;");
				while ($arr = mysql_fetch_assoc($res))
				{
					$fleet[$arr['fs_ship_id']] = $arr['cnt'];
				}
				
				$ship_costs = 0;
				$total_build_cnt = 0;
				$to_much = false;
				foreach ($_POST['buy_ship'] as $ship_id => $build_cnt)
				{
					// Formatiert die eingegebene Zahl (entfernt z.B. die Trennzeichen)
					$build_cnt = nf_back($build_cnt);
					
					if($build_cnt>0)
					{
						// Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
		    			$ship_count = 0;
		      			// ... auf den Planeten
		      			if(isset($shiplist[$ship_id]))
		      			{
		      				$ship_count += array_sum($shiplist[$ship_id]);
		      			}
		      			// ... in der Bauliste
		      			if(isset($queue_total[$ship_id]))
		      			{
		      				$ship_count += $queue_total[$ship_id];
		      			}
						// ... in der Luft
		      			if(isset($fleet[$ship_id]))
		      			{
		      				$ship_count += $fleet[$ship_id];
		      			}
						
		      			// Total Schiffe mit den zu bauenden
						$total_count = $build_cnt + $ship_count;
						
						// Prüft ob Anzahl grösser ist als Schiffsmaximum
						if($ships[$ship_id]['ship_max_count'] >= $total_count || $ships[$ship_id]['ship_max_count'] == 0)
						{
							for ($i=$build_cnt-1; $i>=0; $i--)
							{
								//Kostenfaktor Schiffe
								$cost_factor = pow($cfg->get("alliance_shipcosts_factor"),$ship_count+$i);
								// Berechnet die Kosten
								$ship_costs += $cost_factor * $ships[$ship_id]['ship_alliance_costs'];								
							}

						}
						// Die Anzahl übersteigt die Max. Anzahl -> Nachricht wird ausgegeben
						else
						{
							$to_much = true;
						}
						$total_build_cnt += $build_cnt;
					}
				}
				
				// Prüft, ob die Maximalanzahl nicht überschritten wird
				if(!$to_much)
				{

					
					if ($total_build_cnt>0)
					{
							// Prüft ob Schiffspunkte noch ausreichend sind
							if($cu->alliance->members[$_POST['user_buy_ship']]->allianceShippoints >= $ship_costs)
							{
								// Zieht Punkte vom Konto ab
								dbquery("
									UPDATE
										users
									SET
										user_alliace_shippoints=user_alliace_shippoints-'".$ship_costs."',
										user_alliace_shippoints_used=user_alliace_shippoints_used+'".$ship_costs."'
									WHERE
										user_id='".$_POST['user_buy_ship']."'
								");
								$ship_costed = $ship_costs;
								
								// Lädt das Allianzentity
								$res = dbquery("
								SELECT
									id
								FROM
									entities
								WHERE
									code='x';");		
								$row = mysql_fetch_row($res);
								
								
								// Speichert Flotte
								$launchtime = time(); // Startzeit
								$duration = 3600; // Dauer 1h
								$landtime = $launchtime + $duration; // Landezeit
								dbquery("
										INSERT INTO fleet
										(
											user_id,
											entity_from,
											entity_to,
											launchtime,
											landtime,
											action
										)
										VALUES
										(
											'".$_POST['user_buy_ship']."',
											'".$row[0]."',
											'".$cp->id."',
											'".$launchtime."',
											'".$landtime."',
											'delivery'
										);");
										
								// Speichert Schiffe in der Flotte
								$sql = "";
								$log = "";
								$cnt = 0;
								foreach ($_POST['buy_ship'] as $ship_id => $build_cnt)
								{
									// Formatiert die eingegebene Zahl (entfernt z.B. die Trennzeichen)
									$build_cnt = nf_back($build_cnt);
									
									if($build_cnt>0)
									{
										// Stellt SQL-String her
										if($cnt==0)
										{
											$sql .= "('".mysql_insert_id()."', '".$ship_id."', '".$build_cnt."')";
											$fleet[$ship_id] += $build_cnt;
											// Gibt einmalig eine OK-Medlung aus
											ok_msg("Schiffe wurden erfolgreich hergestellt!");
										}
										else
										{
											$sql .= ", ('".mysql_insert_id()."', '".$ship_id."', '".$build_cnt."')";
										}
										
										// Listet gewählte Schiffe für Log auf
										$log .= "[b]".$_POST['ship_name_'.$ship_id.''].":[/b] ".nf($build_cnt)."\n";
										
										$cnt++;
									}
								}
								// Speichert Schiffe durch durch den generierten String
								dbquery("
										INSERT INTO
										fleet_ships
										(
											fs_fleet_id,
											fs_ship_id,
											fs_ship_cnt
										)
										VALUES
											".$sql."
										;");
						
								// Zur Allianzgeschichte hinzufügen
								add_alliance_history($cu->allianceId,"Folgende Schiffe wurden für [b]".get_user_nick($_POST['user_buy_ship'])."[/b] hergestellt:\n".$log."\n".nf($ship_costs)." Teile wurden dafür benötigt.");
							}
							else
							{
								error_msg("Der gewählte User hat nicht genügend Teile übrig!");
							}
						}
						else
						{
							error_msg("Keine Schiffe ausgewählt!");
						}
					}
					else
					{
						error_msg("Die Maximalanzahl der Schiffe würde mit der eingegebenen Menge überschritten werden!");
					}
				}
				else
				{
					error_msg("Es wurde kein User ausgewählt!");
				}
		}
		else
		{
			$error_msg("Keine Berechtigung!");
		}
	}	
		
	
	//
	// ResBox
	//	
	
	$style0 = "";
	$style1 = "";
	$style2 = "";
	$style3 = "";
	$style4 = "";

	// Negative Rohstoffe farblich hervorben
	if ($cu->alliance->resMetal < 0)
	{
		$style0 = "style=\"color:red;\"";
	}
	if ($cu->alliance->resCrystal < 0)
	{
		$style1 = "style=\"color:red;\"";
	}
	if ($cu->alliance->resPlastic < 0)
	{
		$style2 = "style=\"color:red;\"";
	}
	if ($cu->alliance->resFuel < 0)
	{
		$style3 = "style=\"color:red;\"";
	}
	if ($cu->alliance->resFood < 0)
	{
		$style4 = "style=\"color:red;\"";
	}
	
	
	tableStart("Allianz Ressourcen");
	echo "<tr>
					<th style=\"width:20%;vertical-align:middle;\">".RES_ICON_METAL." ".RES_METAL."</th>
					<th style=\"width:20%;vertical-align:middle;\">".RES_ICON_CRYSTAL." ".RES_CRYSTAL."</th>
					<th style=\"width:20%;vertical-align:middle;\">".RES_ICON_PLASTIC." ".RES_PLASTIC."</th>
					<th style=\"width:20%;vertical-align:middle;\">".RES_ICON_FUEL." ".RES_FUEL."</th>
					<th style=\"width:20%;vertical-align:middle;\">".RES_ICON_FOOD." ".RES_FOOD."</th>
				</tr>
				<tr>
					<td ".$style0." id=\"resBoxMetal\">".nf($cu->alliance->resMetal)." t</td>
					<td ".$style1." id=\"resBoxCrystal\">".nf($cu->alliance->resCrystal)." t</td>
					<td ".$style2."id=\"resBoxPlastic\">".nf($cu->alliance->resPlastic)." t</td>
					<td ".$style3."id=\"resBoxFuel\">".nf($cu->alliance->resFuel)." t</td>
					<td ".$style4."id=\"resBoxFood\">".nf($cu->alliance->resFood)." t</td>
				</tr>";
 	tableEnd();
 	
 	
 	
 	

 	
 	
 	//
 	// Content Laden
 	//
	
	
 	//
 	// Datenverarbeitung 2: Muss nach dem Laden der Daten geschehen
 	// -> Gebäude und Techs speichern
 	//
	
	// Gebäude in Auftrag geben
	if(isset($_POST['building_submit']) && checker_verify())
	{
		if (Alliance::checkActionRights("buildminister"))
		{
			if(isset($_POST['building_id']) && $_POST['building_id']!=0)
			{
				if ($cu->alliance->buildlist->build($_POST['building_id']))
					ok_msg("Gebäude wurde erfolgreich in Auftrag gegeben!");
				else
					error_msg($cu->alliance->buildlist->getLastError());
			}
		}
	}
	
	
	// Technologie in Auftrag geben
	if(isset($_POST['research_submit']) && checker_verify())
	{
		if (Alliance::checkActionRights("buildminister"))
		{
			if(isset($_POST['research_id']) && $_POST['research_id']!=0)
			{			
				if ($cu->alliance->techlist->build($_POST['research_id']))
					ok_msg("Forschung wurde erfolgreich in Auftrag gegeben!");
				else
					error_msg($cu->alliance->techlist->getLastError());
			}
		}
	}
	
	$allianceRes = array(1=>$cu->alliance->resMetal,
						 2=>$cu->alliance->resCrystal,
						 3=>$cu->alliance->resPlastic,
						 4=>$cu->alliance->resFuel,
						 5=>$cu->alliance->resFood);
	$resName = array(1=>RES_METAL,
					 2=>RES_CRYSTAL,
					 3=>RES_PLASTIC,
					 4=>RES_FUEL,
					 5=>RES_FOOD);
	
	
	//
	// Gebäude
	//
	
	if($action2=="buildings")
	{
		$display = "";
	}
	else
	{
		$display = "none";
	}
	echo "<div id=\"tabBuildings\" style=\"display:".$display.";\">";
	echo "<form action=\"?page=".$page."&amp;action=".$_GET['action']."&amp;action2=buildings\" method=\"post\" id=\"alliance_buildings\">\n";
	$cstr=checker_init();
		
	// Mit diesem Feld wird die Gebäude ID vor dem Absenden übergeben
	echo "<input type=\"hidden\" value=\"0\" name=\"building_id\" id=\"building_id\" />";	
		
	// Es sind Gebäude vorhanden
	if($cu->alliance->buildlist->count())
	{	
		$buildingIterator = $cu->alliance->buildlist->getIterator();
		while($buildingIterator->valid())
		{
			$style_message = '';
			if ($cu->alliance->buildlist->show($buildingIterator->key()))
			{
				$level = $cu->alliance->buildlist->getLevel($buildingIterator->key());
				$title = $buildingIterator->current().' <span id="buildlevel">';
				$title.= ($level > 0) ? $level : '';
				$title.= '</span>';
				tableStart($title);
				echo "<tr>
				  <td style=\"width:120px;background:#000;vertical-align:middle;padding:0px;\">"
					.$buildingIterator->current()->imgMiddle()."
				  </td>
				  <td style=\"vertical-align:top;height:100px;\" colspan=\"6\">
					".$buildingIterator->current()->longDesc."
					</td>
					 </tr>";
				//
				// Baumenü
				//
				
				echo "<tr>";
				if ($cu->alliance->buildlist->isMaxLevel($buildingIterator->key()))
					echo "<td colspan=\"7\" style=\"text-align:center;\">Maximallevel erreicht!</td>";
				else
				{
					$costs = $buildingIterator->current()->getCosts($level+1,$cu->alliance->memberCount);
					$need_something = false;
					foreach ($allianceRes as $id=>$res)
					{
						if ($res>=$costs[$id])
						{
							$need[$id] = 0;
							$style[$id] = "";
						}
						else
						{
							$need_something = true;
							
							// Erstellt absolut Wert der Zahl
							$need[$id] = abs($costs[$id]-$res);
							$style[$id] =  "style=\"color:red;\" ".tm("Fehlender Rohstoff","".nf($need[$id])." ".$resName[$id]."")."";
						}
					}
					
					if ($cu->alliance->buildlist->checkBuildable($buildingIterator->key()))
					{
						if($level==0)
							$build_button = "Bauen";
						else
							$build_button = "Ausbauen";
							
						// Generiert Baubutton, mit welchem vor dem Absenden noch die Objekt ID übergeben wird
						$message = "<input type=\"submit\" class=\"button\" name=\"building_submit\" id=\"building_submit\" value=\"".$build_button."\" onclick=\"document.getElementById('building_id').value=".$buildingIterator->key().";\"/>";
					}
					else
					{
						if ($cu->alliance->buildlist->isUnderConstruction())
						{
							if ($cu->alliance->buildlist->isUnderConstruction($buildingIterator->key()))
							{
								$style_message = "color: rgb(0, 255, 0);";
								$message = startTime($cu->alliance->buildlist->isUnderConstruction($buildingIterator->key())-time(), 'build_message_building_'.$buildingIterator->key().'', 0, 'Wird ausgebaut auf Stufe '.($level+1).' (TIME)');
							}
							else
							{
								$message = $cu->alliance->buildlist->getLastError();
								$style_message = "color: rgb(255, 0, 0);";
							}
						}
						elseif ($need_something)
						{
							$message = "<input type=\"button\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Fehlende Rohstoffe einzahlen\" ".tm("Nicht genügend Rohstoffe","Es sind nicht genügend Rohstoffe vorhanden!<br>Klick auf den Button um die fehlenden Rohstoffe einzuzahlen.")." onclick=\"setSpends(".$need[1].", ".$need[2].", ".$need[3].", ".$need[4].", ".$need[5].");\"/>";
						}
						else
							$message = $cu->alliance->buildlist->getLastError();
					}
					echo "<th width=\"7%\">Stufe</th>
						<th width=\"18%\">Zeit</th>
						<th width=\"15%\">".RES_METAL."</th>
						<th width=\"15%\">".RES_CRYSTAL."</th>
						<th width=\"15%\">".RES_PLASTIC."</th>
						<th width=\"15%\">".RES_FUEL."</th>
						<th width=\"15%\">".RES_FOOD."</th>
					</tr><tr>
						<td width=\"7%\">".($level+1)."</th>
						<td width=\"18%\">".tf($cu->alliance->buildlist->getBuildTime($buildingIterator->key(),$level+1))."</th>
						<td ".$style[1]." width=\"15%\">".nf($costs[1])."</td>
						<td ".$style[2]." width=\"15%\">".nf($costs[2])."</td>
						<td ".$style[3]." width=\"15%\">".nf($costs[3])."</td>
						<td ".$style[4]." width=\"15%\">".nf($costs[4])."</td>
						<td ".$style[5]." width=\"15%\">".nf($costs[5])."</td>
					</tr>
					<tr>
						<td colspan=\"7\" style=\"text-align:center;".$style_message."\" name=\"build_message_building_".$buildingIterator->key()."\" id=\"build_message_building_".$buildingIterator->key()."\">".$message."</td>";
				}
				echo "</tr>";
				tableEnd();
			}
			
			$buildingIterator->next();
		}
	}
	// Es sind noch keine Gebäude vorhanden
	else
	{
		error_msg("Es sind noch keine Gebäude definiert!");
	}
	
	echo "</form>";
	echo "</div>";
	
	
	
	//
	// Forschungen
	//
	
	
	if($action2=="research")
	{
		$display = "";
	}
	else
	{
		$display = "none";
	}
	echo "<div id=\"tabResearch\" style=\"display:".$display.";\">";
	echo "<form action=\"?page=".$page."&amp;action=".$_GET['action']."&amp;action2=research\" method=\"post\" id=\"alliance_research\">\n";
	echo $cstr;
		
	// Mit diesem Feld wird die Tech ID vor dem Absenden übergeben
	echo "<input type=\"hidden\" value=\"0\" name=\"research_id\" id=\"research_id\" />";	
		
	// Es sind Technologien vorhanden
	// Es sind Gebäude vorhanden
	if($research && $cu->alliance->techlist->count())
	{	
		$techIterator = $cu->alliance->techlist->getIterator();
		while($techIterator->valid())
		{
			$style_message = '';
			if ($cu->alliance->techlist->show($techIterator->key()))
			{
				$level = $cu->alliance->techlist->getLevel($techIterator->key());
				$title = $techIterator->current().' <span id="buildlevel">';
				$title.= ($level > 0) ? $level : '';
				$title.= '</span>';
				tableStart($title);
				echo "<tr>
				  <td style=\"width:120px;background:#000;vertical-align:middle;padding:0px;\">"
					.$techIterator->current()->imgMiddle()."
				  </td>
				  <td style=\"vertical-align:top;height:100px;\" colspan=\"6\">
					".$techIterator->current()->longDesc."
					</td>
					 </tr>";
				//
				// Baumenü
				//
				
				echo "<tr>";
				if ($cu->alliance->techlist->isMaxLevel($techIterator->key()))
					echo "<td colspan=\"7\" style=\"text-align:center;\">Maximallevel erreicht!</td>";
				else
				{
					$costs = $techIterator->current()->getCosts($level+1,$cu->alliance->memberCount);
					$need_something = false;
					foreach ($allianceRes as $id=>$res)
					{
						if ($res>$costs[$id])
						{
							$need[$id] = 0;
							$style[$id] = "";
						}
						else
						{
							$need_something = true;
							
							// Erstellt absolut Wert der Zahl
							$need[$id] = abs($costs[$id]-$res);
							$style[$id] =  "style=\"color:red;\" ".tm("Fehlender Rohstoff","".nf($need[$id])." ".$resName[$id]."")."";
						}
					}
					
					
					if ($cu->alliance->techlist->checkBuildable($techIterator->key()))
					{		
						// Generiert Baubutton, mit welchem vor dem Absenden noch die Objekt ID übergeben wird
						$message = "<input type=\"submit\" class=\"button\" name=\"research_submit\" id=\"research_submit\" value=\"Erforschen\" onclick=\"document.getElementById('research_id').value=".$techIterator->key().";\"/>";
					}
					else
					{
						if ($cu->alliance->techlist->isUnderConstruction())
						{
							if ($cu->alliance->techlist->isUnderConstruction($techIterator->key()))
							{
								$style_message = "color: rgb(0, 255, 0);";
								$message = startTime($cu->alliance->techlist->isUnderConstruction($techIterator->key())-time(), 'build_message_research_'.$techIterator->key().'', 0, 'Wird ausgebaut auf Stufe '.($level+1).' (TIME)');
							}
							else
							{
								$message = $cu->alliance->techlist->getLastError();
								$style_message = "color: rgb(255, 0, 0);";
							}
						}
						elseif ($need_something)
						{
							$message = "<input type=\"button\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Fehlende Rohstoffe einzahlen\" ".tm("Nicht genügend Rohstoffe","Es sind nicht genügend Rohstoffe vorhanden!<br>Klick auf den Button um die fehlenden Rohstoffe einzuzahlen.")." onclick=\"setSpends(".$need[1].", ".$need[2].", ".$need[3].", ".$need[4].", ".$need[5].");\"/>";
						}
					}
					echo "<th width=\"7%\">Stufe</th>
						<th width=\"18%\">Zeit</th>
						<th width=\"15%\">".RES_METAL."</th>
						<th width=\"15%\">".RES_CRYSTAL."</th>
						<th width=\"15%\">".RES_PLASTIC."</th>
						<th width=\"15%\">".RES_FUEL."</th>
						<th width=\"15%\">".RES_FOOD."</th>
					</tr><tr>
						<td width=\"7%\">".($level+1)."</th>
						<td width=\"18%\">".tf($cu->alliance->techlist->getBuildTime($techIterator->key(),$level+1))."</th>
						<td ".$style[1]." width=\"15%\">".nf($costs[1])."</td>
						<td ".$style[2]." width=\"15%\">".nf($costs[2])."</td>
						<td ".$style[3]." width=\"15%\">".nf($costs[3])."</td>
						<td ".$style[4]." width=\"15%\">".nf($costs[4])."</td>
						<td ".$style[5]." width=\"15%\">".nf($costs[5])."</td>
					</tr>
					<tr>
						<td colspan=\"7\" style=\"text-align:center;".$style_message."\" name=\"build_message_research_".$techIterator->key()."\" id=\"build_message_research_".$techIterator->key()."\">".$message."</td>";
				}
				echo "</tr>";
				tableEnd();
			}
			$techIterator->next();
		}
	}
	// Es sind noch keine Gebäude vorhanden
	else
	{
		error_msg("Es sind noch keine Technologien definiert!");
	}
	
	echo "</form>";
	echo "</div>";
	
	
	
	//
	// Speicher + Einzahlungen
	//
	
	if($action2=="storage")
	{
		$display = "";
	}
	else
	{
		$display = "none";
	}
	echo "<div id=\"tabStorage\" style=\"display:".$display.";\">";

 	echo "<form action=\"?page=".$page."&amp;action=".$_GET['action']."&amp;action2=storage\" method=\"post\" id=\"alliance_storage\">\n";
	echo $cstr;

	tableStart("Rohstoffe einzahlen");	
	
	// Titan
	echo "<tr>
					<th style=\"width:100px;\">".RES_METAL."</th>
					<td style=\"width:150px;\">
						<input type=\"text\" value=\"0\" name=\"spend_metal\" id=\"spend_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resMetal.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_metal').value='".nf($cp->resMetal)."';\">alles</a>
					</td>
				</tr>";
	// Silizium
	echo "<tr>
					<th>".RES_CRYSTAL."</th>
					<td>
						<input type=\"text\" value=\"0\" name=\"spend_crystal\" id=\"spend_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resCrystal.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_crystal').value='".nf($cp->resCrystal)."';\">alles</a>
					</td>
				</tr>";		
	// PVC
	echo "<tr>
					<th>".RES_PLASTIC."</th>
					<td>
						<input type=\"text\" value=\"0\" name=\"spend_plastic\" id=\"spend_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resPlastic.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_plastic').value='".nf($cp->resPlastic)."';\">alles</a>
					</td>
				</tr>";	
	// Tritium
	echo "<tr>
					<th>".RES_FUEL."</th>
					<td>
						<input type=\"text\" value=\"0\" name=\"spend_fuel\" id=\"spend_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFuel.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_fuel').value='".nf($cp->resFuel)."';\">alles</a>
					</td>
				</tr>";	
	// Nahrung
	echo "<tr>
					<th>".RES_FOOD."</th>
					<td>
						<input type=\"text\" value=\"0\" name=\"spend_food\" id=\"spend_food\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFood.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_food').value='".nf($cp->resFood)."';\">alles</a>
					</td>
				</tr>";			
	tableEnd();
	
	echo "<input type=\"submit\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Einzahlen\"/>";
	echo "</form><br><br><br><br>";


	//
	// Einzahlungen
	//

 	echo "<form action=\"?page=".$page."&amp;action=".$_GET['action']."&amp;action2=storage\" method=\"post\" id=\"alliance_spends\">\n";
	echo $cstr;  
  
  echo "<h1>Einzahlungen / Statistik</h1>";
  
  
  //
  // Filter
  //
  
  tableStart("Filter");
  
  // Ausgabe
  echo "<tr>
  				<th>Ausgabe:</th>
  				<td>
  					<input type=\"radio\" name=\"output\" id=\"output\" value=\"0\" checked=\"checked\"/> Einzeln / <input type=\"radio\" name=\"output\" id=\"output\" value=\"1\"/> Summiert
  				</td>
  			</tr>";
  
  // Limit
	echo "<tr>
  				<th>Einzahlungen:</th>
  				<td> 
		  			<select id=\"limit\" name=\"limit\">
							<option value=\"0\" checked=\"checked\">alle</option>
							<option value=\"1\">die letzte</option>
							<option value=\"5\">die letzten 5</option>
							<option value=\"20\">die letzten 20</option>
						</select>
					</td>
				</tr>";
	
	// Von User
	echo "<tr>
  				<th>Von User:</th>
  				<td>
  					<select id=\"user_spends\" name=\"user_spends\">
							<option value=\"0\">alle</option>";
					  	// Allianzuser
							foreach($cu->alliance->members as $id => $data)
							{
					  		echo "<option value=\"".$id."\">".$data."</option>";
					  	}
  		echo "</select>
  				</td>
  			</tr>";
  echo "<tr>
  				<td style=\"text-align:center;\" colspan=\"2\">
  					<input type=\"submit\" class=\"button\" name=\"filter_submit\" id=\"filter_submit\" value=\"Anzeigen\"\"/>
  				</td>
  			</tr>";
  tableEnd();
  echo "</form>";
  
  
  //
  // Ausgabe
  //
  
  // Einzahlungen werden summiert und ausgegeben
  if($sum)
  {
  	if($user>0)
	  {
	  	$user_sql = "AND alliance_spend_user_id='".$user."'";
	  	$user_message = "von ".$cu->alliance->members[$user]." ";
	  }
	  else
	  {
	  	$user_sql = "";
	  	$user_message = "";
	  }
  	
  	echo "Es werden die bisher eingezahlten Rohstoffe ".$user_message." angezeigt.<br><br>";
		
		// Läd Einzahlungen
		$res = dbquery("
		SELECT
			SUM(alliance_spend_metal) AS metal,
			SUM(alliance_spend_crystal) AS crystal,
			SUM(alliance_spend_plastic) AS plastic,
			SUM(alliance_spend_fuel) AS fuel,
			SUM(alliance_spend_food) AS food
		FROM
			alliance_spends
		WHERE
			alliance_spend_alliance_id='".$cu->allianceId."'
			".$user_sql.";");		
		if(mysql_num_rows($res)>0)
		{						
			$arr=mysql_fetch_assoc($res);
			
			tableStart("Total eingezahlte Rohstoffe ".$user_message."");
			echo "<tr>
							<th style=\"width:20%\">".RES_METAL."</th>
							<th style=\"width:20%\">".RES_CRYSTAL."</th>
							<th style=\"width:20%\">".RES_PLASTIC."</th>
							<th style=\"width:20%\">".RES_FUEL."</th>
							<th style=\"width:20%\">".RES_FOOD."</th>
						</tr>";
			echo "<tr>
							<td>".nf($arr['metal'])."</td>
							<td>".nf($arr['crystal'])."</td>
							<td>".nf($arr['plastic'])."</td>
							<td>".nf($arr['fuel'])."</td>
							<td>".nf($arr['food'])."</td>
						</tr>";
			tableEnd();
		}
		else
		{
			iBoxStart("Einzahlungen");
			echo "Es wurden noch keine Rohstoffe eingezahlt!";
			iBoxEnd();
		}
	}
	// Einzahlungen werden einzelen ausgegeben
	else
	{

  	if($user>0)
	  {
	  	$user_sql = "AND alliance_spend_user_id='".$user."'";
	  	$user_message = "von ".$cu->alliance->members[$user]." ";
	  }
	  else
	  {
	  	$user_sql = "";
	  	$user_message = "";
	  }
  	
  	
	  if($limit>0)
	  { 	
	  	if($limit==1)
	  	{
	  		echo "Es wird die letzte Einzahlung ".$user_message."gezeigt.<br><br>";
	  	}
	  	else
	  	{
	  		echo "Es werden die letzten ".$limit." Einzahlungen ".$user_message."gezeigt.<br><br>";
	  	}
	  	
	  	$limit_sql = "LIMIT ".$limit."";
	  }
	  else
	  {
	  	echo "Es werden alle bisherigen Einzahlungen ".$user_message."gezeigt.<br><br>";
	  	$limit_sql = "";
	  }
	  
		
		// Läd Einzahlungen
		$res = dbquery("
		SELECT
			*
		FROM
			alliance_spends
		WHERE
			alliance_spend_alliance_id='".$cu->allianceId."'
			".$user_sql."
		ORDER BY
			alliance_spend_time DESC
		".$limit_sql.";");		
		if(mysql_num_rows($res)>0)
		{						
			while($arr=mysql_fetch_assoc($res))
			{
				tableStart("".$cu->alliance->members[$arr['alliance_spend_user_id']]." - ".df($arr['alliance_spend_time'])."");
				echo "<tr>
								<th style=\"width:20%\">".RES_METAL."</th>
								<th style=\"width:20%\">".RES_CRYSTAL."</th>
								<th style=\"width:20%\">".RES_PLASTIC."</th>
								<th style=\"width:20%\">".RES_FUEL."</th>
								<th style=\"width:20%\">".RES_FOOD."</th>
							</tr>";
				echo "<tr>
								<td>".nf($arr['alliance_spend_metal'])."</td>
								<td>".nf($arr['alliance_spend_crystal'])."</td>
								<td>".nf($arr['alliance_spend_plastic'])."</td>
								<td>".nf($arr['alliance_spend_fuel'])."</td>
								<td>".nf($arr['alliance_spend_food'])."</td>
							</tr>";
				tableEnd();
			}
			
		}
		else
		{
			iBoxStart("Einzahlungen");
			echo "Es wurden noch keine Rohstoffe eingezahlt!";
			iBoxEnd();
		}
	}

	echo "</div>";
	
	
	
	//
	// Schiffswerft
	//
	
	if($action2=="shipyard")
	{
		$display = "";
	}
	else
	{
		$display = "none";
	}
	echo "<div id=\"tabShipyard\" style=\"display:".$display.";\">";

	if($shipyard)
	{
		echo "<h1>Schiffswerft</h1>";
		
 		echo "<form action=\"?page=".$page."&amp;action=".$_GET['action']."&amp;action2=shipyard\" method=\"post\" id=\"alliance_shipyard\">\n";
		echo $cstr;
		
		tableStart("Guthaben Übersicht");
		
		echo "<tr>";
		if ($cu->alliance->resMetal<0 || $cu->alliance->resCrystal<0 || $cu->alliance->resPlastic<0 || $cu->alliance->resFuel<0 || $cu->alliance->resFood<0)
		{
			echo "<td style=\"text-align:center;\"><span ".tm("Produktionsstop","Die Produktion wurde unterbrochen, da negative Rohstoffe vorhanden sind.").">Schiffsteile pro Stunde: 0</span></td>";
		}
		else
		{
			// if changed, also change classes/alliance.class.php
			echo "<td style=\"text-align:center;\">Schiffsteile pro Stunde: ".ceil($cfg->get('alliance_shippoints_per_hour')*pow($cfg->get('alliance_shippoints_base'),($cu->alliance->buildlist->getLevel(ALLIANCE_SHIPYARD_ID)-1)))."</td>";
		}
		echo "</tr>
		<tr>
			<td style=\"text-align:center;\">Vorhandene Teile: ".($cu->allianceShippoints-$ship_costed)."</td>
		</tr>";
		
		tableEnd();
		
		
		// Listet Schiffe auf
		if(isset($ships))
		{
			foreach($ships as $id => $data)
			{
				// Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
		    	$ship_count = 0;
		      	// ... auf den Planeten
		      	if(isset($shiplist[$data['ship_id']]))
		      	{
		      		$ship_count += array_sum($shiplist[$data['ship_id']]);
		      	}
		      	// ... in der Bauliste
		      	if(isset($queue_total[$data['ship_id']]))
		      	{
		      		$ship_count += $queue_total[$data['ship_id']];
		      	}
				// ... in der Luft
		      	if(isset($fleet[$data['ship_id']]))
		      	{
		      		$ship_count += $fleet[$data['ship_id']];
		      	}
				
				
				//Kostenfaktor Schiffe
				$cost_factor = pow($cfg->get("alliance_shipcosts_factor"),$ship_count);
				
				$path = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$data['ship_id']."_middle.".IMAGE_EXT;
				tableStart($data['ship_name']);
				echo "<tr>
	                <td style=\"width:120px;background:#000;vertical-align:middle;padding:0px;\">
                  	<img src=\"".$path."\" style=\"width:120px;height:120px;border:none;margin:0px;\" alt=\"".$data['ship_name']."\"/>
	                	<input type=\"hidden\" value=\"".$data['ship_name']."\" id=\"ship_name_".$data['ship_id']."\" name=\"ship_name_".$data['ship_id']."\" />
	                </td>
	                <td style=\"vertical-align:top;height:100px;\" colspan=\"7\">
	                	".$data['ship_longcomment']."
	               	</td>
				     </tr>
					 <tr>
								<th style=\"width:13%\">Waffen</th>
								<th style=\"width:13%\">Struktur</th>
								<th style=\"width:13%\">Schild</th>
								<th style=\"width:13%\">Speed</th>
								<th style=\"width:13%\">Startzeit</th>
								<th style=\"width:13%\">Landezeit</th>
								<th style=\"width:12%\">Kosten</th>
								<th style=\"width:10%\">Anzahl</th>
							</tr>
							<tr>
								<td>".nf($data['ship_weapon'])."</td>
								<td>".nf($data['ship_structure'])."</td>
								<td>".nf($data['ship_shield'])."</td>
								<td>".nf($data['ship_speed'])." AE/h</td>
								<td>".tf($data['ship_time2start']/FLEET_FACTOR_S)."</td>
								<td>".tf($data['ship_time2land']/FLEET_FACTOR_S)."</td>";
								if ($data['ship_max_count']!=0 && $data['ship_max_count']<=$ship_count) {
									echo "<td colspan=\"2\"><i>Maximalanzahl erreicht</i></td>";
								} else {
									echo "<td>".nf($data['ship_alliance_costs']*$cost_factor)." <input type=\"hidden\" value=\"".$data['ship_alliance_costs']*$cost_factor."\" id=\"ship_costs_".$data['ship_id']."\" name=\"ship_costs_".$data['ship_id']."\" /></td>
								<td>
									<input type=\"text\" value=\"0\" name=\"buy_ship[".$data['ship_id']."]\" id=\"buy_ship_".$data['ship_id']."\" size=\"4\" maxlength=\"6\" onkeyup=\"FormatNumber(this.id,this.value, '', '', '');\"/>";
								}
									echo "<input type=\"hidden\" value=\"".$data['ship_max_count']."\" id=\"ship_max_count_".$data['ship_id']."\" name=\"ship_max_count_".$data['ship_id']."\" />
									</td>
							</tr>";
				
				
				tableEnd();
			}
		}
		else
		{
			iBoxStart("Schiffe");
			echo "Es sind keine Allianzschiffe vorhanden!";
			iBoxEnd();
		}
		
		
		
		tableStart("Fertigung");
		
		echo "<tr>
						<td style=\"text-align:center;\">
							<select id=\"user_buy_ship\" name=\"user_buy_ship\">
					  			<option value=\"".$cu->id."\">".$cu." (".nf($cu->allianceShippoints-$ship_costed).")</option>
							</select><br/><br/>
  						<input type=\"submit\" class=\"button\" name=\"ship_submit\" id=\"ship_submit\" value=\"Schiffe herstellen\" ".tm("Schiffe herstellen","Stellt aus den vorhandenen Teilen die gewünschten Schiffe für den ausgewählten User her.").">
						</td>
					</tr>";
		
		tableEnd();
  		
		echo "</form>";
	}
	
	echo "</div>";

?>