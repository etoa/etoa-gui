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
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////
	//
	// 	File: defense.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Builds planetar defense
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	//info-link Definitionen
	define("ITEMS_TBL","defense");
	define("REQ_TBL","def_requirements");
	define("REQ_ITEM_FLD","req_def_id");
	define("ITEM_ID_FLD","def_id");
	define("ITEM_NAME_FLD","def_name");
	define("RACE_TO_ADD"," AND (def_race_id=0 OR def_race_id='".$cu->raceId."')");
	define("ITEM_SHOW_FLD","def_show");
	define("ITEM_ORDER_FLD","def_order");
	define("NO_ITEMS_MSG","In dieser Kategorie gibt es keine Verteidigungsanlagen!");
	define("HELP_URL","?page=help&site=defense");

	// BEGIN SKRIPT //

	echo "<form action=\"?page=$page\" method=\"post\">";
	
	//Tabulator var setzten (für das fortbewegen des cursors im forumular)
	$tabulator = 1;
	
	//Zähle initialisieren für alle Analgen
	$allCnt = 0;

	//Gentech level laden
	$tlres = dbquery("
	SELECT
		techlist_current_level
	FROM
		techlist
	WHERE
    techlist_user_id='".$cu->id."'
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

	// Waffenfabrik Level und Arbeiter laden
  $werft_res = dbquery("
  SELECT
  	buildlist_current_level,
  	buildlist_people_working,
  	buildlist_deactivated
  FROM
  	buildlist
  WHERE
  	buildlist_entity_id='".$cp->id()."'
  	AND buildlist_building_id='".FACTORY_ID."'
  	AND buildlist_current_level>='1'
  	AND buildlist_user_id='".$cu->id."'");
	
	
	// Verteidigungs-Infos
	ob_start();
	
	$struct = 0;
	$shield = 0;
	$weapon = 0;
	$heal = 0;
	$count = 0;

  tableStart("Verteidigungs-Infos");
  $res = dbquery("
  SELECT
  	def_structure,
		def_shield,
		def_weapon,
		def_heal,
		deflist_count  	
  FROM
  	deflist
  INNER JOIN
  	defense
  ON
  	deflist_def_id=def_id
  	AND deflist_entity_id=".$cp->id()."
  	AND deflist_count>0;");
  if (mysql_num_rows($res)>0)
  {
  	$struct=0;$shield=0;$weapon=0;$count=0;  	
  	while ($arr=mysql_fetch_array($res))
  	{
  		$struct += $arr['def_structure']*$arr['deflist_count'];
  		$shield += $arr['def_shield']*$arr['deflist_count'];
  		$weapon += $arr['def_weapon']*$arr['deflist_count'];
  		$heal += $arr['def_heal']*$arr['deflist_count'];
  		$count += $arr['deflist_count'];
  	}
		
		// Forschung laden und bonus dazu rechnen 
    // Liest Level der Waffen-,Schild-,Panzerungs-,Regena Tech aus Datenbank (att)
		$weapon_tech_a=1;
		$structure_tech_a=1;
    $shield_tech_a=1;
    $heal_tech_a=1;

    $techres_a = dbquery("
		SELECT
			techlist_tech_id,
			techlist_current_level,
			tech_name
		FROM
			techlist
		INNER JOIN
			technologies
		ON 
			techlist_tech_id=tech_id
		AND
			techlist_user_id='".$cu->id."'
			AND
			(
				techlist_tech_id='".STRUCTURE_TECH_ID."'
				OR techlist_tech_id='".SHIELD_TECH_ID."'
				OR techlist_tech_id='".WEAPON_TECH_ID."'
				OR techlist_tech_id='".REGENA_TECH_ID."'
			)
  		;");

      while ($techarr_a = mysql_fetch_array($techres_a))
      {
          if ($techarr_a['techlist_tech_id']==SHIELD_TECH_ID)
					{
              $shield_tech_a+=($techarr_a['techlist_current_level']/10);
							$shield_tech_name = $techarr_a["tech_name"];
							$shield_tech_level = $techarr_a["techlist_current_level"];
					}
          if ($techarr_a['techlist_tech_id']==STRUCTURE_TECH_ID)
					{
              $structure_tech_a+=($techarr_a['techlist_current_level']/10);
							$structure_tech_name = $techarr_a["tech_name"];
							$structure_tech_level = $techarr_a["techlist_current_level"];
					}
          if ($techarr_a['techlist_tech_id']==WEAPON_TECH_ID)
					{
              $weapon_tech_a+=($techarr_a['techlist_current_level']/10);
							$weapon_tech_name = $techarr_a["tech_name"];
							$weapon_tech_level = $techarr_a["techlist_current_level"];
					}
          if ($techarr_a['techlist_tech_id']==REGENA_TECH_ID)
					{
              $heal_tech_a+=($techarr_a['techlist_current_level']/10);
							$heal_tech_name = $techarr_a["tech_name"];
							$heal_tech_level = $techarr_a["techlist_current_level"];
					}
      }

		echo "<tr><th><b>Einheit</b></th><th>Grundwerte</th><th>Aktuelle Werte</th></tr>";
  	echo "<tr>
			<td class=\"tbldata\"><b>Struktur:</b></td>
			<td class=\"tbldata\">".nf($struct)."</td>
			<td class=\"tbldata\">".nf($struct*$structure_tech_a);
			if ($structure_tech_a>1)
			{
				echo " (".get_percent_string($structure_tech_a,1)." durch ".$structure_tech_name." ".$structure_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Schilder:</b></td>
			<td class=\"tbldata\">".nf($shield)."</td>
			<td class=\"tbldata\">".nf($shield*$shield_tech_a);
			if ($shield_tech_a>1)
			{
				echo " (".get_percent_string($shield_tech_a,1)." durch ".$shield_tech_name." ".$shield_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Waffen:</b></td>
			<td class=\"tbldata\">".nf($weapon)."</td>
			<td class=\"tbldata\">".nf($weapon*$weapon_tech_a);
			if ($weapon_tech_a>1)
			{
				echo " (".get_percent_string($weapon_tech_a,1)." durch ".$weapon_tech_name." ".$weapon_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Reparatur:</b></td>
			<td class=\"tbldata\">".nf($heal)."</td>
			<td class=\"tbldata\">".nf($heal*$heal_tech_a);
			if ($heal_tech_a>1)
			{
				echo " (".get_percent_string($heal_tech_a,1)." durch ".$heal_tech_name." ".$heal_tech_level.")";
			}
			echo "</td></tr>";
  	echo "<tr><td class=\"tbldata\"><b>Anzahl Anlagen:</b></td><td class=\"tbldata\" colspan=\"2\">".nf($count)."</td></tr>";
  }
  else
  {
  	echo "<tr><td class=\"tbldata\"><i>Keine Verteidigung vorhanden!</i></td></tr>";
  }
  tableEnd();
  $def_info_string = ob_get_contents();
  ob_end_clean();

  // Prüfen ob Werft gebaut ist
  if (mysql_num_rows($werft_res)>0)
  {
		$werft_arr = mysql_fetch_array($werft_res);
  	define('CURRENT_FACTORY_LEVEL',$werft_arr['buildlist_current_level']);

		// Titel
		echo "<h1>Waffenfabrik (Stufe ".CURRENT_FACTORY_LEVEL.") des Planeten ".$cp->name."</h1>";

		// Ressourcen anzeigen
		$cp->resBox($cu->properties->smallResBox);
		
		echo $def_info_string;

		// Prüfen ob dieses Gebäude deaktiviert wurde
		if ($werft_arr['buildlist_deactivated']>time())
		{
			iBoxStart("Geb&auml;ude nicht bereit");
			echo "Diese Waffenfabrik ist bis ".date("d.m.Y H:i",$werft_arr['buildlist_deactivated'])." deaktiviert.";
			iBoxEnd();
		}
		// Werft anzeigen
		else
		{

			//
			//Felder berechnung (wird im "Ausbauteil" wie auch im "Anzeigeteil" verwendet, deswegen am anfang EINE abfrage
			//

			//Felder von bauenden Gebäuden laden
			$building_fields=0;
    	$field_res = dbquery("
    	SELECT 
    			building_fields
    	FROM  
    			buildings 
    			INNER JOIN
    			buildlist 
    			ON building_id=buildlist_building_id
    			AND buildlist_build_end_time>'0';");
			if (mysql_num_rows($field_res)>0)
			{
				while($field_arr=mysql_fetch_array($field_res))
				{
					$building_fields+=$field_arr['building_fields'];
				}
			}  

      // Felder die von bauender Def besetzt sein werden
      $def_fields=0;
			$field_res=dbquery("
			SELECT
				SUM(def_fields * queue_cnt) AS fields
			FROM
				def_queue
			INNER JOIN
  			defense
  		  ON queue_def_id=def_id
  		  AND queue_entity_id='".$cp->id()."'
  		  AND queue_endtime>'".time()."'
				AND queue_user_id='".$cu->id."'
			;");
			if (mysql_num_rows($field_res)>0)
			{
				while($field_arr=mysql_fetch_array($field_res))
				{
					$def_fields+=$field_arr['fields'];
				}
			}       
			
			//Berechnet freie Felder   
			$fields_available = $cp->fields+$cp->fields_extra-$cp->fields_used-$def_fields-$building_fields;		

    	// level zählen welches die waffenfabrik über dem angegeben level ist und faktor berechnen
    	$need_bonus_level = CURRENT_FACTORY_LEVEL - $conf['build_time_boni_waffenfabrik']['p1'];
    	if($need_bonus_level <= 0)
    	{
    		$time_boni_factor=1;
    	}
    	else
    	{
    		$time_boni_factor=1-($need_bonus_level*($conf['build_time_boni_waffenfabrik']['v']/100));
    	}
    	$people_working = $werft_arr['buildlist_people_working'];
    	
    	// Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
    	if (CURRENT_FACTORY_LEVEL>=DEFQUEUE_CANCEL_MIN_LEVEL)
    	{
    		$cancel_res_factor = min(DEFQUEUE_CANCEL_END,DEFQUEUE_CANCEL_START+((CURRENT_FACTORY_LEVEL-DEFQUEUE_CANCEL_MIN_LEVEL)*DEFQUEUE_CANCEL_FACTOR));
    	}
    	else
    	{
    		$cancel_res_factor=0;
    	}

    	// Infos anzeigen
    	echo '<div>';
    	
    	//echo '<div style="float:left;width:450px;text-align:left;font-size:9pt;">';											
    	tableStart("Fabrik-Infos");
    	echo "<tr><td class=\"tbldata\"><b>Eingestellte Arbeiter:</b> ".nf($people_working)."<br/>
    	<b>Bauzeitverringerung:</b> ";
    	if ($need_bonus_level>=0)
    	{
    		echo get_percent_string($time_boni_factor)." durch Stufe ".CURRENT_FACTORY_LEVEL."<br/>";
    	}
    	else
    	{
    		echo "Stufe ".$conf['build_time_boni_waffenfabrik']['p1']." erforderlich!<br/>";
    	}
    	if ($cancel_res_factor>0)
    	{
    		echo "<b>Ressourcenrückgabe bei Abbruch:</b> ".($cancel_res_factor*100)."% (ohne ".RES_FOOD.", ".(DEFQUEUE_CANCEL_END*100)."% maximal)";
    		$cancelable = true;
    	}
    	else
    	{
    		echo "<b>Abbruchmöglichkeit:</b> Stufe ".DEFQUEUE_CANCEL_MIN_LEVEL." erforderlich!";
    		$cancelable = false;
    	}    	
    	echo "</td></tr>";
    	//iBoxEnd();
    	//echo '</div>';


	/****************************
	*  Sortiereingaben speichern *
	****************************/

			if(count($_POST)>0 && isset($_POST['sort_submit']))
			{
				$cu->properties->itemOrderDef = $_POST['sort_value'];
       	$cu->properties->itemOrderWay = $_POST['sort_way'];	
			}

			
			
	/*************
	* Sortierbox *
	*************/
	
			//Legt Sortierwerte in einem Array fest
			$values = array(
											"name"=>"Name",
											"battlepoints"=>"Kosten",
											"fields"=>"Felder",
											"weapon"=>"Waffen",
											"structure"=>"Struktur",
											"shield"=>"Schild",
											"costs_metal"=>"Titan",
											"costs_crystal"=>"Silizium",
											"costs_plastic"=>"PVC",
											"costs_fuel"=>"Tritium"
											);
											
			echo "<tr>
							<td class=\"tbldata\" style=\"text-align:center;\">
								<select name=\"sort_value\">";
								foreach ($values as $value => $name)
								{		
									echo "<option value=\"".$value."\"";
									if($cu->properties->itemOrderDef==$value)
									{
										echo " selected=\"selected\"";
									}
									echo ">".$name."</option>";							
								}																																																							
					echo "</select>
							
								<select name=\"sort_way\">";
								
									//Aufsteigend
									echo "<option value=\"ASC\"";
									if($cu->properties->itemOrderWay=='ASC') echo " selected=\"selected\"";
									echo ">Aufsteigend</option>";
									
									//Absteigend
									echo "<option value=\"DESC\"";
									if($cu->properties->itemOrderWay=='DESC') echo " selected=\"selected\"";
									echo ">Absteigend</option>";	
																	
					echo "</select>						
							
								<input type=\"submit\" class=\"button\" name=\"sort_submit\" value=\"Sortieren\"/>
							</td>
						</tr>";
			tableEnd();
			//echo '</div>';
			echo '<br style="clear:both;" /></div>';
			
			echo "</form>";			

		echo "<form action=\"?page=$page\" method=\"post\">";



	/****************************
	*  Anlagen in Auftrag geben *
	****************************/

			if(count($_POST)>0 && isset($_POST['submit']) && checker_verify())
			{
				tableStart();
				echo "<tr><th>Ergebnisse des Bauauftrags</th></tr>";

				// Endzeit bereits laufender Aufträge laden
				$end_time=time();
				
				//Log variablen setzten
				$log_defs="";
				$total_duration=0;
				$total_metal=0;
				$total_crystal=0;
				$total_plastic=0;
				$total_fuel=0;
				$total_food=0;
				
				$qres=dbquery("
				SELECT
					queue_endtime
				FROM
					def_queue
				WHERE
  				queue_entity_id='".$cp->id()."'
  				AND queue_user_id='".$cu->id."'
  			ORDER BY
  				queue_endtime DESC
  			LIMIT 1;
				;");
				if (mysql_num_rows($qres)>0)
				{
					$qarr=mysql_fetch_row($qres);
					if ($qarr[0]>$end_time)
					{
						$end_time=$qarr[0];
					}
				}

				//
				// Bauaufträge speichern
				//
				$counter=0;
				foreach ($_POST['build_count'] as $def_id=> $build_cnt)
				{
					$build_cnt=nf_back($build_cnt);

					if ($build_cnt>0)
					{
						// Verteidigung laden
						$dres = dbquery("
						SELECT
							*
						FROM
							defense
						WHERE
							def_id='".$def_id."';
						");
						$darr = mysql_fetch_array($dres);

	   				// TODO: Überprüfen
						//Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
    	      if($build_cnt > $darr['def_max_count'] && $darr['def_max_count']!=0)
    	    	{
    	      	$build_cnt=$darr['def_max_count'];
    	      }

						//Wenn der User nicht genug freie Felder hat, die Anzahl Anlagen drosseln
						if ($darr['def_fields']>0 && $fields_available - $darr['def_fields'] * $build_cnt < 0)
						{
							$build_cnt=floor($fields_available/$darr['def_fields']);
						}


    				// TODO: Überprüfen
						//Wenn der User nicht genug Ress hat, die Anzahl Anlagen drosseln
						//Titan
						if ($darr['def_costs_metal']>0)
						{
							$bf['metal']=$cp->resMetal/$darr['def_costs_metal'];
						}
						else
						{
							$bc['metal']=0;
						}
						//Silizium
						if ($darr['def_costs_crystal']>0)
						{
							$bf['crystal']=$cp->resCrystal/$darr['def_costs_crystal'];
						}
						else
						{
							$bc['crystal']=0;
						}
						//PVC
						if ($darr['def_costs_plastic']>0) 
						{
							$bf['plastic']=$cp->resPlastic/$darr['def_costs_plastic']; 
						}
						else 
						{
							$bc['plastic']=0;
						}
						//Tritium
						if ($darr['def_costs_fuel']>0) 
						{
							$bf['fuel']=$cp->resFuel/$darr['def_costs_fuel']; 
						}
						else 
						{
							$bc['fuel']=0;
						}
						//Nahrung
						if ($_POST['additional_food_costs']>0 || $darr['def_costs_food']>0)
						{
							 $bf['food']=$cp->resFood/($_POST['additional_food_costs']+$darr['def_costs_food']); 
						}
						else 
						{
							$bc['food']=0;
						}

						//Anzahl Drosseln
						if ($build_cnt>floor(min($bf)))
						{
							$build_cnt=floor(min($bf));
						}
						
						//Anzahl muss grösser als 0 sein
						if ($build_cnt>0)
						{
							//Errechne Kosten pro auftrag schiffe
							$bc['metal']=$darr['def_costs_metal']*$build_cnt;
							$bc['crystal']=$darr['def_costs_crystal']*$build_cnt;
							$bc['plastic']=$darr['def_costs_plastic']*$build_cnt;
							$bc['fuel']=$darr['def_costs_fuel']*$build_cnt;
							$bc['food']=($_POST['additional_food_costs']+$darr['def_costs_food'])*$build_cnt;

    	        //Berechnete Ress provisorisch abziehen
    	        $cp->resMetal-=$bc['metal'];
    	        $cp->resCrystal-=$bc['crystal'];
    	        $cp->resPlastic-=$bc['plastic'];
    	        $cp->resFuel-=$bc['fuel'];
    	        $cp->resFood-=$bc['food'];

							// Bauzeit pro Schiff berechnen
							$btime = ($darr['def_costs_metal'] + $darr['def_costs_crystal'] + $darr['def_costs_plastic'] + $darr['def_costs_fuel'] + $darr['def_costs_food']) / 12 * GLOBAL_TIME * DEF_BUILD_TIME * $time_boni_factor;

	    				// TODO: Überprüfen
							//Rechnet zeit wenn arbeiter eingeteilt sind
							$btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
							if ($btime_min<DEFENSE_MIN_BUILD_TIME) $btime_min=DEFENSE_MIN_BUILD_TIME;
							$btime=$btime-$people_working*$cfg->value('people_work_done');
							if ($btime<$btime_min) $btime=$btime_min;
							$obj_time=ceil($btime);

							// Gesamte Bauzeit berechnen
							$duration=$build_cnt*$obj_time;

							if ($end_time>0)
							{
								$start_time=$end_time;
							}
							$end_time = $start_time+$duration;

							// Auftrag speichern
    	        dbquery("
    	        INSERT INTO
    	        def_queue
    	            (queue_user_id,
    	            queue_def_id,
    	            queue_entity_id,
    	            queue_cnt,
    	            queue_starttime,
    	            queue_endtime,
    	            queue_objtime)
    	        VALUES
    	            ('".$cu->id."',
    	            '".$def_id."',
    	            '".$cp->id()."',
    	            '".$build_cnt."',
    	            '".$start_time."',
    	            '".$end_time."',
    	            '".$obj_time."');");
    	        $deflist_id = mysql_insert_id();
								
							echo "<tr><td>".nf($build_cnt)." ".$darr['def_name']." in Auftrag gegeben!</td></tr>";
							
							//Rohstoffe summieren, diese werden nach der Schleife abgezogen
							$total_metal+=$bc['metal'];
							$total_crystal+=$bc['crystal'];
							$total_plastic+=$bc['plastic'];
							$total_fuel+=$bc['fuel'];
							$total_food+=$bc['food'];
							
							
							//Daten für Log speichern
							$log_defs.="<b>".$darr['def_name']."</b>: ".nf($build_cnt)." (".tf($duration).")<br>";
							$total_duration+=$duration;							
						}
						else
						{
							echo "<tr><td>".$darr['def_name'].": Zu wenig Rohstoffe oder Felder für diese Anzahl!</td></tr>";
						}
						$counter++;
					}							
				}
				
				// Die Roshtoffe der $c-variablen wieder beigeben, da sie sonst doppelt abgezogen werden
        $cp->resMetal+=$total_metal;
        $cp->resCrystal+=$total_crystal;
        $cp->resPlastic+=$total_plastic;
        $cp->resFuel+=$total_fuel;
        $cp->resFood+=$total_food;					
				
				//Rohstoffe vom Planeten abziehen und aktualisieren
				$cp->changeRes(-$total_metal,-$total_crystal,-$total_plastic,-$total_fuel,-$total_food);
												
				//Log schreiben
				$log_text = "
				<b>Verteidigungsauftrag Bauen</b><br><br>
				<b>User:</b> [USER_ID=".$cu->id.";USER_NICK=".$cu->nick."]<br>
				<b>Planeten:</b> [PLANET_ID=".$cp->id().";PLANET_NAME=".$cp->name."]<br>
				<b>Dauer des gesamten Auftrages:</b> ".tf($total_duration)."<br>
				<b>Ende des gesamten Auftrages:</b> ".date("Y-m-d H:i:s",$end_time)."<br>
				<b>Waffenfabrik Level:</b> ".CURRENT_FACTORY_LEVEL."<br>
				<b>Eingesetzte Bewohner:</b> ".nf($people_working)."<br>
				<b>Gen-Tech Level:</b> ".GEN_TECH_LEVEL."<br><br>
				<b>Kosten</b><br>
				<b>".RES_METAL.":</b> ".nf($total_metal)."<br>
				<b>".RES_CRYSTAL.":</b> ".nf($total_crystal)."<br>
				<b>".RES_PLASTIC.":</b> ".nf($total_plastic)."<br>
				<b>".RES_FUEL.":</b> ".nf($total_fuel)."<br>
				<b>".RES_FOOD.":</b> ".nf($total_food)."<br><br>
				<b>Rohstoffe auf dem Planeten</b><br><br>
				<b>".RES_METAL.":</b> ".nf($cp->resMetal)."<br>
				<b>".RES_CRYSTAL.":</b> ".nf($cp->resCrystal)."<br>
				<b>".RES_PLASTIC.":</b> ".nf($cp->resPlastic)."<br>
				<b>".RES_FUEL.":</b> ".nf($cp->resFuel)."<br>
				<b>".RES_FOOD.":</b> ".nf($cp->resFood)."<br><br>
				<b>Anlagen</b><br>
				".$log_defs."
				";
				
				//Log Speichern
				add_log_game_def($log_text,$cu->id,$cu->allianceId,$cp->id(),1,time());					
				
				if ($counter==0)
				{
					echo "<tr><td>Keine Schiffe gew&auml;hlt!</td></tr>";
				}
				tableEnd();
			}

			$time = time();
			checker_init();



	/*********************
	* Auftrag abbrechen  *
	*********************/
			if (isset($_GET['cancel']) && $_GET['cancel']>0 && $cancelable)
			{
				$qres=dbquery("
				SELECT
					def_name,
					def_costs_metal,
					def_costs_crystal,
					def_costs_fuel,
					def_costs_plastic,
					def_costs_food,
					queue_starttime,
    			queue_endtime,
    			queue_objtime,
    			queue_cnt
				FROM
					def_queue
				INNER JOIN
 	  			defense
	  		  ON queue_def_id=def_id
					AND queue_id='".intval($_GET['cancel'])."'
					AND queue_user_id='".$cu->id."'
					AND queue_entity_id='".$cp->id()."'
					AND queue_endtime>'".$time."'
				;");
				if (mysql_num_rows($qres)>0)
				{
					$qarr=mysql_fetch_array($qres);
					
					//Zu erhaltende Rohstoffe errechnen
					$obj_cnt = min(ceil(($qarr['queue_endtime']-max($time,$qarr['queue_starttime']))/$qarr['queue_objtime']),$qarr['queue_cnt']);
					echo "Breche den Bau von ".$obj_cnt." ".$qarr['def_name']." ab...<br/>";					
					
					$ret['metal']=$qarr['def_costs_metal']*$obj_cnt*$cancel_res_factor;
					$ret['crystal']=$qarr['def_costs_crystal']*$obj_cnt*$cancel_res_factor;
					$ret['plastic']=$qarr['def_costs_plastic']*$obj_cnt*$cancel_res_factor;
					$ret['fuel']=$qarr['def_costs_fuel']*$obj_cnt*$cancel_res_factor;
					$ret['food']=$qarr['def_costs_food']*$obj_cnt*$cancel_res_factor;

					//Auftrag löschen
					dbquery("
					DELETE FROM
					 def_queue
					WHERE
						queue_id='".intval($_GET['cancel'])."';");

					//Nachkommende Aufträge werden Zeitlich nach vorne verschoben
					$tres=dbquery("
					SELECT
						queue_id,
						queue_starttime,
						queue_endtime
					FROM
						def_queue
					WHERE
						queue_starttime>='".$qarr['queue_endtime']."'
						AND queue_user_id='".$cu->id."'
						AND queue_entity_id='".$cp->id()."'
					ORDER BY
						queue_starttime
					;");
					if (mysql_num_rows($tres)>0)
					{
						$new_starttime=max($qarr['queue_starttime'],$time);
						while ($tarr=mysql_fetch_array($tres))
						{
							$new_endtime=$new_starttime+$tarr['queue_endtime']-$tarr['queue_starttime'];
							dbquery("
							UPDATE
								def_queue
							SET
								queue_starttime='".$new_starttime."',
								queue_endtime='".$new_endtime."'
							WHERE
								queue_id='".$tarr['queue_id']."'
							");
							$new_starttime=$new_endtime;
						}
					}
					
					//Rohstoffe dem Planeten gutschreiben und aktualisieren
					$cp->changeRes($ret['metal'],$ret['crystal'],$ret['plastic'],$ret['fuel'],$ret['food']);						
						
					echo "Der Auftrag wurde abgebrochen!<br/><br/>";
						
					//Log schreiben
					$log_text = "
					<b>Verteidigungsauftrag Abbruch</b><br><br>
					<b>User:</b> [USER_ID=".$cu->id.";USER_NICK=".$cu->nick."]<br>
					<b>Planeten:</b> [PLANET_ID=".$cp->id().";PLANET_NAME=".$cp->name."]<br>
					<b>Anlage:</b> ".$qarr['def_name']."<br>
					<b>Anzahl:</b> ".nf($qarr['queue_cnt'])."<br>
					<b>Auftragsdauer:</b> ".tf($qarr['queue_objtime']*$qarr['queue_cnt'])."<br><br>
					<b>Erhaltene Rohstoffe</b><br>
					<b>Faktor:</b> ".$cancel_res_factor."<br>
					<b>".RES_METAL.":</b> ".nf($ret['metal'])."<br>
					<b>".RES_CRYSTAL.":</b> ".nf($ret['crystal'])."<br>
					<b>".RES_PLASTIC.":</b> ".nf($ret['plastic'])."<br>
					<b>".RES_FUEL.":</b> ".nf($ret['fuel'])."<br>
					<b>".RES_FOOD.":</b> ".nf($ret['food'])."<br><br>
					<b>Rohstoffe auf dem Planeten</b><br><br>
					<b>".RES_METAL.":</b> ".nf($cp->resMetal)."<br>
					<b>".RES_CRYSTAL.":</b> ".nf($cp->resCrystal)."<br>
					<b>".RES_PLASTIC.":</b> ".nf($cp->resPlastic)."<br>
					<b>".RES_FUEL.":</b> ".nf($cp->resFuel)."<br>
					<b>".RES_FOOD.":</b> ".nf($cp->resFood)."<br>
					";
					
					//Log Speichern
					add_log_game_def($log_text,$cu->id,$cu->allianceId,$cp->id(),0,time());					
				}
			}



	/*********************************
	* Liste der Bauaufträge anzeigen *
	*********************************/
			$qres = dbquery("
			SELECT
    		def_name,
    		queue_id,
    		queue_def_id,
    		queue_cnt,
    		queue_starttime,
    		queue_endtime,
    		queue_objtime
			FROM
    		def_queue
    	INNER JOIN
    		defense
    		ON
    		queue_def_id=def_id
  			AND queue_entity_id='".$cp->id()."'
  			AND queue_user_id='".$cu->id."'
  			AND queue_endtime>'".$time."'
    	ORDER BY
				queue_starttime ASC;");
			if (mysql_num_rows($qres)>0)
			{
				tableStart("Bauliste");
				echo "<tr>
								<th style=\"width:40px;\">Anzahl</th>
								<th>Bauauftrag</th>
								<th style=\"width:120px;\">Start</th>
								<th style=\"width:120px;\">Ende</th>
								<th style=\"width:80px;\">Verbleibend</th>
								<th style=\"width:80px;\">Aktionen</th>
							</tr>";
				$first=true;
				$absolut_starttime=0;
				while ($qarr = mysql_fetch_array($qres))
				{
					if ($first)
					{
						$obj_t_remaining = ((($qarr['queue_endtime']-$time) / $qarr['queue_objtime'])-floor(($qarr['queue_endtime']-$time) / $qarr['queue_objtime']))*$qarr['queue_objtime'];
						if ($obj_t_remaining==0)
						{
							$obj_t_remaining = $qarr['queue_objtime'];
						}
						$obj_time = $qarr['queue_objtime'];
						$absolute_starttime=$qarr['queue_starttime'];

						$obj_t_passed = $qarr['queue_objtime']-$obj_t_remaining;
						echo "<tr>
								<th colspan=\"2\">Aktuell</th>
								<th style=\"width:150px;\">Start</th>
								<th style=\"width:150px;\">Ende</th>
								<th style=\"width:80px;\" colspan=\"2\">Verbleibend</th>
							</tr>";
						echo "<tr>";
						echo "<td class=\"tbldata\" colspan=\"2\">".$qarr['def_name']."</td>";
						echo "<td class=\"tbldata\">".df(time()-$obj_t_passed,1)."</td>";
						echo "<td class=\"tbldata\">".df(time()+$obj_t_remaining,1)."</td>";
						echo "<td class=\"tbldata\" colspan=\"2\">".tf($obj_t_remaining)."</td>
						</tr>";
						echo "<tr>
								<th style=\"width:40px;\">Anzahl</th>
								<th>Bauauftrag</th>
								<th style=\"width:150px;\">Start</th>
								<th style=\"width:150px;\">Ende</th>
								<th style=\"width:150px;\">Verbleibend</th>
								<th style=\"width:80px;\">Aktionen</th>
							</tr>";
						$first=false; 
					}

					echo "<tr>";
					echo "<td class=\"tbldata\" id=\"objcount\">".$qarr['queue_cnt']."</td>";
					echo "<td class=\"tbldata\">".$qarr['def_name']."</td>";
					echo "<td class=\"tbldata\">".df($absolute_starttime,1)."</td>";
					echo "<td class=\"tbldata\">".df($absolute_starttime+$qarr['queue_endtime']-$qarr['queue_starttime'],1)."</td>";
					echo "<td class=\"tbldata\">".tf($qarr['queue_endtime']-time(),1)."</td>";
					echo "<td class=\"tbldata\" id=\"cancel\">";
					if ($cancelable)
					{
						echo "<a href=\"?page=$page&amp;cancel=".$qarr['queue_id']."\" onclick=\"return confirm('Soll dieser Auftrag wirklich abgebrochen werden?');\">Abbrechen</a>";
					}
					else
					{
						echo "-";
					}
					echo "</td>
					</tr>";

					
					//Setzt die Startzeit des nächsten Schiffes, auf die Endzeit des jetztigen Schiffes
					$absolute_starttime=$qarr['queue_endtime'];
					
					//Summiert die Anzahl der bauenden Anlagen pro Typ, für im unteren Teil "Maximal Anzahl erreicht" anzeigen zu lassen
					//Würde beispielsweise die Bauliste nach den Anlagen kommen, müsste man eine neue Queue-Abfrage machen!
					if(isset($queue_cnt[$qarr['queue_def_id']]))
					{
						$queue_cnt[$qarr['queue_def_id']]+=$qarr['queue_cnt'];
					}
					else
					{
						$queue_cnt[$qarr['queue_def_id']]=$qarr['queue_cnt'];
					}					
				}
				tableEnd();
			 	echo "<br/><br/>";

			}



	/***********************
	* Anlagen auflisten    *
	***********************/

			// Vorausetzungen laden
			$res = dbquery("SELECT * FROM def_requirements;");
			while ($arr = mysql_fetch_array($res))
			{
				//Gebäude Vorausetzungen
				if ($arr['req_req_building_id']>0) 
				{
					$req[$arr['req_def_id']]['b'][$arr['req_req_building_id']]=$arr['req_req_building_level'];
				}
				
				//Technologie Voraussetzungen
				if ($arr['req_req_tech_id']>0) 
				{
					$req[$arr['req_def_id']]['t'][$arr['req_req_tech_id']]=$arr['req_req_tech_level'];
				}
			}


			//Technologien laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				techlist 
			WHERE 
				techlist_user_id='".$cu->id."';");
			while ($arr = mysql_fetch_array($res))
			{
				$techlist[$arr['techlist_tech_id']]=$arr['techlist_current_level'];
			}

			//Gebäude laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				buildlist 
			WHERE 
				buildlist_entity_id='".$cp->id()."' 
				AND buildlist_user_id='".$cu->id."';");
			while ($arr = mysql_fetch_array($res))
			{
				$buildlist[$arr['buildlist_building_id']]=$arr['buildlist_current_level'];
			}
				
			// Kategorien laden
			$cres=dbquery("
				SELECT
					cat_name,
					cat_id
				FROM
					def_cat
				ORDER BY
					cat_order
			;");
			if (mysql_num_rows($cres)>0)
			{
				while($carr=mysql_fetch_array($cres))
				{
					tableStart($carr['cat_name']);
					$cnt = 0;

					//Ordnung des Users beachten
					$order="def_".$cu->properties->itemOrderDef." ".$cu->properties->itemOrderWay."";

					// Auflistung der Schiffe (auch diese, die noch nicht gebaut wurden)
					$dres = dbquery("
					SELECT
    				def_id,
    				def_name,
    				def_shortcomment,
    				def_costs_metal,
    				def_costs_crystal,
    				def_costs_plastic,
    				def_costs_fuel,
    				def_costs_food,
    				def_fields,
    				def_show,
    				def_buildable,
    				def_structure,
    				def_shield,
    				def_weapon,
    				def_race_id,
    				def_max_count,
    				deflist_count
 					FROM
    				defense
    			LEFT JOIN
    				deflist
  					ON deflist_def_id=def_id
  					AND deflist_entity_id='".$cp->id()."'
  	        AND deflist_user_id='".$cu->id."'
   				WHERE
    				def_buildable='1'
    				AND def_cat_id='".$carr['cat_id']."'
    				AND def_show='1'
    				AND (def_race_id='0' OR def_race_id='".$cu->raceId."')
    			ORDER BY
    				".$order.";");
					if (mysql_num_rows($dres)>0)
					{
						//Einfache Ansicht
						if ($cu->properties->itemShow != 'full')
						{
							echo '<tr>
											<th colspan="2" class="tbltitle">Anlage</th>
											<th class="tbltitle">Zeit</th>
											<th class="tbltitle">'.RES_METAL.'</th>
											<th class="tbltitle">'.RES_CRYSTAL.'</th>
											<th class="tbltitle">'.RES_PLASTIC.'</th>
											<th class="tbltitle">'.RES_FUEL.'</th>
											<th class="tbltitle">'.RES_FOOD.'</th>
											<th class="tbltitle">Anzahl</th>
										</tr>';
						}
						
						while ($darr = mysql_fetch_array($dres))
						{
							// Prüfen ob Schiff gebaut werden kann
    			  	$build_def = 1;
    			  	// Gebäude prüfen
    			    if (isset($req[$darr['def_id']]['b']))
    			    {
  			        foreach ($req[$darr['def_id']]['b'] as $id=>$level)
  			        {
			            if (!isset($buildlist[$id]) || $buildlist[$id]<$level)
			            {
			            	$build_def = 0;
			            }
  			        }
    			    }
    			    
    			    
    			  	// Technologien prüfen
    			    if (isset($req[$darr['def_id']]['t']))
    			    {
  			        foreach ($req[$darr['def_id']]['t'] as $id=>$level)
  			        {
			            if (!isset($techlist[$id]) || $techlist[$id]<$level)
			            {
			            	$build_def = 0;
			            }
  			        }
    			    }
    			  

    			    // Defdatensatz zeigen
							if ($build_def==1)
							{
    			      //zählt die anzahl vertdeidigungsanlagen dieses typs auf dem Planeten...
    			      $def_count=0;
    			      //...gebaute anlagen
    			      $check_res1 = dbquery("
    			      SELECT
    			          deflist_count
    			      FROM
    			          deflist
    			      WHERE
    			      		deflist_entity_id='".$cp->id()."'
    			          AND deflist_def_id='".$darr['def_id']."'
    			          AND deflist_user_id='".$cu->id."';");
    			      if (mysql_num_rows($check_res1)>0)
    			      {
    			        while ($check_arr1=mysql_fetch_array($check_res1))
    			        {
    			            $def_count+=$check_arr1['deflist_count'];
    			        }
    			      }
    			      //...in der Bauliste
    			      if(isset($queue_cnt[$darr['def_id']]))
    			      {
    			      	$def_count+=$queue_cnt[$darr['def_id']];
    			      }


    						// Bauzeit berechnen
								$btime = ($darr['def_costs_metal']+$darr['def_costs_crystal']+$darr['def_costs_plastic']+$darr['def_costs_fuel']+$darr['def_costs_food']) / 12 * GLOBAL_TIME * DEF_BUILD_TIME * $time_boni_factor;
    			      $btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
    			      
    			      //Mindest Bauzeit
    			      if ($btime_min<DEFENSE_MIN_BUILD_TIME) 
    			      {
    			      	$btime_min=DEFENSE_MIN_BUILD_TIME;
    			      }
    			      
    			      $btime=ceil($btime-$people_working*$cfg->value('people_work_done'));
    			      if ($btime<$btime_min) 
    			      {
    			      	$btime=$btime_min;
    			      }

								//Nahrungskosten berechnen
								$food_costs = $people_working*$cfg->value('people_food_require') + $darr['def_costs_food'];
								
								//Nahrungskosten versteckt übermitteln
								echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"".$food_costs."\" />";
								
								
								
								//Errechnet wie viele Schiffe von diesem Typ maximal Gebaut werden können mit den aktuellen Rohstoffen und Felder
								
								//Felder
								if($darr['def_fields']>0)
								{
									$build_cnt_fields=floor($fields_available/$darr['def_fields']);
								}
								else
								{
									$build_cnt_fields=99999999999;
								}
														
								//Titan
								if($darr['def_costs_metal']>0)
								{
									$build_cnt_metal=floor($cp->resMetal/$darr['def_costs_metal']);
								}
								else
								{
									$build_cnt_metal=99999999999;
								}

								//Silizium
								if($darr['def_costs_crystal']>0)
								{
									$build_cnt_crystal=floor($cp->resCrystal/$darr['def_costs_crystal']);
								}
								else
								{
									$build_cnt_crystal=99999999999;
								}
						
								//PVC
								if($darr['def_costs_plastic']>0)
								{
									$build_cnt_plastic=floor($cp->resPlastic/$darr['def_costs_plastic']);
								}
								else
								{
									$build_cnt_plastic=99999999999;
								}
								
								//Tritium
								if($darr['def_costs_fuel']>0)
								{
									$build_cnt_fuel=floor($cp->resFuel/$darr['def_costs_fuel']);
								}
								else
								{
									$build_cnt_fuel=99999999999;
								}

								//Nahrung
								if($food_costs>0)
								{
									$build_cnt_food=floor($cp->resFood/$food_costs);
								}
								else
								{
									$build_cnt_food=99999999999;
								}

								//Begrente Anzahl baubar
								if($darr['def_max_count']!=0)
								{
									$max_cnt=$darr['def_max_count']-$def_count;
								}
								else
								{
									$max_cnt=99999999999;
								}

								//Effetiv max. baubare Schiffe in Betrachtung der Rohstoffe, der Felder und des Baumaximums
								$def_max_build=min($build_cnt_metal,$build_cnt_crystal,$build_cnt_plastic,$build_cnt_fuel,$build_cnt_food,$max_cnt,$build_cnt_fields);

								//Tippbox Nachricht generieren
								//X Anlagen baubar
								if($def_max_build>0)
								{
									$tm_cnt="Es k&ouml;nnen maximal ".nf($def_max_build)." Anlagen gebaut werden.";
								}
								//Zu wenig Felder.
								elseif($build_cnt_fields==0)
								{
									$tm_cnt="Es sind zu wenig Felder vorhanden für weitere Anlagen!";
								}
								//Zuwenig Rohstoffe. Wartezeit errechnen
								elseif($def_max_build==0 && $build_cnt_fields!=0)
								{
									//Wartezeit Titan
    			    		if ($cp->prodMetal>0)
    			    		{
    			    			$bwait['metal']=ceil(($darr['def_costs_metal']-$cp->resMetal)/$cp->prodMetal*3600);
    			    		}
    			    		else
    			    		{
    			    			$bwait['metal']=0;
    			    		}
    			    		
    			    		//Wartezeit Silizium
    			    		if ($cp->prodCrystal>0)
    			    		{
    			    			$bwait['crystal']=ceil(($darr['def_costs_crystal']-$cp->resCrystal)/$cp->prodCrystal*3600);
    			    		}
    			    		else
    			    		{ 
    			    			$bwait['crystal']=0;
    			    		}
    			    		
    			    		//Wartezeit PVC
    			    		if ($cp->prodPlastic>0)
    			    		{
    			    			$bwait['plastic']=ceil(($darr['def_costs_plastic']-$cp->resPlastic)/$cp->prodPlastic*3600);
    			    		}
    			    		else
    			    		{ 
    			    			$bwait['plastic']=0;
    			    		}
    			    		
    			    		//Wartezeit Tritium
    			    		if ($cp->prodFuel>0)
    			    		{
    			    			$bwait['fuel']=ceil(($darr['def_costs_fuel']-$cp->resFuel)/$cp->prodFuel*3600);
    			    		}
    			    		else
    			    		{ 
    			    			$bwait['fuel']=0;
    			    		}
    			    		
    			    		//Wartezeit Nahrung
    			    		if ($cp->prodFood>0)
    			    		{
    			    			$bwait['food']=ceil(($food_costs-$cp->resFood)/$cp->prodFood*3600);
    			    		}
    			    		else
    			    		{ 
    			    			$bwait['food']=0;
    			    		}
    			    		
    			    		//Maximale Wartezeit ermitteln
    			    		$bwmax=max($bwait['metal'],$bwait['crystal'],$bwait['plastic'],$bwait['fuel'],$bwait['food']);
    			    		
    			    		$tm_cnt="Rohstoffe verf&uuml;gbar in ".tf($bwmax)."";
								}
								else
								{
									$tm_cnt="";
								}

								//Stellt Rohstoff Rot dar, wenn es von diesem zu wenig auf dem Planeten hat
								//Titan
								if($darr['def_costs_metal']>$cp->resMetal)
								{
									$ress_style_metal="style=\"color:red;\"";
								}
								else
								{
									$ress_style_metal="";
								}
								
								//Silizium
								if($darr['def_costs_crystal']>$cp->resCrystal)
								{
									$ress_style_crystal="style=\"color:red;\"";
								}
								else
								{
									$ress_style_crystal="";
								}
								
								//PVC
								if($darr['def_costs_plastic']>$cp->resPlastic)
								{
									$ress_style_plastic="style=\"color:red;\"";
								}
								else
								{
									$ress_style_plastic="";
								}
								
								//Tritium
								if($darr['def_costs_fuel']>$cp->resFuel)
								{
									$ress_style_fuel="style=\"color:red;\"";
								}
								else
								{
									$ress_style_fuel="";
								}
								
								//Nahrung
								if($food_costs>$cp->resFood)
								{
									$ress_style_food="style=\"color:red;\"";
								}
								else
								{
									$ress_style_food="";
								}

								// Volle Ansicht
  			      	if($cu->properties->itemShow =='full')
  			      	{	
  			      		if ($cnt>0)
  			      		{
  			      			echo "<tr>
  			      							<td colspan=\"5\" style=\"height:5px;\"></td>
  			      					</tr>";
  			      		}
  			      	  $d_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$darr['def_id']."_middle.".IMAGE_EXT;
  			      	  
  			      	  echo "<tr>
  			      	  				<th colspan=\"5\" height=\"20\">".$darr['def_name']."</th>
  			      	  			</tr>
  			      	  			<tr>
  			      	  				<td class=\"tbldata\" width=\"120\" height=\"120\" rowspan=\"3\">";
				    			      	  //Bild mit Link zur Hilfe darstellen
														echo "<a href=\"".HELP_URL."&amp;id=".$darr[ITEM_ID_FLD]."\" title=\"Info zu dieser Anlage anzeigen\">
			    			      	  	<img src=\"".$d_img."\" width=\"120\" height=\"120\" border=\"0\" /></a>";
  			      	  	echo "</td>
  			      	  				<td class=\"tbldata\" colspan=\"4\" valign=\"top\">".$darr['def_shortcomment']."</td>
  			      	  			</tr>
  			      	  			<tr>
  			      	  				<th class=\"tbltitle\"  height=\"30\">Vorhanden:</th>
			    			      	  <td class=\"tbldata\">".nf($darr['deflist_count'])."</td>
			    			      	  <th class=\"tbltitle\">Felder pro Einheit:</th>
			    			      	  <td class=\"tbldata\">".nf($darr['def_fields'])."</td>
			    			      	</tr>
			    			      	<tr>
			    			      	 	<th class=\"tbltitle\" height=\"30\">Bauzeit</th>
  			      	  				<td class=\"tbldata\">".tf($btime)."</td>";
  			      	  				
			    			      	  //Maximale Anzahl erreicht
			    			      	  if ($def_count>=$darr['def_max_count'] && $darr['def_max_count']!=0)
			    			      	  {
			    			      	     	echo "<th class=\"tbltitle\" height=\"30\" colspan=\"2\"><i>Maximalanzahl erreicht</i></th>";
			    			      	  }
			    			      	  else
			    			      	  {
			    			      	      echo "<th class=\"tbltitle\" height=\"30\">In Aufrag geben:</th>
			    			      	      			<td class=\"tbldata\"><input type=\"text\" value=\"0\" name=\"build_count[".$darr['def_id']."]\" id=\"build_count_".$darr['def_id']."\" size=\"5\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$def_max_build.", '', '');\"/> St&uuml;ck<br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_".$darr['def_id']."').value=".$def_max_build.";\">max</a></td>";
			    			      	  }
  			      	  echo "</tr>";
  			      	  echo "<tr>
			    			      	  <th height=\"20\" width=\"110\">".RES_METAL.":</th>
			    			      	  <th height=\"20\" width=\"97\">".RES_CRYSTAL.":</th>
			    			      	  <th height=\"20\" width=\"98\">".RES_PLASTIC.":</th>
			    			      	  <th height=\"20\" width=\"97\">".RES_FUEL.":</th>
			    			      	  <th height=\"20\" width=\"98\">".RES_FOOD."</th></tr>";
  			      	  echo "<tr>
  			      	  				<td class=\"tbldata\" height=\"20\" width=\"110\" ".$ress_style_metal.">
  			      	  					".nf($darr['def_costs_metal'])."
  			      	  				</td>
  			      	  				<td class=\"tbldata\" height=\"20\" width=\"97\" ".$ress_style_crystal.">
  			      	  					".nf($darr['def_costs_crystal'])."
  			      	  				</td>
  			      	  				<td class=\"tbldata\" height=\"20\" width=\"98\" ".$ress_style_plastic.">
  			      	  					".nf($darr['def_costs_plastic'])."
  			      	  				</td>
  			      	  				<td class=\"tbldata\" height=\"20\" width=\"97\" ".$ress_style_fuel.">
  			      	  					".nf($darr['def_costs_fuel'])."
  			      	  				</td>
  			      	  				<td class=\"tbldata\" height=\"20\" width=\"98\" ".$ress_style_food.">
  			      	  					".nf($food_costs)."
  			      	  				</td>
  			      	  			</tr>";
  			      	}
  			      	//Einfache Ansicht der Schiffsliste
  			      	else
  			      	{
			      			$d_img = IMAGE_PATH."/".IMAGE_DEF_DIR."/def".$darr['def_id']."_small.".IMAGE_EXT;
			      			
			      			echo "<tr>
			      							<td class=\"tbldata\">";
  			      							//Bild mit Link zur Hilfe darstellen
			  			      				echo "<a href=\"".HELP_URL."&amp;id=".$darr[ITEM_ID_FLD]."\"><img src=\"".$d_img."\" width=\"40\" height=\"40\" border=\"0\" /></a></td>";
  			      			echo "<th width=\"30%\">
  			      							".$darr['def_name']."<br/>
  			      							<span style=\"font-weight:500;font-size:8pt;\">
  			      							<b>Gebaut:</b> ".nf($darr['deflist_count'])." &nbsp; 
  			      							<b>Felder:</b> ".nf($darr['def_fields'])." / Stück<br/>
  			      						</span></th>
  			      						<td class=\"tbldata\" width=\"13%\">".tf($btime)."</td>
  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_metal.">".nf($darr['def_costs_metal'])."</td>
  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_crystal.">".nf($darr['def_costs_crystal'])."</td>
  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_plastic.">".nf($darr['def_costs_plastic'])."</td>
  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_fuel.">".nf($darr['def_costs_fuel'])."</td>
  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_food.">".nf($food_costs)."</td>";

													//Maximale Anzahl erreicht
			  			      			if ($def_count>=$darr['def_max_count'] && $darr['def_max_count']!=0)
			  			      			{
			  			      			    echo "<td class=\"tbldata\">Max</tr>";
			  			      			}
			  			      			else
			  			      			{
			  			      			    echo "<td class=\"tbldata\"><input type=\"text\" value=\"0\" id=\"build_count_".$darr['def_id']."\" name=\"build_count[".$darr['def_id']."]\" size=\"5\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$def_max_build.", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_".$darr['def_id']."').value=".$def_max_build.";\">max</a></td></tr>";
			  			      			}

  			      	}
  			      	$tabulator++;
								$cnt++;
								$allCnt++;
								
							}
						}

						// Es können keine Anlagen gebaut werden
						if ($cnt==0)
						{
							echo "<tr>
											<td colspan=\"9\" height=\"30\" align=\"center\" class=\"tbldata\">
												Es k&ouml;nnen noch keine Verteidigungsanlagen gebaut werden!<br>
												Baue zuerst die ben&ouml;tigten Geb&auml;ude und erforsche die erforderlichen Technologien!
											</td>
									</tr><br>";
						}
					}
					// Es gibt noch keine Anlagen
					else
					{
						echo "<tr><td align=\"center\" colspan=\"3\" class=\"infomsg\">Es gibt noch keine Verteidigungsanlagen!</td></tr>";
					}

   				tableEnd();
   				
   				//Lücke zwischen Kategorien
   				echo "<br><br><br>";
				}
   			// Baubutton anzeigen
				if ($allCnt>0)
				{
					echo "<input type=\"submit\" class=\"button\" name=\"submit\" value=\"Bauauftr&auml;ge &uuml;bernehmen\"/>";
				}
			}
			else
			{
				echo "<br>Noch keine Kategorien definiert!<br>";
			}
		}
	}
	else
	{
		// Titel
		echo "<h1>Waffenfabrik des Planeten ".$cp->name."</h1>";		
		
		// Ressourcen anzeigen
		$cp->resBox($cu->properties->smallResBox);
		echo "<br>Die Waffenfabrik wurde noch nicht gebaut!<br/><br/>";
		
		echo $def_info_string;
	}
	echo "</form>";
	

?>
