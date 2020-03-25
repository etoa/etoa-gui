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
	* Construct ships
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/

	//Definition für "Info" Link
	define('ITEMS_TBL',"ships");
	define('REQ_TBL',"ship_requirements");
	define('REQ_ITEM_FLD',"req_ship_id");
	define('ITEM_ID_FLD',"ship_id");
	define('ITEM_NAME_FLD',"ship_name");
	define('RACE_TO_ADD'," AND (ship_race_id=0 OR ship_race_id='".$cu->raceId."')");
	define('ITEM_SHOW_FLD',"ship_show");
	define('ITEM_ORDER_FLD',"ship_order");
	define('NO_ITEMS_MSG',"In dieser Kategorie gibt es keine Schiffe!");
	define('HELP_URL',"?page=help&site=shipyard");

	// Absolute minimal Bauzeit in Sekunden
	define("SHIPYARD_MIN_BUILD_TIME", $cfg->get('shipyard_min_build_time'));

	// Ben. Level für Autragsabbruch
	define("SHIPQUEUE_CANCEL_MIN_LEVEL", $cfg->get('shipqueue_cancel_min_level'));

	define("SHIPQUEUE_CANCEL_START", $cfg->get('shipqueue_cancel_start'));

	define("SHIPQUEUE_CANCEL_FACTOR", $cfg->get('shipqueue_cancel_factor'));

	define("SHIPQUEUE_CANCEL_END", $cfg->get('shipqueue_cancel_end'));

    $bl = new BuildList($cp->id,$cu->id);
    $tl = new TechList($cu->id);

	$shipyard = $bl->item(SHIP_BUILDING_ID);


	// BEGIN SKRIPT //

	//Tabulator var setzten (für das fortbewegen des cursors im forumular)
	$tabulator = 1;

  	// Prüfen ob Werft gebaut ist
    if ($shipyard && $shipyard->level)
    {
        define('CURRENT_SHIPYARD_LEVEL', $shipyard->level);

		// Titel
		echo "<h1>Raumschiffswerft (Stufe ".CURRENT_SHIPYARD_LEVEL.") des Planeten ".$cp->name."</h1>";

		// Ressourcen anzeigen
		echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);

		// Prüfen ob dieses Gebäude deaktiviert wurde
		if ($shipyard->deactivated > time())
		{
			iBoxStart("Geb&auml;ude nicht bereit");
			echo "Diese Schiffswerft ist bis ".date("d.m.Y H:i", $shipyard->deactivated)." deaktiviert.";
			iBoxEnd();
		}
		// Werft anzeigen
		else
		{
			/****************************
			*  Sortiereingaben speichern *
			****************************/
			if(count($_POST)>0 && isset($_POST['sort_submit'])
			   && ctype_aldotsc($_POST['sort_value']) && ctype_aldotsc($_POST['sort_way']))
			{
				$cu->properties->itemOrderShip = $_POST['sort_value'];
       			$cu->properties->itemOrderWay = $_POST['sort_way'];
			}


			//
			// Läd alle benötigten Daten in PHP-Arrays
			//Gentechnologie:

			// Vorausetzungen laden
			$req = array();
			$res = dbquery("
			SELECT 
				* 
			FROM 
				ship_requirements;");
			while ($arr = mysql_fetch_assoc($res))
			{
				//Gebäude Vorausetzungen
				if ($arr['req_building_id']>0)
				{
					$req[$arr['obj_id']]['b'][$arr['req_building_id']]=$arr['req_level'];
				}

				//Technologie Voraussetzungen
				if ($arr['req_tech_id']>0)
				{
					$req[$arr['obj_id']]['t'][$arr['req_tech_id']]=$arr['req_level'];
				}
			}

			//Gentechlevel definieren
			$gen_tech_level = $tl->getLevel(GEN_TECH_ID);

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
  			shiplist_user_id='".$cu->id."';");
			while ($arr = mysql_fetch_assoc($res))
			{
				$shiplist[$arr['shiplist_ship_id']][$arr['shiplist_entity_id']]=$arr['shiplist_count'];
				$bunkered[$arr['shiplist_ship_id']][$arr['shiplist_entity_id']]=$arr['shiplist_bunkered'];
			}

			// Bauliste vom aktuellen Planeten laden (wird nach "Abbrechen" nochmals geladen)
			$res = dbquery("
			SELECT
    		queue_id,
    		queue_ship_id,
    		queue_cnt,
    		queue_starttime,
    		queue_endtime,
    		queue_objtime
			FROM
    		ship_queue
			WHERE
  			queue_entity_id='".$cp->id."'
  			AND queue_endtime>'".$time."'
    	ORDER BY
				queue_starttime ASC;");
			while ($arr = mysql_fetch_assoc($res))
			{
				$queue[$arr['queue_id']] = $arr;
			}

			// Bauliste vom allen Planeten laden und nach Schiffe zusammenfassen
			$res = dbquery("
			SELECT
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

			// Flotten laden
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


			// Alle Schiffe laden
			//Schiffsordnung des Users beachten
			$order="ship_".$cu->properties->itemOrderShip." ".$cu->properties->itemOrderWay."";
			$res = dbquery("
			SELECT
				ship_id,
				ship_name,
				ship_cat_id,
				ship_shortcomment,
				ship_costs_metal,
				ship_costs_crystal,
				ship_costs_plastic,
				ship_costs_fuel,
				ship_costs_food,
				ship_show,
				ship_buildable,
				ship_structure,
				ship_shield,
				ship_weapon,
				ship_race_id,
				ship_max_count,
				special_ship,
				cat_name,
				cat_id
			FROM
					ships
				INNER JOIN
					ship_cat
				ON
					ship_cat_id=cat_id
			WHERE
				ship_buildable='1'
				AND (ship_race_id='0' OR ship_race_id='".$cu->raceId."')
			ORDER BY
				cat_order,
				special_ship DESC,
				".$order.";");
			while ($arr = mysql_fetch_assoc($res))
			{
				$cat[$arr['cat_id']] = $arr['cat_name'];
				$arr['ship_costs_metal'] *= $cu->specialist->costsShip;
				$arr['ship_costs_crystal'] *= $cu->specialist->costsShip;
				$arr['ship_costs_plastic'] *= $cu->specialist->costsShip;
				$arr['ship_costs_fuel'] *= $cu->specialist->costsShip;
				$arr['ship_costs_food'] *= $cu->specialist->costsShip;
				$ships[$arr['ship_id']] = $arr;
			}

    	// level zählen welches die schiffswerft über dem angegeben level ist und faktor berechnen
    	$need_bonus_level = CURRENT_SHIPYARD_LEVEL - $conf['build_time_boni_schiffswerft']['p1'];
    	if($need_bonus_level <= 0)
    	{
    		$time_boni_factor=1;
    	}
    	else
    	{
    		$time_boni_factor=1-($need_bonus_level*($conf['build_time_boni_schiffswerft']['v']/100));
    	}
    	$people_working = $bl->getPeopleWorking(SHIP_BUILDING_ID);

    	// Faktor der zurückerstatteten Ressourcen bei einem Abbruch des Auftrags berechnen
    	if (CURRENT_SHIPYARD_LEVEL>=SHIPQUEUE_CANCEL_MIN_LEVEL)
    	{
    		$cancel_res_factor = min(SHIPQUEUE_CANCEL_END,SHIPQUEUE_CANCEL_START+((CURRENT_SHIPYARD_LEVEL-SHIPQUEUE_CANCEL_MIN_LEVEL)*SHIPQUEUE_CANCEL_FACTOR));
    	}
    	else
    	{
    		$cancel_res_factor=0;
    	}

       	// Infos anzeigen
    	tableStart("Werft-Infos");
		echo '<colgroup><col style="width:400px;"/><col/></colgroup>';
		if ($cu->specialist->costsShip!=1)
		{
			echo "<tr><td>Kostenreduktion durch ".$cu->specialist->name.":</td><td>".get_percent_string($cu->specialist->costsShip)."</td></tr>";
		}
		if ($cu->specialist->shipTime!=1)
		{
			echo "<tr><td>Bauzeitverringerung durch ".$cu->specialist->name.":</td><td>".get_percent_string($cu->specialist->shipTime)."</td></tr>";
		}
    	echo "<tr><td>Eingestellte Arbeiter:</td><td>".nf($bl->getPeopleWorking(SHIP_BUILDING_ID));
    	if (!isset($queue) && empty($queue))
		{
			echo '&nbsp;<a href="javascript:;" onclick="toggleBox(\'changePeople\');">[&Auml;ndern]</a>';
		}
		echo "</td></tr>";
    	if ($bl->getPeopleWorking(SHIP_BUILDING_ID) > 0)
		{
			echo '<tr><td>Zeitreduktion durch Arbeiter pro Auftrag:</td><td><span id="people_work_done">'.tf($cfg->value('people_work_done') *$bl->getPeopleWorking(SHIP_BUILDING_ID)).'</span></td></tr>';
			echo '<tr><td>Nahrungsverbrauch durch Arbeiter pro Auftrag:</td><td><span id="people_food_require">'.nf($cfg->value('people_food_require') * $bl->getPeopleWorking(SHIP_BUILDING_ID)).'</span></td></tr>';
		}
		if ($gen_tech_level  > 0)
		{
			echo '<tr><td>Gentechnologie:</td><td>'.$gen_tech_level .'</td></tr>';
			echo '<tr><td>Minimale Bauzeit (mit Arbeiter):</td><td>Bauzeit * '.(0.1-($gen_tech_level/100)).'</td></tr>';
		}
    	echo "<tr><td>Bauzeitverringerung:</td><td>";
    	if ($need_bonus_level>=0)
    	{
    		echo get_percent_string($time_boni_factor)." durch Stufe ".CURRENT_SHIPYARD_LEVEL;
    	}
    	else
    	{
    		echo "Stufe ".$conf['build_time_boni_schiffswerft']['p1']." erforderlich!";
    	}
		echo "</td></tr>";
    	if ($cancel_res_factor>0)
    	{
    		echo "<tr><td>Ressourcenrückgabe bei Abbruch:</td><td>".($cancel_res_factor*100)."% (ohne ".RES_FOOD.", ".(SHIPQUEUE_CANCEL_END*100)."% maximal)</td></tr>";
    		$cancelable = true;
    	}
    	else
    	{
    		echo "<tr><td>Abbruchmöglichkeit:</td><td>Stufe ".SHIPQUEUE_CANCEL_MIN_LEVEL." erforderlich!</td></tr>";
    		$cancelable = false;
    	}
		tableEnd();
	    $peopleFree = floor($cp->people) - $bl->totalPeopleWorking() + $bl->getPeopleWorking(SHIP_BUILDING_ID);
        $box =  '
                    <input type="hidden" name="workDone" id="workDone" value="'.$cfg->value('people_work_done').'" />
                    <input type="hidden" name="foodRequired" id="foodRequired" value="'.$cfg->value('people_food_require').'" />
                    <input type="hidden" name="peopleFree" id="peopleFree" value="'.$peopleFree.'" />
                    <input type="hidden" name="foodAvaiable" id="foodAvaiable" value="'.$cp->getRes1(4).'" />
                    <input type="hidden" name="peopleOptimized" id="peopleOptimized" value="0" />';

        $box .= '   <tr>
                            <th>Eingestellte Arbeiter</th>
                            <td>
                                <input  type="text" 
                                        name="peopleWorking" 
                                        id="peopleWorking" 
                                        value="'.nf($bl->getPeopleWorking(SHIP_BUILDING_ID)).'"
                                        onkeyup="updatePeopleWorkingBox(this.value,\'-1\',\'-1\');"/>
                        </td>
                        </tr>
                        <tr>
                            <th>Zeitreduktion</th>
                            <td><input  type="text"
                                        name="timeReduction"
                                        id="timeReduction"
                                        value="'.tf($cfg->value('people_work_done') * $bl->getPeopleWorking(SHIP_BUILDING_ID)).'"
                                        onkeyup="updatePeopleWorkingBox(\'-1\',this.value,\'-1\');" /></td>
                        </tr>
                            <th>Nahrungsverbrauch</th>
                            <td><input  type="text"
                                        name="foodUsing"
                                        id="foodUsing"
                                        value="'.nf($cfg->value('people_food_require') * $bl->getPeopleWorking(SHIP_BUILDING_ID)).'"
                                        onkeyup="updatePeopleWorkingBox(\'-1\',\'-1\',this.value);" /></td>
                        </tr>
                        <tr>
                            <td colspan="2" style="text-align:center;">
                                <div id="changeWorkingPeopleError" style="display:none;">&nbsp;</div>
                                <input type="submit" value="Speichern" name="submit_people_form" id="submit_people_form" />&nbsp;';
        echo '<div id="changePeople" style="display:none;">';
    	tableStart("Arbeiter der Raumschiffswerft zuteilen");
        echo '<form id="changeWorkingPeople" method="post" action="?page='.$page.'">
            '.$box.'</form>';
        tableEnd();
        echo '</div>';

        // people working changed
        if (isset($_POST['submit_people_form']))
        {
            if (!isset($queue) && empty($queue)) {
				dbquery("
                        UPDATE
                            buildlist
                        SET
                            buildlist_people_working='".nf_back($_POST['peopleWorking']). "'
                        WHERE
                            buildlist_building_id='".SHIP_BUILDING_ID."'
                        AND buildlist_entity_id=" . $cp->id);
				//success_msg("Arbeiter zugeteilt!");
			}
			else
                error_msg('Arbeiter konnten nicht zugeteilt werden!');
            header("Refresh:0");
        }


	/*************
	* Sortierbox *
	*************/

		echo "<form action=\"?page=$page\" method=\"post\">";
		iBoxStart("Filter");

			//Legt Sortierwerte in einem Array fest
			$values = array(
											"name"=>"Name",
											"points"=>"Kosten",
											"weapon"=>"Waffen",
											"structure"=>"Struktur",
											"shield"=>"Schild",
											"speed"=>"Geschwindigkeit",
											"time2start"=>"Startzeit",
											"time2land"=>"Landezeit",
											"capacity"=>"Kapazität",
											"costs_metal"=>"Titan",
											"costs_crystal"=>"Silizium",
											"costs_plastic"=>"PVC",
											"costs_fuel"=>"Tritium"
											);

			echo "<div style=\"text-align:center;\">
								<select name=\"sort_value\">";
								foreach ($values as $value => $name)
								{
									echo "<option value=\"".$value."\"";
									if($cu->properties->itemOrderShip==$value)
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
							</div>";
			iBoxEnd();
			echo "</form>";

			echo "<form action=\"?page=".$page."\" method=\"post\" style=\"clear:both;\">";


	/****************************
	*  Schiffe in Auftrag geben *
	****************************/

			if(count($_POST)>0 && isset($_POST['submit']) && checker_verify())
			{
				tableStart();
				echo "<tr><th>Ergebnisse des Bauauftrags</th></tr>";

				//Log variablen setzten
				$log_ships="";
				$total_duration=0;
				$total_metal=0;
				$total_crystal=0;
				$total_plastic=0;
				$total_fuel=0;
				$total_food=0;

				// Endzeit bereits laufender Aufträge laden
				$end_time=time();
				if(isset($queue))
				{
					// Speichert die letzte Endzeit, da das Array $queue nach queue_starttime (und somit auch endtime) sortiert ist
					foreach ($queue as $data)
					{
						$end_time = $data['queue_endtime'];
					}
				}

				//
				// Bauaufträge speichern
				//
				$counter=0;
				foreach ($_POST['build_count'] as $ship_id => $build_cnt)
				{
				  $ship_id = intval($ship_id);

					$build_cnt=nf_back($build_cnt);

					if ($build_cnt>0 && isset($ships[$ship_id]))
					{
						$buildCountOriginal = $build_cnt;

			      // Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
			      $ship_count = 0;
			      // ... auf den Planeten
			      if(isset($shiplist[$ship_id]))
			      {
			      	$ship_count += array_sum($shiplist[$ship_id]);
			      }
			      // ... im Bunker
			      if(isset($bunkered[$ship_id]))
			      {
			      	$ship_count += array_sum($bunkered[$ship_id]);
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

						//Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
						if ($build_cnt + $ship_count > $ships[$ship_id]['ship_max_count'] && $ships[$ship_id]['ship_max_count']!=0)
						{
							$build_cnt=max(0,$ships[$ship_id]['ship_max_count']-$ship_count);
						}

    				// TODO: Überprüfen
						//Wenn der User nicht genug Ress hat, die Anzahl Schiffe drosseln
						//Titan
						if ($ships[$ship_id]['ship_costs_metal']>0)
						{
							$bf['metal']=$cp->resMetal/$ships[$ship_id]['ship_costs_metal'];
						}
						else
						{
							$bc['metal']=0;
						}
						//Silizium
						if ($ships[$ship_id]['ship_costs_crystal']>0)
						{
							$bf['crystal']=$cp->resCrystal/$ships[$ship_id]['ship_costs_crystal'];
						}
						else
						{
							$bc['crystal']=0;
						}
						//PVC
						if ($ships[$ship_id]['ship_costs_plastic']>0)
						{
							$bf['plastic']=$cp->resPlastic/$ships[$ship_id]['ship_costs_plastic'];
						}
						else
						{
							$bc['plastic']=0;
						}
						//Tritium
						if ($ships[$ship_id]['ship_costs_fuel']>0)
						{
							$bf['fuel']=$cp->resFuel/$ships[$ship_id]['ship_costs_fuel'];
						}
						else
						{
							$bc['fuel']=0;
						}
						//Nahrung
						if (intval($_POST['additional_food_costs'])>0 || $ships[$ship_id]['ship_costs_food']>0)
						{
							 $bf['food']=$cp->resFood/(intval($_POST['additional_food_costs'])+$ships[$ship_id]['ship_costs_food']);
						}
						else
						{
							$bc['food']=0;
						}

						//Anzahl Drosseln ???
						if ($build_cnt>floor(min($bf)))
						{
							$build_cnt=floor(min($bf));
						}

						//Check for Rene-Bug
						$additional_food_costs = $people_working*$cfg->value('people_food_require');
						if ($additional_food_costs!=intval($_POST['additional_food_costs']) || intval($_POST['additional_food_costs'])<0)
						{
							$build_cnt=0;
						}

						//Anzahl muss grösser als 0 sein
						if ($build_cnt>0)
						{
							//Errechne Kosten pro auftrag schiffe
							$bc['metal']=$ships[$ship_id]['ship_costs_metal']*$build_cnt;
							$bc['crystal']=$ships[$ship_id]['ship_costs_crystal']*$build_cnt;
							$bc['plastic']=$ships[$ship_id]['ship_costs_plastic']*$build_cnt;
							$bc['fuel']=$ships[$ship_id]['ship_costs_fuel']*$build_cnt;
							$bc['food']=(intval($_POST['additional_food_costs'])+$ships[$ship_id]['ship_costs_food'])*$build_cnt;

                	        //Berechnete Ress provisorisch abziehen
                	        $cp->resMetal-=$bc['metal'];
                	        $cp->resCrystal-=$bc['crystal'];
                	        $cp->resPlastic-=$bc['plastic'];
                	        $cp->resFuel-=$bc['fuel'];
                	        $cp->resFood-=$bc['food'];

							// Bauzeit pro Schiff berechnen
							$btime = ($ships[$ship_id]['ship_costs_metal']
							+ $ships[$ship_id]['ship_costs_crystal']
							+ $ships[$ship_id]['ship_costs_plastic']
							+ $ships[$ship_id]['ship_costs_fuel']
							+ $ships[$ship_id]['ship_costs_food'])
							/ GLOBAL_TIME * SHIP_BUILD_TIME
							* $time_boni_factor
							* $cu->specialist->shipTime;

	    				    // TODO: Überprüfen
							//Rechnet zeit wenn arbeiter eingeteilt sind
							$btime_min=$btime*(0.1-($gen_tech_level/100));
						 	if ($btime_min<SHIPYARD_MIN_BUILD_TIME) $btime_min=SHIPYARD_MIN_BUILD_TIME;
							$btime=ceil($btime-$people_working*$cfg->value('people_work_done'));
							if ($btime<$btime_min) $btime=$btime_min;
							$obj_time=$btime;

							// Gesamte Bauzeit berechnen
							$duration=$build_cnt*$obj_time;

							// Setzt Starzeit des Auftrages, direkt nach dem letzten Auftrag
							$start_time = $end_time;
							$end_time = $start_time + $duration;

							// Auftrag speichern
                	        dbquery("
                	        INSERT INTO
                	        ship_queue
                	            (queue_user_id,
                	            queue_ship_id,
                	            queue_entity_id,
                	            queue_cnt,
                	            queue_starttime,
                	            queue_endtime,
                	            queue_objtime)
                	        VALUES
                	            ('".$cu->id."',
                	            '".$ship_id."',
                	            '".$cp->id."',
                	            '".$build_cnt."',
                	            '".$start_time."',
                	            '".$end_time."',
                	            '".$obj_time."');");
                	        $shiplist_id = mysql_insert_id();

							dbquery("
								UPDATE
									buildlist
								SET
									buildlist_people_working_status='1'
								WHERE
									buildlist_building_id='" . SHIPYARD_ID . "'
									AND buildlist_user_id='" . $cu->id . "'
									AND buildlist_entity_id='" . $cp->id . "'");

							// Queue Array aktualisieren
							$queue[$shiplist_id]['queue_id'] = $shiplist_id;
							$queue[$shiplist_id]['queue_ship_id'] = $ship_id;
							$queue[$shiplist_id]['queue_cnt'] = $build_cnt;
							$queue[$shiplist_id]['queue_starttime'] = $start_time;
							$queue[$shiplist_id]['queue_endtime'] = $end_time;
							$queue[$shiplist_id]['queue_objtime'] = $obj_time;


							echo "<tr><td>".nf($build_cnt)." ".$ships[$ship_id]['ship_name']." in Auftrag gegeben!</td></tr>";

							//Log schreiben
							$log_text = "[b]Schiffsauftrag Bauen[/b]

                            [b]Start:[/b] ".date("d.m.Y H:i:s",$end_time)."
                            [b]Ende:[/b] ".date("d.m.Y H:i:s",$end_time)."
                            [b]Dauer:[/b] ".tf($duration)."
                            [b]Dauer pro Einheit:[/b] ".tf($obj_time)."
                            [b]Schiffswerft Level:[/b] ".CURRENT_SHIPYARD_LEVEL."
                            [b]Eingesetzte Bewohner:[/b] ".nf($people_working)."
                            [b]Gen-Tech Level:[/b] ".$gen_tech_level."
                            [b]Eingesetzter Spezialist:[/b] ".$cu->specialist->name."

                            [b]Kosten[/b]
                            [b]".RES_METAL.":[/b] ".nf($bc['metal'])."
                            [b]".RES_CRYSTAL.":[/b] ".nf($bc['crystal'])."
                            [b]".RES_PLASTIC.":[/b] ".nf($bc['plastic'])."
                            [b]".RES_FUEL.":[/b] ".nf($bc['fuel'])."
                            [b]".RES_FOOD.":[/b] ".nf($bc['food'])."

                            [b]Rohstoffe auf dem Planeten[/b]
                            [b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
                            [b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
                            [b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
                            [b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
                            [b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";

							GameLog::add(GameLog::F_SHIP, GameLog::INFO,$log_text,$cu->id,$cu->allianceId,$cp->id, $ship_id, 1, $build_cnt);

							//Rohstoffe summieren, diese werden nach der Schleife abgezogen
							$total_metal+=$bc['metal'];
							$total_crystal+=$bc['crystal'];
							$total_plastic+=$bc['plastic'];
							$total_fuel+=$bc['fuel'];
							$total_food+=$bc['food'];

							//Daten für Log speichern
							$log_ships.="<b>".$ships[$ship_id]['ship_name']."</b>: ".nf($build_cnt)." (".tf($duration).")<br>";
							$total_duration+=$duration;
						}
						else
						{
							echo "<tr><td>".$ships[$ship_id]['ship_name'].": Zu wenig Rohstoffe für diese Anzahl ($buildCountOriginal)!</td></tr>";
						}
						$counter++;
					}
				}

				// Die Rohstoffe der $c-variablen wieder beigeben, da sie sonst doppelt abgezogen werden
                $cp->resMetal+=$total_metal;
                $cp->resCrystal+=$total_crystal;
                $cp->resPlastic+=$total_plastic;
                $cp->resFuel+=$total_fuel;
                $cp->resFood+=$total_food;

				//Rohstoffe vom Planeten abziehen und aktualisieren
				$cp->changeRes(-$total_metal,-$total_crystal,-$total_plastic,-$total_fuel,-$total_food);

				if ($counter==0)
				{
					echo "<tr><td>Keine Schiffe gew&auml;hlt!</td></tr>";
				}
				tableEnd();
                header("Refresh:0");
			}


			checker_init();

        	/*********************
        	* Auftrag abbrechen  *
        	*********************/
			if (isset($_GET['cancel']) && intval($_GET['cancel'])>0 && $cancelable)
			{
				$id = intval($_GET['cancel']);
				if (isset($queue[$id]))
				{

					//Zu erhaltende Rohstoffe errechnen
					$obj_cnt = min(ceil(($queue[$id]['queue_endtime']-max($time,$queue[$id]['queue_starttime']))/$queue[$id]['queue_objtime']),$queue[$id]['queue_cnt']);
					echo "Breche den Bau von ".$obj_cnt." ".$ships[$queue[$id]['queue_ship_id']]['ship_name']." ab...<br/>";

					$ret['metal']=$ships[$queue[$id]['queue_ship_id']]['ship_costs_metal']*$obj_cnt*$cancel_res_factor;
					$ret['crystal']=$ships[$queue[$id]['queue_ship_id']]['ship_costs_crystal']*$obj_cnt*$cancel_res_factor;
					$ret['plastic']=$ships[$queue[$id]['queue_ship_id']]['ship_costs_plastic']*$obj_cnt*$cancel_res_factor;
					$ret['fuel']=$ships[$queue[$id]['queue_ship_id']]['ship_costs_fuel']*$obj_cnt*$cancel_res_factor;
					$ret['food']=$ships[$queue[$id]['queue_ship_id']]['ship_costs_food']*$obj_cnt*$cancel_res_factor;

					// Daten für Log speichern
					$ship_name = $ships[$queue[$id]['queue_ship_id']]['ship_name'];
					$ship_id = $queue[$id]['queue_ship_id'];
					$queue_count = $queue[$id]['queue_cnt'];
					$queue_objtime = $queue[$id]['queue_objtime'];
					$start_time = $queue[$id]['queue_starttime'];
					$end_time = $queue[$id]['queue_endtime'];


					//Auftrag löschen
					dbquery("
					DELETE FROM
					 ship_queue
					WHERE
						queue_id='".$id."';");

					dbquery("
					UPDATE
						buildlist
					SET
						buildlist_people_working_status='0'
					WHERE
						buildlist_building_id='" . SHIPYARD_ID . "'
						AND buildlist_user_id='" . $cu->id . "'
						AND buildlist_entity_id='" . $cp->id . "'");

					// Nachkommende Aufträge werden Zeitlich nach vorne verschoben
					$tres=dbquery("
					SELECT
						queue_id,
		    		queue_ship_id,
		    		queue_cnt,
		    		queue_starttime,
		    		queue_endtime,
		    		queue_objtime
					FROM
						ship_queue
					WHERE
						queue_starttime>='".$end_time."'
						AND queue_entity_id='".$cp->id."'
					ORDER BY
						queue_starttime ASC
					;");
					if (mysql_num_rows($tres)>0)
					{
						$new_starttime=max($start_time,time());
						while ($tarr=mysql_fetch_assoc($tres))
						{
							$new_endtime = $new_starttime + $tarr['queue_endtime'] - $tarr['queue_starttime'];
							dbquery("
							UPDATE
								ship_queue
							SET
								queue_starttime='".$new_starttime."',
								queue_endtime='".$new_endtime."'
							WHERE
								queue_id='".$tarr['queue_id']."'
							");

							// Aktualisiert das Queue-Array
							$queue[$tarr['queue_id']]['queue_starttime'] = $new_starttime;
							$queue[$tarr['queue_id']]['queue_endtime'] = $new_endtime;

							$new_starttime=$new_endtime;
						}
					}

					// Auftrag aus Array löschen
					$queue[$id] = NULL;

					//Rohstoffe dem Planeten gutschreiben und aktualisieren
					$cp->changeRes($ret['metal'],$ret['crystal'],$ret['plastic'],$ret['fuel'],$ret['food']);

					echo "Der Auftrag wurde abgebrochen!<br/><br/>";

					//Log schreiben
					$log_text = "[b]Schiffsauftrag Abbruch[/b]

                    [b]Auftragsdauer:[/b] ".tf($queue_objtime*$queue_count)."

                    [b]Erhaltene Rohstoffe[/b]
                    [b]Faktor:[/b] ".$cancel_res_factor."
                    [b]".RES_METAL.":[/b] ".nf($ret['metal'])."
                    [b]".RES_CRYSTAL.":[/b] ".nf($ret['crystal'])."
                    [b]".RES_PLASTIC.":[/b] ".nf($ret['plastic'])."
                    [b]".RES_FUEL.":[/b] ".nf($ret['fuel'])."
                    [b]".RES_FOOD.":[/b] ".nf($ret['food'])."

                    [b]Rohstoffe auf dem Planeten[/b]
                    [b]".RES_METAL.":[/b] ".nf($cp->resMetal)."
                    [b]".RES_CRYSTAL.":[/b] ".nf($cp->resCrystal)."
                    [b]".RES_PLASTIC.":[/b] ".nf($cp->resPlastic)."
                    [b]".RES_FUEL.":[/b] ".nf($cp->resFuel)."
                    [b]".RES_FOOD.":[/b] ".nf($cp->resFood)."";

					//Log Speichern
					GameLog::add(GameLog::F_SHIP, GameLog::INFO,$log_text,$cu->id,$cu->allianceId,$cp->id, $ship_id, 0, $queue_count);
				    header("Refresh:0");
                }

			}


        	/*********************************
        	* Liste der Bauaufträge anzeigen *
        	*********************************/
			if(isset($queue) && !empty($queue))
			{
				tableStart("Bauliste");
				$first=true;
				$absolut_starttime=0;
				foreach ($queue as $data)
				{
					// Listet nur Die Datensätze aus, die auch eine Schiffs ID beinhalten, da ev. der Datensatz mit NULL gleichgesetzt wurde
					if(isset($data['queue_ship_id']))
					{
						if ($first)
						{
							$obj_t_remaining = ((($data['queue_endtime']-$time) / $data['queue_objtime'])-floor(($data['queue_endtime']-$time) / $data['queue_objtime']))*$data['queue_objtime'];
							if ($obj_t_remaining==0)
							{
								$obj_t_remaining = $data['queue_objtime'];
							}
							$obj_time = $data['queue_objtime'];

							$absolute_starttime=$data['queue_starttime'];

							$obj_t_passed = $data['queue_objtime']-$obj_t_remaining;
							echo "<tr>
									<th colspan=\"2\">Aktuell</th>
									<th style=\"width:150px;\">Start</th>
									<th style=\"width:150px;\">Ende</th>
									<th style=\"width:80px;\" colspan=\"2\">Verbleibend</th>
								</tr>";
							echo "<tr>";
							echo "<td colspan=\"2\">".$ships[$data['queue_ship_id']]['ship_name']."</td>";
							echo "<td>".df(time()-$obj_t_passed,1)."</td>";
							echo "<td>".df(time()+$obj_t_remaining,1)."</td>";
							echo "<td colspan=\"2\">".tf($obj_t_remaining)."</td>
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
						echo "<td id=\"objcount\">".$data['queue_cnt']."</td>";
						echo "<td>".$ships[$data['queue_ship_id']]['ship_name']."</td>";
						echo "<td>".df($absolute_starttime,1)."</td>";
						echo "<td>".df($absolute_starttime+$data['queue_endtime']-$data['queue_starttime'],1)."</td>";
						echo "<td>".tf($data['queue_endtime']-time(),1)."</td>";
						echo "<td id=\"cancel\">";
						if ($cancelable)
						{
							echo "<a href=\"?page=$page&amp;cancel=".$data['queue_id']."\" onclick=\"return confirm('Soll dieser Auftrag wirklich abgebrochen werden?');\">Abbrechen</a>";
						}
						else
						{
							echo "-";
						}
						echo "</td>
						</tr>";

						//Setzt die Startzeit des nächsten Schiffes, auf die Endzeit des jetztigen Schiffes
						$absolute_starttime=$data['queue_endtime'];
					}
				}
				tableEnd();
			}



	/***********************
	* Schiffe auflisten    *
	***********************/

			$cnt = 0;
			if (isset($cat))
			{
				foreach ($cat as $cat_id => $cat_name)
				{
					tableStart($cat_name);
					$ccnt = 0;

					// Auflistung der Schiffe (auch diese, die noch nicht gebaut wurden)
					if (isset($ships))
					{
						//Einfache Ansicht
						if ($cu->properties->itemShow!='full')
						{
							echo '<tr>
											<th colspan="2" class="tbltitle">Schiff</th>
											<th class="tbltitle">Zeit</th>
											<th class="tbltitle">'.RES_METAL.'</th>
											<th class="tbltitle">'.RES_CRYSTAL.'</th>
											<th class="tbltitle">'.RES_PLASTIC.'</th>
											<th class="tbltitle">'.RES_FUEL.'</th>
											<th class="tbltitle">'.RES_FOOD.'</th>
											<th class="tbltitle">Anzahl</th>
										</tr>';
						}

						foreach ($ships as $data)
						{
							// Prüfen ob Schiff gebaut werden kann
							$build_ship = 1;
							// Gebäude prüfen
							if (isset($req[$data['ship_id']]['b']) && count($req[$data['ship_id']]['b']) > 0)
							{
								foreach ($req[$data['ship_id']]['b'] as $id=>$level)
								{
									if ($bl->getLevel($id) < $level)
									{
										$build_ship = 0;
									}
								}
							}
							// Technologien prüfen
							if (isset($req[$data['ship_id']]['t']) && count($req[$data['ship_id']]['t'])>0)
							{
								foreach ($req[$data['ship_id']]['t'] as $id=>$level)
								{
									if ($tl->getLevel($id) < $level)
									{
										$build_ship = 0;
									}
								}
							}

							// Schiffdatensatz zeigen wenn die Voraussetzungen erfüllt sind und das Schiff in diese Kategorie gehört
							if ($build_ship == 1 && $data['ship_cat_id'] == $cat_id)
							{
								// Zählt die Anzahl Schiffe dieses Typs im ganzen Account...
								$ship_count = 0;
    			      			// ... auf den Planeten
    			      			if(isset($shiplist[$data['ship_id']]))
    			      			{
    			      				$ship_count += array_sum($shiplist[$data['ship_id']]);
    			      			}
    			      			// ... im Bunker
    			      			if(isset($bunkered[$data['ship_id']]))
    			      			{
    			      				$ship_count += array_sum($bunkered[$data['ship_id']]);
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

								// Bauzeit berechnen
								$btime = ($data['ship_costs_metal'] + $data['ship_costs_crystal'] + $data['ship_costs_plastic'] + $data['ship_costs_fuel'] + $data['ship_costs_food']) / GLOBAL_TIME * SHIP_BUILD_TIME * $time_boni_factor * $cu->specialist->shipTime;
								$btime_min = $btime * (0.1 - ($gen_tech_level / 100));
								$peopleOptimized= ceil(($btime-$btime_min)/$cfg->value('people_work_done'));

								//Mindest Bauzeit
    			      			if ($btime_min < SHIPYARD_MIN_BUILD_TIME)
								{
									$btime_min = SHIPYARD_MIN_BUILD_TIME;
								}

								$btime = ceil($btime - $people_working * $cfg->value('people_work_done'));
								if ($btime < $btime_min)
								{
									$btime = $btime_min;
								}

								//Nahrungskosten berechnen
								$food_costs = $people_working * $cfg->value('people_food_require');

								//Nahrungskosten versteckt übermitteln
								echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"".$food_costs."\" />";
								$food_costs += $data['ship_costs_food'];



								//Errechnet wie viele Schiffe von diesem Typ maximal Gebaut werden können mit den aktuellen Rohstoffen

								//Titan
								if($data['ship_costs_metal'] > 0)
								{
									$build_cnt_metal = floor($cp->resMetal / $data['ship_costs_metal']);
								}
								else
								{
									$build_cnt_metal = 99999999999;
								}

								//Silizium
								if($data['ship_costs_crystal'] > 0)
								{
									$build_cnt_crystal = floor($cp->resCrystal/$data['ship_costs_crystal']);
								}
								else
								{
									$build_cnt_crystal = 99999999999;
								}

								//PVC
								if($data['ship_costs_plastic'] > 0)
								{
									$build_cnt_plastic = floor($cp->resPlastic / $data['ship_costs_plastic']);
								}
								else
								{
									$build_cnt_plastic = 99999999999;
								}

								//Tritium
								if($data['ship_costs_fuel'] > 0)
								{
									$build_cnt_fuel = floor($cp->resFuel / $data['ship_costs_fuel']);
								}
								else
								{
									$build_cnt_fuel = 99999999999;
								}

								//Nahrung
								if($food_costs > 0)
								{
									$build_cnt_food = floor($cp->resFood / $food_costs);
								}
								else
								{
									$build_cnt_food = 99999999999;
								}

								//Begrente Anzahl baubar
								if($data['ship_max_count'] != 0)
								{
									$max_cnt = $data['ship_max_count'] - $ship_count;
								}
								else
								{
									$max_cnt = 99999999999;
								}

								//Effetiv max. baubare Schiffe in Betrachtung der Rohstoffe und des Baumaximums
								$ship_max_build = min($build_cnt_metal,$build_cnt_crystal,$build_cnt_plastic,$build_cnt_fuel,$build_cnt_food,$max_cnt);

								//Tippbox Nachricht generieren
								//X Schiffe baubar
								if($ship_max_build > 0)
								{
									$tm_cnt="Es k&ouml;nnen maximal ".nf($ship_max_build)." Schiffe gebaut werden.";
								}
								//Zuwenig Rohstoffe. Wartezeit errechnen
								elseif($ship_max_build == 0)
								{
									//Wartezeit Titan
									if ($cp->prodMetal > 0)
									{
										$bwait['metal'] = ceil(($data['ship_costs_metal'] - $cp->resMetal) / $cp->prodMetal * 3600);
										$bwmsg['metal'] = tm("Fehlender Rohstoff",nf($data['ship_costs_metal']-$cp->resMetal)." Titan<br />Bereit in ".tf($bwait['metal'])."");
									}
									else
									{
										$bwait['metal'] = 0;
										$bwmsg['metal'] = '';
									}

									//Wartezeit Silizium
									if ($cp->prodCrystal > 0)
									{
										$bwait['crystal'] = ceil(($data['ship_costs_crystal'] - $cp->resCrystal) / $cp->prodCrystal * 3600);
										$bwmsg['crystal'] = tm("Fehlender Rohstoff",nf($data['ship_costs_crystal']-$cp->resCrystal)." Silizium<br />Bereit in ".tf($bwait['crystal'])."");
									}
									else
									{
										$bwait['crystal'] = 0;
										$bwmsg['crystal'] = '';
									}

									//Wartezeit PVC
									if ($cp->prodPlastic > 0)
									{
										$bwait['plastic'] = ceil(($data['ship_costs_plastic'] - $cp->resPlastic) / $cp->prodPlastic * 3600);
										$bwmsg['plastic'] = tm("Fehlender Rohstoff",nf($data['ship_costs_plastic']-$cp->resPlastic)." PVC<br />Bereit in ".tf($bwait['plastic'])."");
									}
									else
									{
										$bwait['plastic'] = 0;
										$bwmsg['plastic'] = '';
									}

									//Wartezeit Tritium
									if ($cp->prodFuel > 0)
									{
										$bwait['fuel'] = ceil(($data['ship_costs_fuel'] - $cp->resFuel) / $cp->prodFuel * 3600);
										$bwmsg['fuel'] = tm("Fehlender Rohstoff",nf($data['ship_costs_fuel']-$cp->resFuel)." Tritium<br />Bereit in ".tf($bwait['fuel'])."");
									}
									else
									{
										$bwait['fuel'] = 0;
										$bwmsg['fuel'] = '';
									}

									//Wartezeit Nahrung
									if ($cp->prodFood > 0)
									{
										$bwait['food'] = ceil(($food_costs - $cp->resFood) / $cp->prodFood * 3600);
										$bwmsg['food'] = tm("Fehlender Rohstoff",nf($food_costs-$cp->resFood)." Nahrung<br />Bereit in ".tf($bwait['food'])."");
									}
									else
									{
										$bwait['food'] = 0;
										$bwmsg['food'] = '';
									}

									//Maximale Wartezeit ermitteln
									$bwmax = max($bwait['metal'],$bwait['crystal'],$bwait['plastic'],$bwait['fuel'],$bwait['food']);

									$tm_cnt="Rohstoffe verf&uuml;gbar in ".tf($bwmax)."";
								}
								else
								{
									$tm_cnt="";
								}

								//Stellt Rohstoff Rot dar, wenn es von diesem zu wenig auf dem Planeten hat
								//Titan
								if($data['ship_costs_metal']>$cp->resMetal)
								{
									$ress_style_metal="style=\"color:red;\" ".$bwmsg['metal']."";
								}
								else
								{
									$ress_style_metal="";
								}

								//Silizium
								if($data['ship_costs_crystal']>$cp->resCrystal)
								{
									$ress_style_crystal="style=\"color:red;\" ".$bwmsg['crystal']."";
								}
								else
								{
									$ress_style_crystal="";
								}

								//PVC
								if($data['ship_costs_plastic']>$cp->resPlastic)
								{
									$ress_style_plastic="style=\"color:red;\" ".$bwmsg['plastic']."";
								}
								else
								{
									$ress_style_plastic="";
								}

								//Tritium
								if($data['ship_costs_fuel']>$cp->resFuel)
								{
									$ress_style_fuel="style=\"color:red;\" ".$bwmsg['fuel']."";
								}
								else
								{
									$ress_style_fuel="";
								}

								//Nahrung
								if($food_costs>$cp->resFood)
								{
									$ress_style_food="style=\"color:red;\" ".$bwmsg['food']."";
								}
								else
								{
									$ress_style_food="";
								}

								// Sicherstellen dass epische Spezialschiffe nur auf dem Hauptplanet gebaut werden
								if ($data['special_ship']==0 || $cp->isMain)
								{
 			      				// Speichert die Anzahl gebauter Schiffe in eine Variable
 			      				if(isset($shiplist[$data['ship_id']][$cp->id]))
 			      				{
 			      					$shiplist_count = $shiplist[$data['ship_id']][$cp->id];
 			      				}
 			      				else
 			      				{
 			      					$shiplist_count = 0;
 			      				}

								// Volle Ansicht
    			      			if($cu->properties->itemShow=='full')
    			      			{
    			      				if ($ccnt>0)
    			      				{
    			      					echo "<tr>
    			      									<td colspan=\"5\" style=\"height:5px;\"></td>
    			      							</tr>";
    			      				}
    			      	  		 	$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$data['ship_id']."_middle.".IMAGE_EXT;

    			      	  		 	echo "<tr>
    			      	  		 			<th colspan=\"5\" height=\"20\">".$data['ship_name']."</th>
    			      	  		 		</tr>
    			      	  		 		<tr>
    			      	  		 			<td width=\"120\" height=\"120\" rowspan=\"3\">";

								 	//Bei Spezialschiffen nur Bild ohne Link darstellen
								 	if ($data['special_ship']==1)
								 	{
								 		echo "<img src=\"".$s_img."\" width=\"120\" height=\"120\" border=\"0\" />";
								 	}
								 	//Bei normalen Schiffen mit Hilfe verlinken
								 	else
								 	{
								 		echo "<a href=\"".HELP_URL."&amp;id=".$data[ITEM_ID_FLD]."\" title=\"Info zu diesem Schiff anzeigen\">
				    			   	  	<img src=\"".$s_img."\" width=\"120\" height=\"120\" border=\"0\" /></a>";
								 	}
    			      	  		 	echo "</td>
								 			<td colspan=\"4\" valign=\"top\">".$data['ship_shortcomment']."</td>
    			      	  		 		</tr>
								 		<tr>
								 			<th  height=\"30\">Vorhanden:</th>
								 			<td colspan=\"3\">".nf($shiplist_count)."</td>
								 		</tr>
								 		<tr>
								 			<th height=\"30\">Bauzeit</th>
    			      	  		 			<td>".tf($btime)."</td>";

								 	//Maximale Anzahl erreicht
								 	if ($ship_count>=$data['ship_max_count'] && $data['ship_max_count']!=0)
								 	{
								 		echo "<th height=\"30\" colspan=\"2\"><i>Maximalanzahl erreicht</i></th>";
								 	}
								 	else
								 	{


								 		echo "<th height=\"30\">In Aufrag geben:</th>
				    			   	      			<td><input type=\"text\" value=\"0\" name=\"build_count[".$data['ship_id']."]\" id=\"build_count_".$data['ship_id']."\" size=\"4\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$ship_max_build.", '', '');\"/> St&uuml;ck<br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_".$data['ship_id']."').value=".$ship_max_build.";\">max</a>";
                                                    if (!isset($queue) && empty($queue))
													{
														echo '&nbsp;<a href="#changePeople" onclick="javascript:if(document.getElementById(\'changePeople\').style.display==\'none\') {toggleBox(\'changePeople\')};updatePeopleWorkingBox(\''.$peopleOptimized.'\',\'-1\',\'^-1\');">optimieren</a>';
													}


                                        echo"</td>";
								 	}
								 	echo "</tr>";
								 	echo "<tr>
				    			   	  <th height=\"20\" width=\"110\">".RES_METAL.":</th>
				    			   	  <th height=\"20\" width=\"97\">".RES_CRYSTAL.":</th>
				    			   	  <th height=\"20\" width=\"98\">".RES_PLASTIC.":</th>
				    			   	  <th height=\"20\" width=\"97\">".RES_FUEL.":</th>
				    			   	  <th height=\"20\" width=\"98\">".RES_FOOD."</th></tr>";
								 	echo "<tr>
    			      	  		 		<td height=\"20\" width=\"110\" ".$ress_style_metal.">
    			      	  		 			".nf($data['ship_costs_metal'])."
    			      	  		 		</td>
    			      	  		 		<td height=\"20\" width=\"25%\" ".$ress_style_crystal.">
    			      	  		 			".nf($data['ship_costs_crystal'])."
								 		</td>
								 		<td height=\"20\" width=\"25%\" ".$ress_style_plastic.">
    			      	  		 			".nf($data['ship_costs_plastic'])."
    			      	  		 		</td>
    			      	  		 		<td height=\"20\" width=\"25%\" ".$ress_style_fuel.">
    			      	  		 			".nf($data['ship_costs_fuel'])."
    			      	  		 		</td>
    			      	  		 		<td height=\"20\" width=\"25%\" ".$ress_style_food.">
    			      	  		 			".nf($food_costs)."
    			      	  		 		</td>
    			      	  		 	</tr>";
								}
								//Einfache Ansicht der Schiffsliste
								else
								{
									$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$data['ship_id']."_small.".IMAGE_EXT;

									echo "<tr>
  			      							<td>";

	  			      				//Spezialschiffe ohne Link darstellen
				  			      	if ($data['special_ship']==1)
									{
				  			      		echo "<img src=\"$s_img\" width=\"40\" height=\"40\" border=\"0\" /></td>";
				  			      	}
				  			      	//Normale Schiffe mit Link zur Hilfe darstellen
				  			      	else
				  			      	{
				  			      		echo "<a href=\"".HELP_URL."&amp;id=".$data[ITEM_ID_FLD]."\"><img src=\"".$s_img."\" width=\"40\" height=\"40\" border=\"0\" /></a></td>";
				  			      	}

	  			      				echo "<th width=\"30%\">
	  			      							<span style=\"font-weight:500\">".$data['ship_name']."<br/>
	  			      							Gebaut:</span> ".nf($shiplist_count)."
	  			      						</th>
	  			      						<td width=\"13%\">".tf($btime)."</td>
	  			      						<td width=\"10%\" ".$ress_style_metal.">".nf($data['ship_costs_metal'])."</td>
	  			      						<td width=\"10%\" ".$ress_style_crystal.">".nf($data['ship_costs_crystal'])."</td>
	  			      						<td width=\"10%\" ".$ress_style_plastic.">".nf($data['ship_costs_plastic'])."</td>
	  			      						<td width=\"10%\" ".$ress_style_fuel.">".nf($data['ship_costs_fuel'])."</td>
	  			      						<td width=\"10%\" ".$ress_style_food.">".nf($food_costs)."</td>";

														//Maximale Anzahl erreicht
				  			      			if ($ship_count>=$data['ship_max_count'] && $data['ship_max_count']!=0)
				  			      			{
				  			      			    echo "<td>Max</td></tr>";
				  			      			}
				  			      			else
				  			      			{
				  			      			    echo "<td><input type=\"text\" value=\"0\" id=\"build_count_".$data['ship_id']."\" name=\"build_count[".$data['ship_id']."]\" size=\"5\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$ship_max_build.", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_".$data['ship_id']."').value=".$ship_max_build.";\">max</a></td></tr>";
				  			      			}

    			      	}
    			      	$tabulator++;
									$cnt++;
									$ccnt++;
								}
							}
						}

						// Es können keine Schiffe gebaut werden
						if ($ccnt==0)
						{
							echo "<tr>
											<td colspan=\"9\" height=\"30\" align=\"center\">
												Es k&ouml;nnen noch keine Schiffe gebaut werden!<br>
												Baue zuerst die ben&ouml;tigten Geb&auml;ude und erforsche die erforderlichen Technologien!
											</td>
									</tr>";
						}
					}
					// Es gibt noch keine Schiffe
					else
					{
						echo "<tr><td align=\"center\" colspan=\"3\">Es gibt noch keine Schiffe!</td></tr>";
					}

   				tableEnd();
				}
   			// Baubutton anzeigen
				if ($cnt > 0)
				{
					echo "<input type=\"submit\" name=\"submit\" value=\"Bauauftr&auml;ge &uuml;bernehmen\"/><br/><br/>";
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
		echo "<h1>Raumschiffswerft des Planeten ".$cp->name."</h1>";

		// Ressourcen anzeigen
		echo ResourceBoxDrawer::getHTML($cp, $cu->properties->smallResBox);
		info_msg("Die Raumschiffswerft wurde noch nicht gebaut!");


	}
	echo "</form>";

?>
