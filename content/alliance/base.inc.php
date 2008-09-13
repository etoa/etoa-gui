<?PHP

	echo "<h2><a href=\"?page=".$page."&amp;action=".$_GET['action']."\">Allianzbasis</a></h2>";
		
	//
	// Funktionen				
	//
	
	// Wechselt zwischen den Verschiedenen Tabs
	echo "<script type=\"text/javascript\">
	function showTab(idx)
	{
		document.getElementById('submitMessage').style.display='none';
		document.getElementById('tabBuildings').style.display='none';
		document.getElementById('tabResearch').style.display='none';
		document.getElementById('tabStorage').style.display='none';
		document.getElementById('tabSpends').style.display='none';
		
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
		
		// Wenn zu wenig Rohstoffe auf dem aktuellen Planeten sind, wir eine Nachricht ausgegeben
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
		document.getElementById('resBoxCrystal').innerHTML=FormatNumber('return',crystal,'','','');;
		document.getElementById('resBoxPlastic').innerHTML=FormatNumber('return',plastic,'','','');;
		document.getElementById('resBoxFuel').innerHTML=FormatNumber('return',fuel,'','','');;
		document.getElementById('resBoxFood').innerHTML=FormatNumber('return',food,'','','');;
	}
	</script>";	
		
	// Stellt die ganzen Bauoptionen dar, und errechnet und formatiert die Preise und gibt die Infos in einem Array zurück
	function show_buildoptions($typ="building", $id=0, $res_metal=0, $res_crystal=0, $res_plastic=0, $res_fuel=0, $res_food=0, $costs_metal=0, $costs_crystal=0, $costs_plastic=0, $costs_fuel=0, $costs_food=0, $build_time=0, $costs_factor=0, $level=0, $max_level=0, $buildsomething=false, $members=1, $end_time=0)
	{		
		global $conf;
		
		// Prüft Max. Level
		if($level<$max_level)
		{
			// Bauzeit (Fixe Zeit * Level)
			$btime = $build_time * ($level+1);

			// Errechnet die Effektiven Kosten für die Allianz in Abgängigkeit von der Mitgliederanzahl
			$factor = pow($costs_factor,$level);
			$member_factor = pow($members,$conf['alliance_membercosts_factor']['v']);
			if($factor<1)
			{
				$factor = 1;
			}
			$costs_metal = ceil($costs_metal * $factor * $member_factor);
			$costs_crystal = ceil($costs_crystal * $factor * $member_factor);
			$costs_plastic = ceil($costs_plastic * $factor * $member_factor);
			$costs_fuel = ceil($costs_fuel * $factor * $member_factor);
			$costs_food = ceil($costs_food * $factor * $member_factor);
	
			// Rechnet fehlende Rohstoffe (Vorhandene Rohstoffe - Kosten -> Wenn Resultat positiv ist, sind genug Rohstoffe vorhanden)
			$need_metal = $res_metal - $costs_metal;
			$need_crystal = $res_crystal - $costs_crystal;
			$need_plastic = $res_plastic - $costs_plastic;
			$need_fuel = $res_fuel - $costs_fuel;
			$need_food = $res_food - $costs_food;
			
			// Prüft ob Rohstoffe gebraucht werden (Negative Zahlen = Ja). 
			$need_something = false;
			// Titan
			if($need_metal>=0)
			{
				$need_metal = 0;
				$style_metal = "class=\"tbldata\"";
			}
			else
			{
				$need_something = true;
				
				// Erstellt absolut Wert der Zahl
				$need_metal = abs($need_metal);
				$style_metal = "class=\"tbldata2\" ".tm("Fehlender Rohstoff","".nf($need_metal)." ".RES_METAL."")."";
			}
			
			// Silizium
			if($need_crystal>=0)
			{
				$need_crystal = 0;
				$style_crystal = "class=\"tbldata\"";
			}
			else
			{
				$need_something = true;
				
				// Erstellt absolut Wert der Zahl
				$need_crystal = abs($need_crystal);
				$style_crystal = "class=\"tbldata2\" ".tm("Fehlender Rohstoff","".nf($need_crystal)." ".RES_CRYSTAL."")."";
			}
			
			// PVC
			if($need_plastic>=0)
			{
				$need_plastic = 0;
				$style_plastic = "class=\"tbldata\"";
			}
			else
			{
				$need_something = true;
				
				// Erstellt absolut Wert der Zahl
				$need_plastic = abs($need_plastic);
				$style_plastic = "class=\"tbldata2\" ".tm("Fehlender Rohstoff","".nf($need_plastic)." ".RES_PLASTIC."")."";
			}
			
			// Tritium
			if($need_fuel>=0)
			{
				$need_fuel = 0;
				$style_fuel = "class=\"tbldata\"";
			}
			else
			{
				$need_something = true;
				
				// Erstellt absolut Wert der Zahl
				$need_fuel = abs($need_fuel);
				$style_fuel = "class=\"tbldata2\" ".tm("Fehlender Rohstoff","".nf($need_fuel)." ".RES_FUEL."")."";
			}
			
			// Nahrung
			if($need_food>=0)
			{
				$need_food = 0;
				$style_food = "class=\"tbldata\"";
			}
			else
			{
				$need_something = true;
				
				// Erstellt absolut Wert der Zahl
				$need_food = abs($need_food);
				$style_food = "class=\"tbldata2\" ".tm("Fehlender Rohstoff","".nf($need_food)." ".RES_FOOD."")."";
			}
			
			
			// Gibt Nachrichten aus. Unterschiedliche Ausgabe von Gebäuden und Technologien
			$style_message = "class=\"tbldata\"";
			
			if($typ=="building")
			{
				if($level==0)
				{
					$build_button = "Bauen";
				}
				else
				{
					$build_button = "Ausbauen";
				}
				$status_message = startTime($end_time-time(), 'build_message_'.$typ.'_'.$id.'', 0, 'Wird ausgebaut auf Stufe '.($level+1).' (TIME)');
				$status_message2 = "Es wird bereits gebaut!";
			}
			elseif($typ=="research")
			{
				$build_button = "Erforschen";
				$status_message = startTime($end_time-time(), 'build_message_'.$typ.'_'.$id.'', 0, 'Stufe '.($level+1).' wird erforscht (TIME)');
				$status_message2 = "Es wird bereits geforscht!";
			}
			else
			{
				$build_button = "";
			}
			
	
			// Es wird schon gebaut
			if($buildsomething)
			{
				// Wenn dieses Objekt im Bau ist
				if($end_time>0)
				{
					$style_message = "class=\"tbldata3\"";
					$build_message = $status_message;
				}
				// Ein anderes Objekt ist in Bau
				else
				{
					$style_message = "class=\"tbldata2\"";
					$build_message = $status_message2;
				}
			}
			// Nicht genügend Rohstoffe
			elseif($need_something)
			{		
				$build_message = "<input type=\"button\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Fehlende Roshtoffe einzahlen\" ".tm("Nicht genügend Rohstoffe","Es sind nicht genügend Rohstoffe vorhanden!<br>Klick auf den Button um die fehlenden Rohstoffe einzuzahlen.")." onclick=\"setSpends(".$need_metal.", ".$need_crystal.", ".$need_plastic.", ".$need_fuel.", ".$need_food.");\"/>";
			}
			// Gebäude kann ausgebaut werden
			else
			{
				// Generiert Baubutton, mit welchem vor dem Absenden noch die Objekt ID übergeben wird
				$build_message = "<input type=\"submit\" class=\"button\" name=\"".$typ."_submit\" id=\"".$typ."_submit\" value=\"".$build_button."\" onclick=\"document.getElementById('".$typ."_id').value=".$id.";\"/>";
			}
			
			$out = "";
			$out .= "<tr>
							<td class=\"tbltitle\" width=\"7%\">Stufe</th>
			        <td class=\"tbltitle\" width=\"18%\">Zeit</th>
			        <td class=\"tbltitle\" width=\"15%\">".RES_METAL."</td>
			        <td class=\"tbltitle\" width=\"15%\">".RES_CRYSTAL."</td>
			        <td class=\"tbltitle\" width=\"15%\">".RES_PLASTIC."</td>
			        <td class=\"tbltitle\" width=\"15%\">".RES_FUEL."</td>
			        <td class=\"tbltitle\" width=\"15%\">".RES_FOOD."</td>
						</tr>
						<tr>
							<td class=\"tbldata\" width=\"7%\">".($level+1)."</th>
							<td class=\"tbldata\" width=\"18%\">".tf($btime)."</th>
			        <td ".$style_metal." width=\"15%\">".nf($costs_metal)."</td>
			        <td ".$style_crystal." width=\"15%\">".nf($costs_crystal)."</td>
			        <td ".$style_plastic." width=\"15%\">".nf($costs_plastic)."</td>
			        <td ".$style_fuel." width=\"15%\">".nf($costs_fuel)."</td>
			        <td ".$style_food." width=\"15%\">".nf($costs_food)."</td>
						</tr>
						<tr>
							<td ".$style_message." colspan=\"7\" style=\"text-align:center;\" name=\"build_message_".$typ."_".$id."\" id=\"build_message_".$typ."_".$id."\">".$build_message."</td>
						</tr>";
			// Packt alle infos in ein Array und gibt dieses zurück
			// Kosten
			$return['costs_metal'] = $costs_metal;
			$return['costs_crystal'] = $costs_crystal;
			$return['costs_plastic'] = $costs_plastic;
			$return['costs_fuel'] = $costs_fuel;
			$return['costs_food'] = $costs_food;
			// Bauzeit
			$return['btime'] = $btime;
			// Gibt Optionsbox aus
			$return['optionsbox'] = $out;
			
			return $return;
		}
		// Maximallevel erreicht, es werden keine Berechnungen mehr durchgeführt
		else
		{
			$return['optionsbox'] = "<tr><td class=\"tbldata\" style=\"text-align:center;\">Maximallevel erreicht!</td></tr>";
			return $return;
		}
	}			
		
		
		
 	//
 	// Datenverarbeitung
 	//
 	
 	// Speichert Nachricht, welche später ausgegeben wird
 	$submit_message = "";
 	
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
	  	$res=dbquery("
	    SELECT
	      planet_res_metal,
	      planet_res_crystal,
	      planet_res_plastic,
	      planet_res_fuel,
	      planet_res_food
	    FROM
	      planets
	    WHERE
	      id='".$cp->id()."'");
	    $arr = mysql_fetch_assoc($res);
	    
	    if($arr['planet_res_metal'] >= $metal
	    	&& $arr['planet_res_crystal'] >= $crystal
	    	&& $arr['planet_res_plastic'] >= $plastic
	    	&& $arr['planet_res_fuel'] >= $fuel
	    	&& $arr['planet_res_food'] >= $food) 	
	    {
	    	// Rohstoffe vom Planet abziehen
	      dbquery("
	      UPDATE
	        planets
	      SET
	        planet_res_metal=planet_res_metal-'".$metal."',
	        planet_res_crystal=planet_res_crystal-'".$crystal."',
	        planet_res_plastic=planet_res_plastic-'".$plastic."',
	        planet_res_fuel=planet_res_fuel-'".$fuel."',
	        planet_res_food=planet_res_food-'".$food."'
	      WHERE
	        id='".$cp->id()."';");
	          
	      // Rohstoffe der Allianz gutschreiben
	      dbquery("
	      UPDATE
	        alliances
	      SET
	        alliance_res_metal=alliance_res_metal+'".$metal."',
	        alliance_res_crystal=alliance_res_crystal+'".$crystal."',
	        alliance_res_plastic=alliance_res_plastic+'".$plastic."',
	        alliance_res_fuel=alliance_res_fuel+'".$fuel."',
	        alliance_res_food=alliance_res_food+'".$food."'
	      WHERE
	        alliance_id='".$cu->allianceId()."';");
	        
	     	// Spende speichern
	     	dbquery("
	      INSERT INTO
	      alliance_spends
	          (alliance_spend_alliance_id,
	          alliance_spend_user_id,
	          alliance_spend_metal,
	          alliance_spend_crystal,
	          alliance_spend_plastic,
	          alliance_spend_fuel,
	          alliance_spend_food,
	          alliance_spend_time)
	      VALUES
	          ('".$cu->allianceId()."',
	          '".$cu->id()."',
	          '".$metal."',
	          '".$crystal."',
	          '".$plastic."',
	          '".$fuel."',
	          '".$food."',
	          '".time()."')");

	      	$submit_message .= "Rohstoffe erfolgreich eingezahlt!<br><br>";    
	    }
	    else
	    {
	    	$submit_message .= "Es sind zu wenig Rohstoffe auf dem Planeten!<br><br>";
	    }
	  }
	  else
	  {
	  	$submit_message .= "Du hast keine Rohstoffe angegeben!<br><br>";
	  }	  
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
	
	// Allianzdaten
	$res = dbquery("
	SELECT
		alliance_id,
		alliance_founder_id,
		alliance_architect_id,
		alliance_technican_id,
		alliance_res_metal,
		alliance_res_crystal,
		alliance_res_plastic,
		alliance_res_fuel,
		alliance_res_food
	FROM
		alliances
	WHERE
		alliance_id='".$cu->allianceId()."';");		
	$aarr = mysql_fetch_assoc($res);
	
	// Läd die Allianzmitglieder
	$res = dbquery("
	SELECT
		user_id,
		user_nick
	FROM
		users
	WHERE
		user_alliance_id='".$cu->allianceId()."';");
	$alliance_member_cnt = mysql_num_rows($res);
	while($arr=mysql_fetch_assoc($res))		
	{
		$alliance_members[$arr['user_id']] = $arr['user_nick'];
	}

	// Allianzgebäude
	$res = dbquery("
	SELECT
		*
	FROM
		alliance_buildings
	WHERE
		alliance_building_show='1';");
	while($arr=mysql_fetch_assoc($res))		
	{
		$buildings[$arr['alliance_building_id']] = $arr;
	}
	
	// Gebaute Gebäude laden und markieren, falls etwas im Bau ist
	$buildsomething = false;
	$res = dbquery("
	SELECT
		*
	FROM
		alliance_buildlist
	WHERE
		alliance_buildlist_alliance_id='".$cu->allianceId()."';");
	while($arr=mysql_fetch_assoc($res))		
	{
		$buildlist[$arr['alliance_buildlist_building_id']] = $arr;
				
		if($arr['alliance_buildlist_build_end_time']>0)
		{
			$buildsomething = true;
		}
	}	
	
	// Allianzforschungen
	$res = dbquery("
	SELECT
		*
	FROM
		alliance_technologies
	WHERE
		alliance_tech_show='1';");
	while($arr=mysql_fetch_assoc($res))		
	{
		$techs[$arr['alliance_tech_id']] = $arr;
	}
	
	// Erforschte Techs laden und markieren, falls etwas im Bau ist
	$researchsomething = false;
	$res = dbquery("
	SELECT
		*
	FROM
		alliance_techlist
	WHERE
		alliance_techlist_alliance_id='".$cu->allianceId()."';");
	while($arr=mysql_fetch_assoc($res))		
	{
		$techlist[$arr['alliance_techlist_tech_id']] = $arr;
				
		if($arr['alliance_techlist_build_end_time']>0)
		{
			$researchsomething = true;
		}
	}	
	
	
	
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
	
	echo "<a href=\"javascript:;\" onclick=\"showTab('tabBuildings')\">Gebäude</a> | <a href=\"javascript:;\" onclick=\"showTab('tabResearch')\">Technologien</a> | <a href=\"javascript:;\" onclick=\"showTab('tabStorage')\">Speicher</a> | <a href=\"javascript:;\" onclick=\"showTab('tabSpends')\">Einzahlungen</a><br><br><br>";
	
	
	
	//
	// ResBox
	//	
	
	$style0 = "class=\"tbldata\"";
	$style1 = "class=\"tbldata\"";
	$style2 = "class=\"tbldata\"";
	$style3 = "class=\"tbldata\"";
	$style4 = "class=\"tbldata\"";

	// Negative Rohstoffe farblich hervorben
	if ($aarr['alliance_res_metal'] < 0)
	{
		$style0 = "class=\"tbldata2\"";
	}
	if ($aarr['alliance_res_crystal'] < 0)
	{
		$style1 = "class=\"tbldata2\"";
	}
	if ($aarr['alliance_res_plastic'] < 0)
	{
		$style2 = "class=\"tbldata2\"";
	}
	if ($aarr['alliance_res_fuel'] < 0)
	{
		$style3 = "class=\"tbldata2\"";
	}
	if ($aarr['alliance_res_food'] < 0)
	{
		$style4 = "class=\"tbldata2\"";
	}
	
	
	infobox_start("Rohstoffe",1);
	echo "<tr>
					<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_METAL." ".RES_METAL."</td>
					<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_CRYSTAL." ".RES_CRYSTAL."</td>
					<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_PLASTIC." ".RES_PLASTIC."</td>
					<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_FUEL." ".RES_FUEL."</td>
					<td class=\"tbltitle\" style=\"vertical-align:middle;\">".RES_ICON_FOOD." ".RES_FOOD."</td>
				</tr>
				<tr>
					<td ".$style0." id=\"resBoxMetal\">".nf($aarr['alliance_res_metal'])." t</td>
					<td ".$style1." id=\"resBoxCrystal\">".nf($aarr['alliance_res_crystal'])." t</td>
					<td ".$style2."id=\"resBoxPlastic\">".nf($aarr['alliance_res_plastic'])." t</td>
					<td ".$style3."id=\"resBoxFuel\">".nf($aarr['alliance_res_fuel'])." t</td>
					<td ".$style4."id=\"resBoxFood\">".nf($aarr['alliance_res_food'])." t</td>
				</tr>";
 	infobox_end(1);
 	
 	
 	
 	

 	
 	
 	//
 	// Content Laden
 	//
	
	//
	// Submit Nachricht
	//
	
	// Zeigt Nachricht an wenn vorhanden
	if($submit_message!="")
	{
		$display = "";
	}
	else
	{
		$display = "none";
	}
	echo "<div id=\"submitMessage\"\" style=\"display:".$display.";\">";
	echo $submit_message;
	echo "</div>";
	
	
	
	//
	// Gebäude
	//
	
	// Gebäude in Auftrag geben
	if(isset($_POST['building_submit']) && checker_verify())
	{
		if(isset($_POST['building_id']) && $_POST['building_id']!=0)
		{		
			// Überprüft ob schon ein Gebäude in Bau ist
			if(!$buildsomething)
			{
				$id = intval($_POST['building_id']);
				if(isset($buildlist[$id]))
				{
					$b_level = $buildlist[$id]['alliance_buildlist_current_level'];
				}
				else
				{
					$b_level = 0;
				}
				
				// Berechnet Kosten
				$options_arr = show_buildoptions('', '',$aarr['alliance_res_metal'], $aarr['alliance_res_crystal'], $aarr['alliance_res_plastic'], $aarr['alliance_res_fuel'], $aarr['alliance_res_food'], $buildings[$id]['alliance_building_costs_metal'], $buildings[$id]['alliance_building_costs_crystal'], $buildings[$id]['alliance_building_costs_plastic'], $buildings[$id]['alliance_building_costs_fuel'], $buildings[$id]['alliance_building_costs_food'], $buildings[$id]['alliance_building_build_time'], $buildings[$id]['alliance_building_costs_factor'], $b_level, $buildings[$id]['alliance_building_last_level'], $buildsomething, $alliance_member_cnt);
				
				// Prüft Allianzrohstoffe
				if($aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal'])
				{					
					// Zieht Rohstoffe vom Allianzkonto ab
					dbquery("
		      UPDATE
		        alliances
		      SET
		        alliance_res_metal=alliance_res_metal-'".$options_arr['costs_metal']."',
		        alliance_res_crystal=alliance_res_crystal-'".$options_arr['costs_crystal']."',
		        alliance_res_plastic=alliance_res_plastic-'".$options_arr['costs_plastic']."',
		        alliance_res_fuel=alliance_res_fuel-'".$options_arr['costs_fuel']."',
		        alliance_res_food=alliance_res_food-'".$options_arr['costs_food']."'
		      WHERE
		        alliance_id='".$cu->allianceId()."';");
		        
		      // Setzt Bauzeit für das Gebäude
		      $end_time = $options_arr['btime'] + time();
		      // Prüft ob Datensatz schon vorhanden ist
		      if(isset($buildlist[$id]))
		      {
		      	// Update
		      	dbquery("
			      UPDATE
			        alliance_buildlist
			      SET
			        alliance_buildlist_build_start_time='".time()."',
			        alliance_buildlist_build_end_time='".$end_time."'
			      WHERE
			        alliance_buildlist_alliance_id='".$cu->allianceId()."'
			        AND alliance_buildlist_building_id='".$id."';");
		      }
		      else
		      {
    				dbquery("
						INSERT INTO 
						alliance_buildlist 
						(
							alliance_buildlist_alliance_id,
							alliance_buildlist_building_id,
							alliance_buildlist_build_start_time,
							alliance_buildlist_build_end_time
						) 
						VALUES 
						( 
							'".$cu->allianceId()."',
							'".$id."',
							'".time()."',
							'".$end_time."'
						);");
		      }
		      
		      // Baulisten Array aktualisieren
	      	$buildlist[$id]['alliance_buildlist_build_start_time'] = time();
	      	$buildlist[$id]['alliance_buildlist_build_end_time'] = $end_time;
	      	if(!isset($buildlist[$id]['alliance_buildlist_current_level']))
	      	{
	      		$buildlist[$id]['alliance_buildlist_current_level'] = 0;
	      	}
	      	$buildsomething=true;

					// Aktualister ResBox
					$aarr['alliance_res_metal'] = $aarr['alliance_res_metal'] - $options_arr['costs_metal'];
					$aarr['alliance_res_crystal'] = $aarr['alliance_res_crystal'] - $options_arr['costs_crystal'];
					$aarr['alliance_res_plastic'] = $aarr['alliance_res_plastic'] - $options_arr['costs_plastic'];
					$aarr['alliance_res_fuel'] = $aarr['alliance_res_fuel'] - $options_arr['costs_fuel'];
					$aarr['alliance_res_food'] = $aarr['alliance_res_food'] - $options_arr['costs_food'];
					
					echo "<script type=\"text/javascript\">changeResBox(".$aarr['alliance_res_metal'].", ".$aarr['alliance_res_crystal'].", ".$aarr['alliance_res_plastic'].", ".$aarr['alliance_res_fuel'].", ".$aarr['alliance_res_food'].");</script>";
		      
		      // Ausbau zur Allianzgeschichte hinzufügen
		      add_alliance_history($cu->allianceId(),"[b]".$cu->nick()."[/b] hat das Gebäude \"".$buildings[$id]['alliance_building_name']." (".($buildlist[$id]['alliance_buildlist_current_level']+1).")\" in Auftrag gegeben.");
		      
		      echo "Gebäude wurde erfolgreich in Auftrag gegeben!<br><br>";
				}
				else
				{
					echo "Es sind zuwenig Rohstoffe vorhanden!<br><br>";
				}
			}
			else
			{
				echo "Es ist bereits ein Gebäude in bau!<br><br>";
			}
		}
		else
		{
			echo "Es konnte keine Objekt-ID ermittelt werden!<br><br>";
		}
	}
	
	
		// Technologie in Auftrag geben
	if(isset($_POST['research_submit']) && checker_verify())
	{
		if(isset($_POST['research_id']) && $_POST['research_id']!=0)
		{			
			// Überprüft ob schon ein Gebäude in Bau ist
			if(!$researchsomething)
			{
				$id = intval($_POST['research_id']);
				if(isset($techlist[$id]))
				{
					$b_level = $techlist[$id]['alliance_techlist_current_level'];
				}
				else
				{
					$b_level = 0;
				}
				
				// Berechnet Kosten
				$options_arr = show_buildoptions('', '',$aarr['alliance_res_metal'], $aarr['alliance_res_crystal'], $aarr['alliance_res_plastic'], $aarr['alliance_res_fuel'], $aarr['alliance_res_food'], $techs[$id]['alliance_tech_costs_metal'], $techs[$id]['alliance_tech_costs_crystal'], $techs[$id]['alliance_tech_costs_plastic'], $techs[$id]['alliance_tech_costs_fuel'], $techs[$id]['alliance_tech_costs_food'], $techs[$id]['alliance_tech_build_time'], $techs[$id]['alliance_tech_costs_factor'], $b_level, $techs[$id]['alliance_tech_last_level'], $researchsomething, $alliance_member_cnt);
				
				// Prüft Allianzrohstoffe
				if($aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal']
					&& $aarr['alliance_res_metal']>=$options_arr['costs_metal'])
				{					
					// Zieht Rohstoffe vom Allianzkonto ab
					dbquery("
		      UPDATE
		        alliances
		      SET
		        alliance_res_metal=alliance_res_metal-'".$options_arr['costs_metal']."',
		        alliance_res_crystal=alliance_res_crystal-'".$options_arr['costs_crystal']."',
		        alliance_res_plastic=alliance_res_plastic-'".$options_arr['costs_plastic']."',
		        alliance_res_fuel=alliance_res_fuel-'".$options_arr['costs_fuel']."',
		        alliance_res_food=alliance_res_food-'".$options_arr['costs_food']."'
		      WHERE
		        alliance_id='".$cu->allianceId()."';");
		        
		      // Setzt Bauzeit für das Gebäude
		      $end_time = $options_arr['btime'] + time();
		      // Prüft ob Datensatz schon vorhanden ist
		      if(isset($techlist[$id]))
		      {
		      	// Update
		      	dbquery("
			      UPDATE
			        alliance_techlist
			      SET
			        alliance_techlist_build_start_time='".time()."',
			        alliance_techlist_build_end_time='".$end_time."'
			      WHERE
			        alliance_techlist_alliance_id='".$cu->allianceId()."'
			        AND alliance_techlist_tech_id='".$id."';");
		      }
		      else
		      {
    				dbquery("
						INSERT INTO 
						alliance_techlist 
						(
							alliance_techlist_alliance_id,
							alliance_techlist_tech_id,
							alliance_techlist_build_start_time,
							alliance_techlist_build_end_time
						) 
						VALUES 
						( 
							'".$cu->allianceId()."',
							'".$id."',
							'".time()."',
							'".$end_time."'
						);");
		      }
		      
		      // Baulisten Array aktualisieren
	      	$techlist[$id]['alliance_techlist_build_start_time'] = time();
	      	$techlist[$id]['alliance_techlist_build_end_time'] = $end_time;
	      	if(!isset($techlist[$id]['alliance_techlist_current_level']))
	      	{
	      		$techlist[$id]['alliance_techlist_current_level'] = 0;
	      	}
	      	$researchsomething=true;

					// Aktualister ResBox
					$aarr['alliance_res_metal'] = $aarr['alliance_res_metal'] - $options_arr['costs_metal'];
					$aarr['alliance_res_crystal'] = $aarr['alliance_res_crystal'] - $options_arr['costs_crystal'];
					$aarr['alliance_res_plastic'] = $aarr['alliance_res_plastic'] - $options_arr['costs_plastic'];
					$aarr['alliance_res_fuel'] = $aarr['alliance_res_fuel'] - $options_arr['costs_fuel'];
					$aarr['alliance_res_food'] = $aarr['alliance_res_food'] - $options_arr['costs_food'];
					
					echo "<script type=\"text/javascript\">changeResBox(".$aarr['alliance_res_metal'].", ".$aarr['alliance_res_crystal'].", ".$aarr['alliance_res_plastic'].", ".$aarr['alliance_res_fuel'].", ".$aarr['alliance_res_food'].");</script>";
		      
		      // Ausbau zur Allianzgeschichte hinzufügen
		      add_alliance_history($cu->allianceId(),"[b]".$cu->nick()."[/b] hat die Forschung \"".$techs[$id]['alliance_tech_name']." (".($techlist[$id]['alliance_techlist_current_level']+1).")\" in Auftrag gegeben.");
		      
		      echo "Forschung wurde erfolgreich in Auftrag gegeben!<br><br>";
				}
				else
				{
					echo "Es sind zuwenig Rohstoffe vorhanden!<br><br>";
				}
			}
			else
			{
				echo "Es wird bereits geforscht!<br><br>";
			}
		}
		else
		{
			echo "Es konnte keine Objekt-ID ermittelt werden!<br><br>";
		}
	}
	
	
	
	
	/// 123
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
	if(isset($buildings))
	{
		// Geht alle Gebäude durch
		foreach($buildings as $id => $data)
		{
			// Prüft Voraussetzungen
			if($data['alliance_building_needed_level']==0 
				|| (isset($buildlist[$data['alliance_building_needed_id']]) 
						&& $data['alliance_building_needed_level']<=$buildlist[$data['alliance_building_needed_id']]['alliance_buildlist_current_level']
						)
				)
			{
				
				//
				// Gebäudedaten anzeigen
				//
				
				// Definiert Level und Endzeit
				if(isset($buildlist[$id]['alliance_buildlist_current_level']))
				{
					$b_level = $buildlist[$id]['alliance_buildlist_current_level'];
					$end_time = $buildlist[$id]['alliance_buildlist_build_end_time'];
				}
				else
				{
					$b_level = 0;
					$end_time = 0;
				}
				
				$path = IMAGE_PATH."/".IMAGE_ALLIANCE_BUILDING_DIR."/building".$data['alliance_building_id']."_middle.".IMAGE_EXT;
				$title = $data['alliance_building_name'].' <span id="buildlevel">';
				$title.= $b_level > 0 ? $b_level : '';
				$title.= '</span>';
				infobox_start($title,1);
				echo "<tr>
                  <td class=\"tbldata\" style=\"width:120px;background:#000;vertical-align:middle;\">
                  	<img src=\"".$path."\" style=\"width:120px;height:120px;border:none;\" alt=\"".$data['alliance_building_name']."\"/>
                  </td>
                  <td class=\"tbldata\" style=\"vertical-align:top;height:100px;\">
                  	".$data['alliance_building_comment']."
                 	</td>
				     </tr>";
				infobox_end(1,1);
				
				//
				// Baumenü
				//
        
        // Generiert Bauoptionen mit allen Überprüfungen
  			$options_arr = show_buildoptions("building", $data['alliance_building_id'], $aarr['alliance_res_metal'], $aarr['alliance_res_crystal'], $aarr['alliance_res_plastic'], $aarr['alliance_res_fuel'], $aarr['alliance_res_food'], $data['alliance_building_costs_metal'], $data['alliance_building_costs_crystal'], $data['alliance_building_costs_plastic'], $data['alliance_building_costs_fuel'], $data['alliance_building_costs_food'], $data['alliance_building_build_time'], $data['alliance_building_costs_factor'], $b_level, $data['alliance_building_last_level'], $buildsomething, $alliance_member_cnt, $end_time);
  			
  			// Stellt Optionsbox dar
  			infobox_start("",1);
  			echo $options_arr['optionsbox'];
  			infobox_end(1);
			}
		}
	}
	// Es sind noch keine Gebäude vorhanden
	else
	{
		echo "Es sind noch keine Gebäude definiert!<br>";
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
	if(isset($techs))
	{
		// Geht alle Techs durch
		foreach($techs as $id => $data)
		{
			// Prüft Voraussetzungen
			if($data['alliance_tech_needed_level']==0 
				|| (isset($techlist[$data['alliance_tech_needed_id']]) 
						&& $data['alliance_tech_needed_level']<=$techlist[$data['alliance_tech_needed_id']]['alliance_techlist_current_level']
						)
				)
			{
				
				//
				// Techdaten anzeigen
				//
				
				// Definiert Level und Endzeit
				if(isset($techlist[$id]['alliance_techlist_current_level']))
				{
					$b_level = $techlist[$id]['alliance_techlist_current_level'];
					$end_time = $techlist[$id]['alliance_techlist_build_end_time'];
				}
				else
				{
					$b_level = 0;
					$end_time = 0;
				}
				
				$path = IMAGE_PATH."/".IMAGE_ALLIANCE_BUILDING_DIR."/tech".$data['alliance_tech_id']."_middle.".IMAGE_EXT;
				$title = $data['alliance_tech_name'].' <span id="buildlevel">';
				$title.= $b_level > 0 ? $b_level : '';
				$title.= '</span>';
				infobox_start($title,1);
				echo "<tr>
                  <td class=\"tbldata\" style=\"width:120px;background:#000;vertical-align:middle;\">
                  	<img src=\"".$path."\" style=\"width:120px;height:120px;border:none;\" alt=\"".$data['alliance_tech_name']."\"/>
                  </td>
                  <td class=\"tbldata\" style=\"vertical-align:top;height:100px;\">
                  	".$data['alliance_tech_comment']."
                 	</td>
				     </tr>";
				infobox_end(1,1);
				
				//
				// Baumenü
				//
        
        // Generiert Bauoptionen mit allen Überprüfungen
  			$options_arr = show_buildoptions("research", $data['alliance_tech_id'], $aarr['alliance_res_metal'], $aarr['alliance_res_crystal'], $aarr['alliance_res_plastic'], $aarr['alliance_res_fuel'], $aarr['alliance_res_food'], $data['alliance_tech_costs_metal'], $data['alliance_tech_costs_crystal'], $data['alliance_tech_costs_plastic'], $data['alliance_tech_costs_fuel'], $data['alliance_tech_costs_food'], $data['alliance_tech_build_time'], $data['alliance_tech_costs_factor'], $b_level, $data['alliance_tech_last_level'], $researchsomething, $alliance_member_cnt, $end_time);
  			
  			// Stellt Optionsbox dar
  			infobox_start("",1);
  			echo $options_arr['optionsbox'];
  			infobox_end(1);
			}
		}
	}
	// Es sind noch keine Gebäude vorhanden
	else
	{
		echo "Es sind noch keine Technologien definiert!<br>";
	}
	
	echo "</form>";
	echo "</div>";
	
	
	
	//
	// Speicher
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

	infobox_start("Rohstoffe einzahlen",1,0);	
	
	// Titan
	echo "<tr>
					<td class=\"tbltitle\" style=\"width:100px;\">".RES_METAL."</td>
					<td class=\"tbldata\" style=\"width:150px;\">
						<input type=\"text\" value=\"0\" name=\"spend_metal\" id=\"spend_metal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resMetal.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_metal').value='".nf($cp->resMetal)."';\">alles</a>
					</td>
				</tr>";
	// Silizium
	echo "<tr>
					<td class=\"tbltitle\">".RES_CRYSTAL."</td>
					<td class=\"tbldata\">
						<input type=\"text\" value=\"0\" name=\"spend_crystal\" id=\"spend_crystal\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resCrystal.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_crystal').value='".nf($cp->resCrystal)."';\">alles</a>
					</td>
				</tr>";		
	// PVC
	echo "<tr>
					<td class=\"tbltitle\">".RES_PLASTIC."</td>
					<td class=\"tbldata\">
						<input type=\"text\" value=\"0\" name=\"spend_plastic\" id=\"spend_plastic\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resPlastic.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_plastic').value='".nf($cp->resPlastic)."';\">alles</a>
					</td>
				</tr>";	
	// Tritium
	echo "<tr>
					<td class=\"tbltitle\">".RES_FUEL."</td>
					<td class=\"tbldata\">
						<input type=\"text\" value=\"0\" name=\"spend_fuel\" id=\"spend_fuel\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFuel.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_fuel').value='".nf($cp->resFuel)."';\">alles</a>
					</td>
				</tr>";	
	// Nahrung
	echo "<tr>
					<td class=\"tbltitle\">".RES_FOOD."</td>
					<td class=\"tbldata\">
						<input type=\"text\" value=\"0\" name=\"spend_food\" id=\"spend_food\" size=\"9\" maxlength=\"15\" onkeyup=\"FormatNumber(this.id,this.value,".$cp->resFood.",'','');\"> <a href=\"javascript:;\" onclick=\"document.getElementById('spend_food').value='".nf($cp->resFood)."';\">alles</a>
					</td>
				</tr>";			
	infobox_end(1);
	
	echo "<input type=\"submit\" class=\"button\" name=\"storage_submit\" id=\"storage_submit\" value=\"Einzahlen\"/>";
	echo "</form><br><br>";


	echo "</div>";
	
	
	
	//
	// Einzahlungen
	//
	
	if($action2=="spends")
	{
		$display = "";
	}
	else
	{
		$display = "none";
	}
	echo "<div id=\"tabSpends\" style=\"display:".$display.";\">";

 	echo "<form action=\"?page=".$page."&amp;action=".$_GET['action']."&amp;action2=spends\" method=\"post\" id=\"alliance_spends\">\n";
	echo $cstr;  
  
  echo "<h1>Einzahlungen</h1>";
  
  
  //
  // Filter
  //
  
  infobox_start("Filter",1,0);
  
  // Ausgabe
  echo "<tr>
  				<td class=\"tbltitle\">Ausgabe:</td>
  				<td class=\"tbldata\">
  					<input type=\"radio\" name=\"output\" id=\"output\" value=\"0\" checked=\"checked\"/> Einzeln / <input type=\"radio\" name=\"output\" id=\"output\" value=\"1\"/> Summiert
  				</td>
  			</tr>";
  
  // Limit
	echo "<tr>
  				<td class=\"tbltitle\">Einzahlungen:</td>
  				<td class=\"tbldata\"> 
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
  				<td class=\"tbltitle\">Von User:</td>
  				<td class=\"tbldata\">
  					<select id=\"user_spends\" name=\"user_spends\">
							<option value=\"0\">alle</option>";
					  	// Allianzuser
							foreach($alliance_members as $id => $nick)
							{
					  		echo "<option value=\"".$id."\">".$nick."</option>";
					  	}
  		echo "</select>
  				</td>
  			</tr>";
  echo "<tr>
  				<td class=\"tbldata\" style=\"text-align:center;\" colspan=\"2\">
  					<input type=\"submit\" class=\"button\" name=\"filter_submit\" id=\"filter_submit\" value=\"Anzeigen\"\"/>
  				</td>
  			</tr>";
  infobox_end(1);
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
	  	$user_message = "von ".$alliance_members[$user]." ";
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
			alliance_spend_alliance_id='".$cu->allianceId()."'
			".$user_sql.";");		
		if(mysql_num_rows($res)>0)
		{						
			$arr=mysql_fetch_assoc($res);
			
			infobox_start("Total eingezahlte Rohstoffe ".$user_message."",1);
			echo "<tr>
							<td class=\"tbltitle\" style=\"width:20%\">".RES_METAL."</td>
							<td class=\"tbltitle\" style=\"width:20%\">".RES_CRYSTAL."</td>
							<td class=\"tbltitle\" style=\"width:20%\">".RES_PLASTIC."</td>
							<td class=\"tbltitle\" style=\"width:20%\">".RES_FUEL."</td>
							<td class=\"tbltitle\" style=\"width:20%\">".RES_FOOD."</td>
						</tr>";
			echo "<tr>
							<td class=\"tbldata\">".nf($arr['metal'])."</td>
							<td class=\"tbldata\">".nf($arr['crystal'])."</td>
							<td class=\"tbldata\">".nf($arr['plastic'])."</td>
							<td class=\"tbldata\">".nf($arr['fuel'])."</td>
							<td class=\"tbldata\">".nf($arr['food'])."</td>
						</tr>";
			infobox_end(1);
		}
		else
		{
			infobox_start("Einzahlungen");
			echo "Es wurden noch keine Rohstoffe eingezahlt!";
			infobox_end();
		}
	}
	// Einzahlungen werden einzelen ausgegeb
	else
	{

  	if($user>0)
	  {
	  	$user_sql = "AND alliance_spend_user_id='".$user."'";
	  	$user_message = "von ".$alliance_members[$user]." ";
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
			alliance_spend_alliance_id='".$cu->allianceId()."'
			".$user_sql."
		ORDER BY
			alliance_spend_time DESC
		".$limit_sql.";");		
		if(mysql_num_rows($res)>0)
		{						
			while($arr=mysql_fetch_assoc($res))
			{
				infobox_start("".$alliance_members[$arr['alliance_spend_user_id']]." - ".df($arr['alliance_spend_time'])."",1);
				echo "<tr>
								<td class=\"tbltitle\" style=\"width:20%\">".RES_METAL."</td>
								<td class=\"tbltitle\" style=\"width:20%\">".RES_CRYSTAL."</td>
								<td class=\"tbltitle\" style=\"width:20%\">".RES_PLASTIC."</td>
								<td class=\"tbltitle\" style=\"width:20%\">".RES_FUEL."</td>
								<td class=\"tbltitle\" style=\"width:20%\">".RES_FOOD."</td>
							</tr>";
				echo "<tr>
								<td class=\"tbldata\">".nf($arr['alliance_spend_metal'])."</td>
								<td class=\"tbldata\">".nf($arr['alliance_spend_crystal'])."</td>
								<td class=\"tbldata\">".nf($arr['alliance_spend_plastic'])."</td>
								<td class=\"tbldata\">".nf($arr['alliance_spend_fuel'])."</td>
								<td class=\"tbldata\">".nf($arr['alliance_spend_food'])."</td>
							</tr>";
				infobox_end(1);
			}
			
		}
		else
		{
			infobox_start("Einzahlungen");
			echo "Es wurden noch keine Rohstoffe eingezahlt!";
			infobox_end();
		}
	}

	echo "</div>";

?>