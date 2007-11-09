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
	// 	File: shipyard.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Construct ships
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	//Definition für "Info" Link
	define('ITEMS_TBL',"ships");
	define('REQ_TBL',"ship_requirements");
	define('REQ_ITEM_FLD',"req_ship_id");
	define('ITEM_ID_FLD',"ship_id");
	define('ITEM_NAME_FLD',"ship_name");
	define('RACE_TO_ADD'," AND (ship_race_id=0 OR ship_race_id='".$s['user']['race_id']."')");
	define('ITEM_SHOW_FLD',"ship_show");
	define('ITEM_ORDER_FLD',"ship_order");
	define('NO_ITEMS_MSG',"In dieser Kategorie gibt es keine Schiffe!");
	define('HELP_URL',"?page=help&site=shipyard");

	// BEGIN SKRIPT //

	echo "<form action=\"?page=$page\" method=\"post\">";

	//Tabulator var setzten (für das fortbewegen des cursors im forumular)
	$tabulator = 1;

	//Gentech level laden
	$tlres = dbquery("
	SELECT
		techlist_current_level
	FROM
		".$db_table['techlist']."
	WHERE
    techlist_user_id='".$s['user']['id']."'
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

	// Schiffswerft Level und Arbeiter laden
  $werft_res = dbquery("
  SELECT
  	buildlist_current_level,
  	buildlist_people_working,
  	buildlist_deactivated
  FROM
  	".$db_table['buildlist']."
  WHERE
  	buildlist_planet_id='".$c->id."'
  	AND buildlist_building_id='".SHIPYARD_ID."'
  	AND buildlist_current_level>='1'
  	AND buildlist_user_id='".$s['user']['id']."'");

  // Prüfen ob Werft gebaut ist
  if (mysql_num_rows($werft_res)>0)
  {
		$werft_arr = mysql_fetch_array($werft_res);
  	define('CURRENT_SHIPYARD_LEVEL',$werft_arr['buildlist_current_level']);

		// Titel
		echo "<h1>Raumschiffswerft (Stufe ".CURRENT_SHIPYARD_LEVEL.") des Planeten ".$c->name."</h1>";

		// Ressourcen anzeigen
		$c->resBox();

		// Prüfen ob dieses Gebäude deaktiviert wurde
		if ($werft_arr['buildlist_deactivated']>time())
		{
			infobox_start("Geb&auml;ude nicht bereit");
			echo "Diese Schiffswerft ist bis ".date("d.m.Y H:i",$werft_arr['buildlist_deactivated'])." deaktiviert.";
			infobox_end();
		}
		// Werft anzeigen
		else
		{

    	// level zählen welches der schiffswerft über dem angegeben level ist und faktor berechnen
    	$need_bonus_level = CURRENT_SHIPYARD_LEVEL - $conf['build_time_boni_schiffswerft']['p1'];
    	if($need_bonus_level <= 0)
    	{
    		$time_boni_factor=1;
    	}
    	else
    	{
    		$time_boni_factor=1-($need_bonus_level*($conf['build_time_boni_schiffswerft']['v']/100));
    	}
    	$people_working = $werft_arr['buildlist_people_working'];
    	
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
    	echo "<div>";
    	//echo '<div><div style="float:left;width:450px;text-align:left;font-size:9pt;">';											
    	infobox_start("Werft-Infos",1);
    	echo "<tr><td class=\"tbldata\">";
    	echo "<b>Eingestellte Arbeiter:</b> ".nf($people_working)."<br/>
    	<b>Bauzeitverringerung:</b> ";
    	if ($need_bonus_level>=0)
    	{
    		echo get_percent_string($time_boni_factor)." durch Stufe ".CURRENT_SHIPYARD_LEVEL."<br/>";
    	}
    	else
    	{
    		echo "Stufe ".$conf['build_time_boni_schiffswerft']['p1']." erforderlich!<br/>";
    	}
    	if ($cancel_res_factor>0)
    	{
    		echo "<b>Ressourcenrückgabe bei Abbruch:</b> ".($cancel_res_factor*100)."% (ohne ".RES_FOOD.", ".(SHIPQUEUE_CANCEL_END*100)."% maximal)";
    		$cancelable = true;
    	}
    	else
    	{
    		echo "<b>Abbruchmöglichkeit:</b> Stufe ".SHIPQUEUE_CANCEL_MIN_LEVEL." erforderlich!";
    		$cancelable = false;
    	} 
    	echo "</td></tr>";   	
    	//infobox_end();
    	//echo '</div>';


	/****************************
	*  Sortiereingaben speichern *
	****************************/
//  && checker_verify()
			if(count($_POST)>0 && isset($_POST['sort_submit']))
			{
				dbquery("
				UPDATE
					".$db_table['users']."
				SET
					user_item_order_ship='".$_POST['sort_value']."',
					user_item_order_way='".$_POST['sort_way']."'
				WHERE
					user_id='".$s['user']['id']."'
				");		
				
				$s['user']['item_order_ship']=$_POST['sort_value'];
        $s['user']['item_order_way']=$_POST['sort_way'];	
			}

			
			
	/*************
	* Sortierbox *
	*************/
	
			//Legt Sortierwerte in einem Array fest
			$values = array(
											"name"=>"Name",
											"battlepoints"=>"Kosten",
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
											
//			echo '<div style="width:300px;float:right;">';											
//			infobox_start("Sortieren",1,0);
			echo "<tr>
							<td class=\"tbldata\" style=\"text-align:center;\">
								<select name=\"sort_value\">";
								foreach ($values as $value => $name)
								{		
									echo "<option value=\"".$value."\"";
									if($s['user']['item_order_ship']==$value)
									{
										echo " selected=\"selected\"";
									}
									echo ">".$name."</option>";							
								}																																																							
					echo "</select>
							
								<select name=\"sort_way\">";
								
									//Aufsteigend
									echo "<option value=\"ASC\"";
									if($s['user']['item_order_way']=='ASC') echo " selected=\"selected\"";
									echo ">Aufsteigend</option>";
									
									//Absteigend
									echo "<option value=\"DESC\"";
									if($s['user']['item_order_way']=='DESC') echo " selected=\"selected\"";
									echo ">Absteigend</option>";	
																	
					echo "</select>						
							
								<input type=\"submit\" class=\"button\" name=\"sort_submit\" value=\"Sortieren\"/>
							</td>
						</tr>";
			infobox_end(1);
			//echo '</div>';
			echo '<br style="clear:both;" /></div>';
			
			echo "</form>";

			echo "<form action=\"?page=$page\" method=\"post\">";

	/****************************
	*  Schiffe in Auftrag geben *
	****************************/

			if(count($_POST)>0 && isset($_POST['submit']) && checker_verify())
			{
				echo "<table class=\"tb\">";
				echo "<tr><th>Ergebnisse des Bauauftrags</th></tr>";

				// Endzeit bereits laufender Aufträge laden
				$end_time=time();
				
				//Log variablen setzten
				$log_ships="";
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
					".$db_table['ship_queue']."
				WHERE
  				queue_planet_id='".$c->id."'
  				AND queue_user_id='".$s['user']['id']."'
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
				foreach ($_POST['build_count'] as $ship_id=> $build_cnt)
				{
					$build_cnt=nf_back($build_cnt);

					if ($build_cnt>0)
					{
						// Schiffe laden
						$sres = dbquery("
						SELECT
							*
						FROM
							".$db_table['ships']."
						WHERE
							ship_id='".$ship_id."';
						");
						$sarr = mysql_fetch_array($sres);

	   				// TODO: Überprüfen
						//Anzahl überprüfen, ob diese die maximalzahl übersteigt, gegebenenfalls ändern
    	      if($build_cnt > $sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
    	    	{
    	      	$build_cnt=$sarr['ship_max_count'];
    	      }

    				// TODO: Überprüfen
						//Wenn der User nicht genug Ress hat, die Anzahl Schiffe drosseln
						//Titan
						if ($sarr['ship_costs_metal']>0)
						{
							$bf['metal']=$c->res->metal/$sarr['ship_costs_metal'];
						}
						else
						{
							$bc['metal']=0;
						}
						//Silizium
						if ($sarr['ship_costs_crystal']>0)
						{
							$bf['crystal']=$c->res->crystal/$sarr['ship_costs_crystal'];
						}
						else
						{
							$bc['crystal']=0;
						}
						//PVC
						if ($sarr['ship_costs_plastic']>0) 
						{
							$bf['plastic']=$c->res->plastic/$sarr['ship_costs_plastic']; 
						}
						else 
						{
							$bc['plastic']=0;
						}
						//Tritium
						if ($sarr['ship_costs_fuel']>0) 
						{
							$bf['fuel']=$c->res->fuel/$sarr['ship_costs_fuel']; 
						}
						else 
						{
							$bc['fuel']=0;
						}
						//Nahrung
						if ($_POST['additional_food_costs']>0 || $sarr['ship_costs_food']>0)
						{
							 $bf['food']=$c->res->food/($_POST['additional_food_costs']+$sarr['ship_costs_food']); 
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
							$bc['metal']=$sarr['ship_costs_metal']*$build_cnt;
							$bc['crystal']=$sarr['ship_costs_crystal']*$build_cnt;
							$bc['plastic']=$sarr['ship_costs_plastic']*$build_cnt;
							$bc['fuel']=$sarr['ship_costs_fuel']*$build_cnt;
							$bc['food']=($_POST['additional_food_costs']+$sarr['ship_costs_food'])*$build_cnt;

    	        //Berechnete Ress provisorisch abziehen
    	        $c->res->metal-=$bc['metal'];
    	        $c->res->crystal-=$bc['crystal'];
    	        $c->res->plastic-=$bc['plastic'];
    	        $c->res->fuel-=$bc['fuel'];
    	        $c->res->food-=$bc['food'];

							// Bauzeit pro Schiff berechnen
							$btime = ($sarr['ship_costs_metal'] + $sarr['ship_costs_crystal'] + $sarr['ship_costs_plastic'] + $sarr['ship_costs_fuel'] + $sarr['ship_costs_food']) / 12 * GLOBAL_TIME * SHIP_BUILD_TIME * $time_boni_factor;

	    				// TODO: Überprüfen
							//Rechnet zeit wenn arbeiter eingeteilt sind
							$btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
							if ($btime_min<SHIPYARD_MIN_BUILD_TIME) $btime_min=SHIPYARD_MIN_BUILD_TIME;
							$btime=$btime-$people_working*3;
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
    	        ".$db_table['ship_queue']."
    	            (queue_user_id,
    	            queue_ship_id,
    	            queue_planet_id,
    	            queue_cnt,
    	            queue_starttime,
    	            queue_endtime,
    	            queue_objtime)
    	        VALUES
    	            ('".$s['user']['id']."',
    	            '".$ship_id."',
    	            '".$c->id."',
    	            '".$build_cnt."',
    	            '".$start_time."',
    	            '".$end_time."',
    	            '".$obj_time."');");
    	        $shiplist_id = mysql_insert_id();
								
							echo "<tr><td>".nf($build_cnt)." ".$sarr['ship_name']." in Auftrag gegeben!</td></tr>";
							
							//Rohstoffe summieren, diese werden nach der Schleife abgezogen
							$total_metal+=$bc['metal'];
							$total_crystal+=$bc['crystal'];
							$total_plastic+=$bc['plastic'];
							$total_fuel+=$bc['fuel'];
							$total_food+=$bc['food'];
							
							
							//Daten für Log speichern
							$log_ships.="<b>".$sarr['ship_name']."</b>: ".nf($build_cnt)." (".tf($duration).")<br>";
							$total_duration+=$duration;							
						}
						else
						{
							echo "<tr><td>".$sarr['ship_name'].": Zu wenig Rohstoffe für diese Anzahl!</td></tr>";
						}
						$counter++;
					}							
				}
				
				// Die Roshtoffe der $c-variablen wieder beigeben, da sie sonst doppelt abgezogen werden
        $c->res->metal+=$total_metal;
        $c->res->crystal+=$total_crystal;
        $c->res->plastic+=$total_plastic;
        $c->res->fuel+=$total_fuel;
        $c->res->food+=$total_food;				
				
				//Rohstoffe vom Planeten abziehen und aktualisieren
				$c->changeRes(-$total_metal,-$total_crystal,-$total_plastic,-$total_fuel,-$total_food);
												
				//Log schreiben
				$log_text = "
				<b>Schiffsauftrag Bauen</b><br><br>
				<b>User:</b> [USER_ID=".$s['user']['id'].";USER_NICK=".$s['user']['nick']."]<br>
				<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
				<b>Dauer des gesamten Auftrages:</b> ".tf($total_duration)."<br>
				<b>Ende des gesamten Auftrages:</b> ".date("Y-m-d H:i:s",$end_time)."<br><br>
				<b>Kosten</b><br>
				<b>".RES_METAL.":</b> ".nf($total_metal)."<br>
				<b>".RES_CRYSTAL.":</b> ".nf($total_crystal)."<br>
				<b>".RES_PLASTIC.":</b> ".nf($total_plastic)."<br>
				<b>".RES_FUEL.":</b> ".nf($total_fuel)."<br>
				<b>".RES_FOOD.":</b> ".nf($total_food)."<br><br>
				<b>Rohstoffe auf dem Planeten</b><br><br>
				<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
				<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
				<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
				<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
				<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br><br>
				<b>Schiffe</b><br>
				".$log_ships."
				";
				
				//Log Speichern
				add_log_game_ship($log_text,$s['user']['id'],$s['user']['alliance_id'],$c->id,1,time());					
				
				if ($counter==0)
				{
					echo "<tr><td>Keine Schiffe gew&auml;hlt!</td></tr>";
				}
				echo "</table><br/>";
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
					ship_name,
					ship_costs_metal,
					ship_costs_crystal,
					ship_costs_fuel,
					ship_costs_plastic,
					ship_costs_food,
    			queue_endtime,
    			queue_starttime,
    			queue_objtime,
    			queue_cnt
				FROM
					".$db_table['ship_queue']."
				INNER JOIN
 	  			".$db_table['ships']."
	  		  ON queue_ship_id=ship_id
					AND queue_id='".intval($_GET['cancel'])."'
					AND queue_user_id='".$s['user']['id']."'
					AND queue_planet_id='".$c->id."'
					AND queue_endtime>'".$time."'
				;");
				if (mysql_num_rows($qres)>0)
				{
					$qarr=mysql_fetch_array($qres);
					
					//Zu erhaltende Rohstoffe errechnen
					$obj_cnt=ceil(($qarr['queue_endtime']-$time)/$qarr['queue_objtime']);
					$ret['metal']=$qarr['ship_costs_metal']*$obj_cnt*$cancel_res_factor;
					$ret['crystal']=$qarr['ship_costs_crystal']*$obj_cnt*$cancel_res_factor;
					$ret['plastic']=$qarr['ship_costs_plastic']*$obj_cnt*$cancel_res_factor;
					$ret['fuel']=$qarr['ship_costs_fuel']*$obj_cnt*$cancel_res_factor;
					$ret['food']=$qarr['ship_costs_food']*$obj_cnt*$cancel_res_factor;

					//Auftrag löschen
					dbquery("
					DELETE FROM
					 ".$db_table['ship_queue']."
					WHERE
						queue_id='".intval($_GET['cancel'])."';");

					//Nachkommende Aufträge werden Zeitlich nach vorne verschoben
					$tres=dbquery("
					SELECT
						queue_id,
						queue_starttime,
						queue_endtime
					FROM
						".$db_table['ship_queue']."
					WHERE
						queue_starttime>='".$qarr['queue_endtime']."'
						AND queue_user_id='".$s['user']['id']."'
						AND queue_planet_id='".$c->id."'
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
								".$db_table['ship_queue']."
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
					$c->changeRes($ret['metal'],$ret['crystal'],$ret['plastic'],$ret['fuel'],$ret['food']);						
						
					echo "Der Auftrag wurde abgebrochen!<br/><br/>";
						
					//Log schreiben
					$log_text = "
					<b>Schiffsauftrag Abbruch</b><br><br>
					<b>User:</b> [USER_ID=".$s['user']['id'].";USER_NICK=".$s['user']['nick']."]<br>
					<b>Planeten:</b> [PLANET_ID=".$c->id.";PLANET_NAME=".$c->name."]<br>
					<b>Schiff:</b> ".$qarr['ship_name']."
					<b>Anzahl:</b> ".nf($qarr['queue_cnt'])."
					<b>Auftragsdauer:</b> ".tf($qarr['queue_objtime']*$qarr['queue_cnt'])."<br><br>
					<b>Erhaltene Rohstoffe</b><br>
					<b>Faktor:</b> ".$cancel_res_factor."<br>
					<b>".RES_METAL.":</b> ".nf($ret['metal'])."<br>
					<b>".RES_CRYSTAL.":</b> ".nf($ret['crystal'])."<br>
					<b>".RES_PLASTIC.":</b> ".nf($ret['plastic'])."<br>
					<b>".RES_FUEL.":</b> ".nf($ret['fuel'])."<br>
					<b>".RES_FOOD.":</b> ".nf($ret['food'])."<br><br>
					<b>Rohstoffe auf dem Planeten</b><br><br>
					<b>".RES_METAL.":</b> ".nf($c->res->metal)."<br>
					<b>".RES_CRYSTAL.":</b> ".nf($c->res->crystal)."<br>
					<b>".RES_PLASTIC.":</b> ".nf($c->res->plastic)."<br>
					<b>".RES_FUEL.":</b> ".nf($c->res->fuel)."<br>
					<b>".RES_FOOD.":</b> ".nf($c->res->food)."<br>
					";
					
					//Log Speichern
					add_log_game_ship($log_text,$s['user']['id'],$s['user']['alliance_id'],$c->id,0,time());					
				}
			}



	/*********************************
	* Liste der Bauaufträge anzeigen *
	*********************************/
			$qres = dbquery("
			SELECT
    		ship_name,
    		queue_id,
    		queue_ship_id,
    		queue_cnt,
    		queue_starttime,
    		queue_endtime,
    		queue_objtime
			FROM
    		".$db_table['ship_queue']."
    	INNER JOIN
    		".$db_table['ships']."
    	ON
    		queue_ship_id=ship_id
  			AND queue_planet_id='".$c->id."'
  			AND queue_user_id='".$s['user']['id']."'
  			AND queue_endtime>'".$time."'
    	ORDER BY
				queue_starttime ASC;");
			if (mysql_num_rows($qres)>0)
			{
				infobox_start("Bauliste",1);
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
								<td class=\"tbltitle\" colspan=\"2\">Aktuell</td>
								<td class=\"tbltitle\" style=\"width:150px;\">Start</td>
								<td class=\"tbltitle\" style=\"width:150px;\">Ende</td>
								<td class=\"tbltitle\" style=\"width:80px;\" colspan=\"2\">Verbleibend</td>
							</tr>";
						echo "<tr>";
						echo "<td class=\"tbldata\" colspan=\"2\">".$qarr['ship_name']."</td>";
						echo "<td class=\"tbldata\">".df(time()-$obj_t_passed,1)."</td>";
						echo "<td class=\"tbldata\">".df(time()+$obj_t_remaining,1)."</td>";
						echo "<td class=\"tbldata\" colspan=\"2\">".tf($obj_t_remaining)."</td>
						</tr>";
						echo "<tr>
								<td class=\"tbltitle\" style=\"width:40px;\">Anzahl</td>
								<td class=\"tbltitle\">Bauauftrag</td>
								<td class=\"tbltitle\" style=\"width:150px;\">Start</td>
								<td class=\"tbltitle\" style=\"width:150px;\">Ende</td>
								<td class=\"tbltitle\" style=\"width:150px;\">Verbleibend</td>
								<td class=\"tbltitle\" style=\"width:80px;\">Aktionen</td>
							</tr>";
						$first=false; 
					}

					echo "<tr>";
					echo "<td class=\"tbldata\" id=\"objcount\">".$qarr['queue_cnt']."</td>";
					echo "<td class=\"tbldata\">".$qarr['ship_name']."</td>";
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
					
					//Summiert die Anzahl der bauenden Schiffen pro Typ, für im unteren Teil "Maximal Anzahl erreicht" anzeigen zu lassen
					//Würde beispielsweise die Bauliste nach den Schiffen kommen, müsste man eine neue Queue-Abfrage machen!
					if(isset($queue_cnt[$qarr['queue_ship_id']]))
					{
						$queue_cnt[$qarr['queue_ship_id']]+=$qarr['queue_cnt'];
					}
					else
					{
						$queue_cnt[$qarr['queue_ship_id']]=$qarr['queue_cnt'];
					}
				}
				infobox_end(1);
			 	echo "<br/><br/>";

			}



	/***********************
	* Schiffe auflisten    *
	***********************/

			// Vorausetzungen laden
			$res = dbquery("SELECT * FROM ".$db_table['ship_requirements'].";");
			while ($arr = mysql_fetch_array($res))
			{
				//Gebäude Vorausetzungen
				if ($arr['req_req_building_id']>0) 
				{
					$req[$arr['req_ship_id']]['b'][$arr['req_req_building_id']]=$arr['req_req_building_level'];
				}
				
				//Technologie Voraussetzungen
				if ($arr['req_req_tech_id']>0) 
				{
					$req[$arr['req_ship_id']]['t'][$arr['req_req_tech_id']]=$arr['req_req_tech_level'];
				}
			}


			//Technologien laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['techlist']." 
			WHERE 
				techlist_user_id='".$s['user']['id']."';");
			while ($arr = mysql_fetch_array($res))
			{
				$techlist[$arr['techlist_tech_id']]=$arr['techlist_current_level'];
			}

			//Gebäude laden
			$res = dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['buildlist']." 
			WHERE 
				buildlist_planet_id='".$c->id."' 
				AND buildlist_user_id='".$s['user']['id']."';");
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
					".$db_table['ship_cat']."
				INNER JOIN
					ships
				ON
					ship_cat_id=cat_id
					AND ship_buildable=1
					AND ship_show=1
					AND (ship_race_id=0 
					OR ship_race_id=".$s['user']['race_id'].")
				GROUP BY
					cat_id
				ORDER BY
					cat_order
			;");
			$cnt = 0;
			if (mysql_num_rows($cres)>0)
			{
				while($carr=mysql_fetch_array($cres))
				{
					infobox_start($carr['cat_name'],1);
					$ccnt = 0;

					//Schiffsordnung des Users beachten
					$order="ship_".$s['user']['item_order_ship']." ".$s['user']['item_order_way']."";

					// Auflistung der Schiffe (auch diese, die noch nicht gebaut wurden)
					$sres = dbquery("
					SELECT
    				ship_id,
    				ship_name,
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
    				shiplist_count
 					FROM
    				".$db_table['ships']."
    			LEFT JOIN
    				".$db_table['shiplist']."
    					ON shiplist_ship_id=ship_id
    					AND shiplist_planet_id='".$c->id."'
    	        AND shiplist_user_id='".$s['user']['id']."'
   				WHERE
    				ship_buildable=1
    				AND ship_cat_id=".$carr['cat_id']."
    				AND ship_show=1
    				AND (ship_race_id=0 OR ship_race_id=".$s['user']['race_id'].")
    			ORDER BY
    				special_ship DESC,
    				".$order.";"); 
					if (mysql_num_rows($sres)>0)
					{
						//Einfache Ansicht
						if ($s['user']['item_show']!='full')
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
						
						while ($sarr = mysql_fetch_array($sres))
						{
							// Prüfen ob Schiff gebaut werden kann
    			  	$build_ship = 1;
    			  	// Gebäude prüfen
    			    if (count($req[$sarr['ship_id']]['b'])>0)
    			    {
  			        foreach ($req[$sarr['ship_id']]['b'] as $id=>$level)
  			        {
			            if (!isset($buildlist[$id]) || $buildlist[$id]<$level)
			            {
			            	$build_ship = 0;
			            }
  			        }
    			    }
    			  	// Technologien prüfen
    			    if (count($req[$sarr['ship_id']]['t'])>0)
    			    {
  			        foreach ($req[$sarr['ship_id']]['t'] as $id=>$level)
  			        {
			            if (!isset($techlist[$id]) || $techlist[$id]<$level)
			            {
			            	$build_ship = 0;
			            }
  			        }
    			    }

    			    // Schiffdatensatz zeigen
							if ($build_ship==1)
							{
    			      //zählt die anzahl schiffe dieses typs im ganzen account...
    			      $ship_count=0;
    			      //...auf den planeten
    			      $check_res1 = dbquery("
    			      SELECT
    			          SUM(shiplist_count) AS count
    			      FROM
    			          ".$db_table['shiplist']."
    			      WHERE
    			          shiplist_ship_id='".$sarr['ship_id']."'
    			          AND shiplist_user_id='".$s['user']['id']."';");
    			      if (mysql_num_rows($check_res1)>0)
    			      {
    			        while ($check_arr1=mysql_fetch_array($check_res1))
    			        {
    			            $ship_count+=$check_arr1['count'];
    			        }
    			      }
    			      //...in der Bauschleife
    			      if(isset($queue_cnt[$sarr['ship_id']]))
    			      {
    			      	$ship_count+=$queue_cnt[$sarr['ship_id']];
    			      }
    			      //...in der luft
    			      $check_res2 = dbquery("
    			      SELECT
    			          fs.fs_ship_cnt
    			      FROM
    			          ".$db_table['fleet']." AS f
    			          INNER JOIN
    			          ".$db_table['fleet_ships']." AS fs
    			          ON f.fleet_id=fs.fs_fleet_id
    			          AND fs.fs_ship_id='".$sarr['ship_id']."'
    			          AND f.fleet_user_id='".$s['user']['id']."';");
    			      if (mysql_num_rows($check_res2)>0)
    			      {
    			        while ($check_arr2=mysql_fetch_array($check_res2))
    			        {
    			            $ship_count+=$check_arr2['fs_ship_cnt'];
    			        }
    			      }

    						// Bauzeit berechnen
								$btime = ($sarr['ship_costs_metal']+$sarr['ship_costs_crystal']+$sarr['ship_costs_plastic']+$sarr['ship_costs_fuel']+$sarr['ship_costs_food']) / 12 * GLOBAL_TIME * SHIP_BUILD_TIME * $time_boni_factor;
    			      $btime_min=$btime*(0.1-(GEN_TECH_LEVEL/100));
    			      
    			      //Mindest Bauzeit
    			      if ($btime_min<SHIPYARD_MIN_BUILD_TIME) 
    			      {
    			      	$btime_min=SHIPYARD_MIN_BUILD_TIME;
    			      }
    			      
    			      $btime=ceil($btime-$people_working*3);
    			      if ($btime<$btime_min) 
    			      {
    			      	$btime=$btime_min;
    			      }

								//Nahrungskosten berechnen
								$food_costs = $people_working*12 + $sarr['ship_costs_food'];
								
								//Nahrungskosten versteckt übermitteln
								echo "<input type=\"hidden\" name=\"additional_food_costs\" value=\"".$food_costs."\" />";
								
								
								
								//Errechnet wie viele Schiffe von diesem Typ maximal Gebaut werden können mit den aktuellen Rohstoffen
								
								//Titan
								if($sarr['ship_costs_metal']>0)
								{
									$build_cnt_metal=floor($c->res->metal/$sarr['ship_costs_metal']);
								}
								else
								{
									$build_cnt_metal=99999999999;
								}

								//Silizium
								if($sarr['ship_costs_crystal']>0)
								{
									$build_cnt_crystal=floor($c->res->crystal/$sarr['ship_costs_crystal']);
								}
								else
								{
									$build_cnt_crystal=99999999999;
								}
						
								//PVC
								if($sarr['ship_costs_plastic']>0)
								{
									$build_cnt_plastic=floor($c->res->plastic/$sarr['ship_costs_plastic']);
								}
								else
								{
									$build_cnt_plastic=99999999999;
								}
								
								//Tritium
								if($sarr['ship_costs_fuel']>0)
								{
									$build_cnt_fuel=floor($c->res->fuel/$sarr['ship_costs_fuel']);
								}
								else
								{
									$build_cnt_fuel=99999999999;
								}

								//Nahrung
								if($food_costs>0)
								{
									$build_cnt_food=floor($c->res->food/$food_costs);
								}
								else
								{
									$build_cnt_food=99999999999;
								}

								//Begrente Anzahl baubar
								if($sarr['ship_max_count']!=0)
								{
									$max_cnt=$sarr['ship_max_count']-$ship_count;
								}
								else
								{
									$max_cnt=99999999999;
								}

								//Effetiv max. baubare Schiffe in Betrachtung der Rohstoffe und des Baumaximums
								$ship_max_build=min($build_cnt_metal,$build_cnt_crystal,$build_cnt_plastic,$build_cnt_fuel,$build_cnt_food,$max_cnt);

								//Tippbox Nachricht generieren
								//X Schiffe baubar
								if($ship_max_build>0)
								{
									$tm_cnt="Es k&ouml;nnen maximal ".nf($ship_max_build)." Schiffe gebaut werden.";
								}
								//Zuwenig Rohstoffe. Wartezeit errechnen
								elseif($ship_max_build==0)
								{
									//Wartezeit Titan
    			    		if ($c->prod->metal>0)
    			    		{
    			    			$bwait['metal']=ceil(($sarr['ship_costs_metal']-$c->res->metal)/$c->prod->metal*3600);
    			    		}
    			    		else
    			    		{
    			    			$bwait['metal']=0;
    			    		}
    			    		
    			    		//Wartezeit Silizium
    			    		if ($c->prod->crystal>0)
    			    		{
    			    			$bwait['crystal']=ceil(($sarr['ship_costs_crystal']-$c->res->crystal)/$c->prod->crystal*3600);
    			    		}
    			    		else
    			    		{ 
    			    			$bwait['crystal']=0;
    			    		}
    			    		
    			    		//Wartezeit PVC
    			    		if ($c->prod->plastic>0)
    			    		{
    			    			$bwait['plastic']=ceil(($sarr['ship_costs_plastic']-$c->res->plastic)/$c->prod->plastic*3600);
    			    		}
    			    		else
    			    		{ 
    			    			$bwait['plastic']=0;
    			    		}
    			    		
    			    		//Wartezeit Tritium
    			    		if ($c->prod->fuel>0)
    			    		{
    			    			$bwait['fuel']=ceil(($sarr['ship_costs_fuel']-$c->res->fuel)/$c->prod->fuel*3600);
    			    		}
    			    		else
    			    		{ 
    			    			$bwait['fuel']=0;
    			    		}
    			    		
    			    		//Wartezeit Nahrung
    			    		if ($c->prod->food>0)
    			    		{
    			    			$bwait['food']=ceil(($food_costs-$c->res->food)/$c->prod->food*3600);
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
								if($sarr['ship_costs_metal']>$c->res->metal)
								{
									$ress_style_metal="style=\"color:red;\"";
								}
								else
								{
									$ress_style_metal="";
								}
								
								//Silizium
								if($sarr['ship_costs_crystal']>$c->res->crystal)
								{
									$ress_style_crystal="style=\"color:red;\"";
								}
								else
								{
									$ress_style_crystal="";
								}
								
								//PVC
								if($sarr['ship_costs_plastic']>$c->res->plastic)
								{
									$ress_style_plastic="style=\"color:red;\"";
								}
								else
								{
									$ress_style_plastic="";
								}
								
								//Tritium
								if($sarr['ship_costs_fuel']>$c->res->fuel)
								{
									$ress_style_fuel="style=\"color:red;\"";
								}
								else
								{
									$ress_style_fuel="";
								}
								
								//Nahrung
								if($food_costs>$c->res->food)
								{
									$ress_style_food="style=\"color:red;\"";
								}
								else
								{
									$ress_style_food="";
								}


    			      // Sicherstellen dass epische Spezialschiffe nur auf dem Hauptplanet gebaut werden
 			      		if ($sarr['special_ship']==0 || $c->isMain)
 			      		{
									// Volle Ansicht
    			      	if($s['user']['item_show']=='full')
    			      	{
    			      		if ($ccnt>0)
    			      		{
    			      			echo "<tr>
    			      							<td colspan=\"5\" style=\"height:5px;\"></td>
    			      					</tr>";
    			      		}
    			      	  $s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_middle.".IMAGE_EXT;
    			      	  
    			      	  echo "<tr>
    			      	  				<td class=\"tbltitle\" colspan=\"5\" height=\"20\">".$sarr['ship_name']."</td>
    			      	  			</tr>
    			      	  			<tr>
    			      	  				<td class=\"tbldata\" width=\"120\" height=\"120\" rowspan=\"3\">";
    			      	  				
				    			      	  //Bei Spezialschiffen nur Bild ohne Link darstellen
				    			      		if ($sarr['special_ship']==1)
														{
															echo "<img src=\"".$s_img."\" width=\"120\" height=\"120\" border=\"0\" />";
														}
														//Bei normalen Schiffen mit Hilfe verlinken
														else
														{
															echo "<a href=\"".HELP_URL."&amp;id=".$sarr[ITEM_ID_FLD]."\" title=\"Info zu diesem Schiff anzeigen\">
				    			      	  	<img src=\"".$s_img."\" width=\"120\" height=\"120\" border=\"0\" /></a>";
				    			      	  }
    			      	  	echo "</td>
    			      	  				<td class=\"tbldata\" colspan=\"4\" valign=\"top\">".$sarr['ship_shortcomment']."</td>
    			      	  			</tr>
    			      	  			<tr>
    			      	  				<th class=\"tbltitle\"  height=\"30\">Vorhanden:</th>
				    			      	  <td class=\"tbldata\" colspan=\"3\">".nf($sarr['shiplist_count'])."</td>
				    			      	</tr>
				    			      	<tr>
				    			      	 	<th class=\"tbltitle\" height=\"30\">Bauzeit</th>
    			      	  				<td class=\"tbldata\">".tf($btime)."</td>";
    			      	  				
				    			      	  //Maximale Anzahl erreicht
				    			      	  if ($ship_count>=$sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
				    			      	  {
				    			      	     	echo "<th class=\"tbltitle\" height=\"30\" colspan=\"2\"><i>Maximalanzahl erreicht</i></th>";
				    			      	  }
				    			      	  else
				    			      	  {
				    			      	      echo "<th class=\"tbltitle\" height=\"30\">In Aufrag geben:</th>
				    			      	      			<td class=\"tbldata\"><input type=\"text\" value=\"0\" name=\"build_count[".$sarr['ship_id']."]\" id=\"build_count_".$sarr['ship_id']."\" size=\"4\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$ship_max_build.", '', '');\"/> St&uuml;ck<br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_".$sarr['ship_id']."').value=".$ship_max_build.";\">max</a></td>";
				    			      	  }
    			      	  echo "</tr>";
    			      	  echo "<tr>
				    			      	  <td class=\"tbltitle\" height=\"20\" width=\"110\">".RES_METAL.":</td>
				    			      	  <td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_CRYSTAL.":</td>
				    			      	  <td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_PLASTIC.":</td>
				    			      	  <td class=\"tbltitle\" height=\"20\" width=\"97\">".RES_FUEL.":</td>
				    			      	  <td class=\"tbltitle\" height=\"20\" width=\"98\">".RES_FOOD."</td></tr>";
    			      	  echo "<tr>
    			      	  				<td class=\"tbldata\" height=\"20\" width=\"110\" ".$ress_style_metal.">
    			      	  					".nf($sarr['ship_costs_metal'])."
    			      	  				</td>
    			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_crystal.">
    			      	  					".nf($sarr['ship_costs_crystal'])."
    			      	  				</td>
    			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_plastic.">
    			      	  					".nf($sarr['ship_costs_plastic'])."
    			      	  				</td>
    			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_fuel.">
    			      	  					".nf($sarr['ship_costs_fuel'])."
    			      	  				</td>
    			      	  				<td class=\"tbldata\" height=\"20\" width=\"25%\" ".$ress_style_food.">
    			      	  					".nf($food_costs)."
    			      	  				</td>
    			      	  			</tr>";
    			      	}
    			      	//Einfache Ansicht der Schiffsliste
    			      	else
    			      	{
  			      			$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_small.".IMAGE_EXT;
  			      			
  			      			echo "<tr>
  			      							<td class=\"tbldata\">";
  			      						
	  			      						//Spezialschiffe ohne Link darstellen
				  			      			if ($sarr['special_ship']==1)
														{
				  			      				echo "<img src=\"$s_img\" width=\"40\" height=\"40\" border=\"0\" /></td>";
				  			      			}
				  			      			//Normale Schiffe mit Link zur Hilfe darstellen
				  			      			else
				  			      			{
				  			      				echo "<a href=\"".HELP_URL."&amp;id=".$sarr[ITEM_ID_FLD]."\"><img src=\"".$s_img."\" width=\"40\" height=\"40\" border=\"0\" /></a></td>";
				  			      			}
				  			      			
	  			      			echo "<td class=\"tbltitle\" width=\"30%\">
	  			      							<span style=\"font-weight:500\">".$sarr['ship_name']."<br/>
	  			      							Gebaut:</span> ".nf($sarr['shiplist_count'])."
	  			      						</td>
	  			      						<td class=\"tbldata\" width=\"13%\">".tf($btime)."</td>
	  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_metal.">".nf($sarr['ship_costs_metal'])."</td>
	  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_crystal.">".nf($sarr['ship_costs_crystal'])."</td>
	  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_plastic.">".nf($sarr['ship_costs_plastic'])."</td>
	  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_fuel.">".nf($sarr['ship_costs_fuel'])."</td>
	  			      						<td class=\"tbldata\" width=\"10%\" ".$ress_style_food.">".nf($food_costs)."</td>";

														//Maximale Anzahl erreicht
				  			      			if ($ship_count>=$sarr['ship_max_count'] && $sarr['ship_max_count']!=0)
				  			      			{
				  			      			    echo "<td class=\"tbldata\">Max</td></tr>";
				  			      			}
				  			      			else
				  			      			{
				  			      			    echo "<td class=\"tbldata\"><input type=\"text\" value=\"0\" id=\"build_count_".$sarr['ship_id']."\" name=\"build_count[".$sarr['ship_id']."]\" size=\"5\" maxlength=\"9\" ".tm("",$tm_cnt)." tabindex=\"".$tabulator."\" onkeyup=\"FormatNumber(this.id,this.value, ".$ship_max_build.", '', '');\"/><br><a href=\"javascript:;\" onclick=\"document.getElementById('build_count_".$sarr['ship_id']."').value=".$ship_max_build.";\">max</a></td></tr>";
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
											<td colspan=\"9\" height=\"30\" align=\"center\" class=\"tbldata\">
												Es k&ouml;nnen noch keine Schiffe gebaut werden!<br>
												Baue zuerst die ben&ouml;tigten Geb&auml;ude und erforsche die erforderlichen Technologien!
											</td>
									</tr><br>";
						}
					}
					// Es gibt noch keine Schiffe
					else
					{
						echo "<tr><td align=\"center\" colspan=\"3\" class=\"tbldata\">Es gibt noch keine Schiffe!</td></tr>";
					}

   				infobox_end(1);
   				
   				//Lücke zwischen Kategorien
   				echo "<br/>";
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
		echo "<h1>Raumschiffswerft des Planeten ".$c->name."</h1>";		
		
		// Ressourcen anzeigen
		$c->resBox();
		echo "<br>Die Schiffswerft wurde noch nicht gebaut!<br>";
	}
	echo "</form>";

?>
