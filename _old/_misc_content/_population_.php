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
	// 	File: population.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about the planetar population
	*
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// DEFINITIONEN //

	define(BUILD_BUILDING_ID,6);
	define(TECH_BUILDING_ID,8);
	define(SHIP_BUILDING_ID,9);
	define(DEF_BUILDING_ID,10);

	// BEGIN SKRIPT //

	if ($planets->current)
	{

		echo "<h1>Bevölkerungsübersicht des Planeten ".$c->name."</h1>";
		$c->resBox();

		$res = dbquery("SELECT building_store_factor,
		building_name,
		building_people_place,
		buildlist_current_level
		FROM ".$db_table['buildlist'].",".$db_table['buildings']." WHERE
		buildlist_building_id=building_id
		AND buildlist_planet_id=".$c->id."
		AND building_people_place>0;");
		if (mysql_num_rows($res)>0)
		{
			//
			// Wohnfläche
			//
			infobox_start("Wohnfläche",1,0);
			echo "<tr><td class=\"tbldata\">Grundwohnfläche</td><td class=\"tbldata\">".nf($conf['user_start_people']['p1'])."</td></tr>";
			$pcnt=$conf['user_start_people']['p1'];
			while ($arr=mysql_fetch_array($res))
			{
				$place = floor($arr['building_people_place'] * pow($arr['building_store_factor'],$arr['buildlist_current_level']-1));
				echo "<tr><td class=\"tbldata\">".$arr['building_name']."</td><td class=\"tbldata\">".nf($place)."</td></tr>";
				$pcnt+=$place;
			}
			echo "<tr><td class=\"tbldata\"><b>TOTAL</b></td><td class=\"tbldata\"><b>".nf($pcnt)."</b></td></tr>";
			infobox_end(1);


  		// Arbeitende (besetzte) Leute laden
  			$people_working=0;
			$bres = dbquery("SELECT buildlist_people_working FROM ".$db_table['buildlist']." WHERE buildlist_people_working_status='work' AND buildlist_planet_id=".$c->id.";");
			while ($barr=mysql_fetch_array($bres))
			{
				$people_working+=$barr['buildlist_people_working'];
			}

  		//
  		// Arbeiter zuteilen
  		//
			if ($_POST['submit_people_work']!="" && checker_verify())
			{
				$free = $c->people - $people_working;
				foreach ($_POST['people_work'] as $id=>$num)
				{
					$num=abs(intval($num));
					if($free<0) $free = 0;
				 	$work = min($free,$num);
				 	$free-=$work;
					$ares = dbquery("SELECT buildlist_people_working_status FROM ".$db_table['buildlist']." WHERE buildlist_building_id=$id AND buildlist_planet_id=".$c->id."");
					$aarr = mysql_fetch_array($ares);
					if ($aarr['buildlist_people_working_status']=='dontwork')
					{
			  		dbquery("UPDATE ".$db_table['buildlist']." SET buildlist_people_working=".$work." WHERE buildlist_building_id=$id AND buildlist_planet_id=".$c->id."");
					}
					$people_working+=$work;
				}
			}

  		// Zählt alle arbeiter die eingetragen snid (besetzt oder nicht) für die anszeige!
  			$people_working=0;
			$bres = dbquery("SELECT buildlist_people_working  FROM ".$db_table['buildlist']." WHERE buildlist_planet_id=".$c->id.";");
			while ($barr=mysql_fetch_array($bres))
			{
				$people_working+=$barr['buildlist_people_working'];
			}

			if ($_POST['submit_people_free']!="" && checker_verify())
			{
	  		dbquery("UPDATE ".$db_table['buildlist']." SET buildlist_people_working='0' WHERE buildlist_user_id=".$s['user']['id']." AND  buildlist_people_working_status='dontwork' AND buildlist_planet_id=".$c->id."");
	  		$people_working=0;
			}

			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			echo "Wenn einem Gebäude Arbeiter zugeteilt werden wird es entsprechen schneller gebaut, die Arbeiter benötigen jedoch Nahrung. ";
			echo "Die Zuteilung der Arbeiter kann erst geändert werden wenn entsprechende Bauaufträge abgeschlossen sind. ";
			echo "Die gesamte Nahrung für die Arbeiter wird beim Start eines Bauvorgangs sofort vom Planetenkonto abgezogen.<br/><br/>";
			infobox_start("Arbeiter zuteilen",1,0);
			echo "<tr><th class=\"tbltitle\">Gebäude</th><th class=\"tbltitle\">Arbeiter</th><th class=\"tbltitle\">Zusätzliche Nahrung</th></tr>";

			//Abfrage Schiffe
			$sp_res = dbquery("
			SELECT 
                buildlist_people_working,
                building_name,
                building_people_place 
			FROM 
                ".$db_table['buildlist'].",
                ".$db_table['buildings']." 
			WHERE 
                buildlist_building_id=building_id 
                AND buildlist_planet_id='".$c->id."' 
                AND building_id='".SHIP_BUILDING_ID."';");
			$work_available=0;
			if (mysql_num_rows($sp_res)>0)
			{
				$work_available=1;
				$sp_arr=mysql_fetch_array($sp_res);
				if (mysql_num_rows(dbquery("
				SELECT 
					shiplist_ship_id 
				FROM 
                    ".$db_table['shiplist']."
				WHERE 
                    shiplist_planet_id='".$c->id."' 
                    AND shiplist_user_id='".$s['user']['id']."' 
                    AND shiplist_build_start_time>'0' 
                    AND shiplist_build_end_time>'0';")
				)>0)
				{
					echo "<tr><td class=\"tbldata2\" width=\"150\">".$sp_arr['building_name']."</td>";
					echo "<td class=\"tbldata\"><input type=\"text\" readonly=\"readonly\"  value=\"".$sp_arr['buildlist_people_working']."\" size=\"5\" maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\" /></td>";

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='work' 
					WHERE 
						buildlist_building_id='".SHIP_BUILDING_ID."' 
						AND buildlist_planet_id=".$c->id."");

				}else{

					echo "<tr><td class=\"tbldata\" width=\"150\">".$sp_arr['building_name']."</td><td class=\"tbldata\">
						<input type=\"text\" name=\"people_work[".SHIP_BUILDING_ID."]\" value=\"".$sp_arr['buildlist_people_working']."\" size=\"5\" maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\"/></td>";

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='dontwork' 
					WHERE 
						buildlist_building_id='".SHIP_BUILDING_ID."' 
						AND buildlist_planet_id='".$c->id."'");
				}
				echo "<td class=\"tbldata\">".(nf($sp_arr['buildlist_people_working']*12))." t</td></tr>";

			}


		//Abfrage Verteidigung
			$dp_res = dbquery("
			SELECT 
                buildlist_people_working,
                building_name,
                building_people_place 
			FROM 
                ".$db_table['buildlist'].",
                ".$db_table['buildings']." 
			WHERE 
                buildlist_building_id=building_id 
                AND buildlist_planet_id='".$c->id."' 
                AND building_id='".DEF_BUILDING_ID."';");
			if (mysql_num_rows($dp_res)>0)
			{
				$work_available=1;
				$dp_arr=mysql_fetch_array($dp_res);
				//$people_working += $dp_arr['buildlist_people_working'];
				if (mysql_num_rows(dbquery("
				SELECT 
					deflist_def_id 
				FROM 
                    ".$db_table['deflist']."
				WHERE 
                    deflist_planet_id='".$c->id."' 
                    AND deflist_user_id='".$s['user']['id']."' 
                    AND deflist_build_start_time>'0' 
                    AND deflist_build_end_time>'0';")
				)>0)
				{

					echo "<tr><td class=\"tbldata2\" width=\"150\">".$dp_arr['building_name']."</td>
					<td class=\"tbldata\"><input type=\"text\" readonly=\"readonly\"  value=\"".$dp_arr['buildlist_people_working']."\" size=\"5\" maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\" /></td>"; //name=\"people_work[".DEF_BUILDING_ID."]\"

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='work' 
					WHERE 
						buildlist_building_id='".DEF_BUILDING_ID."' 
						AND buildlist_planet_id='".$c->id."'");

				}else{

					echo "<tr><td class=\"tbldata\" width=\"150\">".$dp_arr['building_name']."</td><td class=\"tbldata\">
						<input type=\"text\" name=\"people_work[".DEF_BUILDING_ID."]\" value=\"".$dp_arr['buildlist_people_working']."\" size=\"5\" maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\" /></td>";

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='dontwork' 
					WHERE 
						buildlist_building_id='".DEF_BUILDING_ID."' 
						AND buildlist_planet_id='".$c->id."'");

				}
				echo "<td class=\"tbldata\">".(nf($dp_arr['buildlist_people_working']*12))." t</td></tr>";
			}


		//Abfrage Technologien
			$tp_res = dbquery("
			SELECT 
                buildlist_people_working,
                building_name,
                building_people_place 
			FROM 
                ".$db_table['buildlist'].",
                ".$db_table['buildings']." 
			WHERE 
                buildlist_building_id=building_id 
                AND buildlist_planet_id=".$c->id." 
                AND building_id='".TECH_BUILDING_ID."';");
			$work_available=0;
			if (mysql_num_rows($tp_res)>0)
			{
				$work_available=1;
				$tp_arr=mysql_fetch_array($tp_res);
				//$people_working += $tp_arr['buildlist_people_working'];
				if (mysql_num_rows(dbquery("
				SELECT 
					techlist_tech_id
				FROM 
                    ".$db_table['techlist']."
				WHERE 
                    techlist_planet_id='".$c->id."' 
                    AND techlist_user_id='".$s['user']['id']."' 
                    AND techlist_build_start_time>'0' 
                    AND techlist_build_end_time>'0';")
                )>0)
				{
					echo "<tr><td class=\"tbldata2\" width=\"150\">".$tp_arr['building_name']."</td>
					<td class=\"tbldata\"><input type=\"text\" readonly=\"readonly\" value=\"".$tp_arr['buildlist_people_working']."\" size=\"5\"  maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\" /></td>";

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='work' 
					WHERE 
						buildlist_building_id='".TECH_BUILDING_ID."' 
						AND buildlist_planet_id='".$c->id."'");

				}else{

					echo "<tr><td class=\"tbldata\" width=\"150\">".$tp_arr['building_name']."</td><td class=\"tbldata\">
						<input type=\"text\" name=\"people_work[".TECH_BUILDING_ID."]\" value=\"".$tp_arr['buildlist_people_working']."\" size=\"5\" maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\" /></td>";

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='dontwork' 
					WHERE 
						buildlist_building_id='".TECH_BUILDING_ID."' 
						AND buildlist_planet_id='".$c->id."'");

				}
				echo "<td class=\"tbldata\">".(nf($tp_arr['buildlist_people_working']*12))." t</td></tr>";
			}


		//Abfrage Bauhof
			$bp_res = dbquery("
			SELECT 
                buildlist_people_working,
                building_name,
                building_people_place 
			FROM 
                ".$db_table['buildlist'].",
                ".$db_table['buildings']." 
			WHERE 
                buildlist_building_id=building_id 
                AND buildlist_planet_id='".$c->id." '
                AND building_id='".BUILD_BUILDING_ID."';");
			if (mysql_num_rows($bp_res)>0)
			{
				$work_available=1;
				$bp_arr=mysql_fetch_array($bp_res);
				if (mysql_num_rows(dbquery("
				SELECT 
					buildlist_building_id
				FROM 
                    ".$db_table['buildlist']."
				WHERE 
                    buildlist_planet_id='".$c->id."' 
                    AND buildlist_user_id='".$s['user']['id']."' 
                    AND buildlist_build_start_time>'0'
                    AND buildlist_build_end_time>'0';")
                )>0)
				{
					echo "<tr><td class=\"tbldata2\" width=\"150\">Bauhof</td>
					<td class=\"tbldata\"><input type=\"text\" readonly=\"readonly\" name=\"people_work[".BUILD_BUILDING_ID."]\" value=\"".$bp_arr['buildlist_people_working']."\" size=\"5\"  maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\" /></td>"; // name=\"people_work[".BUILD_BUILDING_ID."]\"

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='work' 
					WHERE 
                        buildlist_building_id='".BUILD_BUILDING_ID."' 
                        AND buildlist_planet_id='".$c->id."'");

				}else{
					echo "<tr><td class=\"tbldata\" width=\"150\">Bauhof</td><td class=\"tbldata\">
						<input type=\"text\" name=\"people_work[".BUILD_BUILDING_ID."]\" value=\"".$bp_arr['buildlist_people_working']."\" size=\"5\" maxlength=\"20\" onKeyPress=\"return nurZahlen(event)\" /></td>";

					dbquery("
					UPDATE 
						".$db_table['buildlist']." 
					SET 
						buildlist_people_working_status='dontwork' 
					WHERE 
                        buildlist_building_id='".BUILD_BUILDING_ID."' 
                        AND buildlist_planet_id='".$c->id."'");

				}
				echo "<td class=\"tbldata\">".(nf($bp_arr['buildlist_people_working']*12))." t</td></tr>";
			}

			if ($work_available==1)
			
				echo "<tr><td class=\"tbldata\"&nbsp;</td>
				<td class=\"tbldata\"><input type=\"submit\" name=\"submit_people_work\" value=\"Speichern\" /></td>
			

				<td class=\"tbldata\"><input type=\"submit\" name=\"submit_people_free\" value=\"Alle Arbeiter freigeben\" /></td></tr>";
			
			infobox_end(1);
			echo "</form>";




  		// Infodaten
			$people_free = $c->people-$people_working;
			$people_div = $c->people/50 * ($conf['people_multiply']['v'] + $c->type->population + $user['race']['population'] + $c->sol->type->population -3);
			if($people_div<=3) $people_div=3;
			infobox_start("Daten",1,0);
			echo "<tr><td class=\"tbldata\" width=\"250\">Bevölkerung total</td><td class=\"tbldata\">".nf($c->people)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Arbeiter</td><td class=\"tbldata\">".nf($people_working)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Freie Leute</td><td class=\"tbldata\">".nf($people_free)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Verarbeitung pro Arbeiter und Stunde</td><td class=\"tbldata\">".$conf['people_work_done']['v']." t/h</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Nahrung pro Arbeiter und Stunde</td><td class=\"tbldata\">".$conf['people_food_require']['v']." t/h</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Grundwachstumsrate</td><td class=\"tbldata\">".get_percent_string($conf['people_multiply']['v'])."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus ".$c->type_name."</td><td class=\"tbldata\">".get_percent_string($c->type->population,1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus ".$user['race']['name']."</td><td class=\"tbldata\">".get_percent_string($user['race']['population'],1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus ".$c->sol_type_name."</td><td class=\"tbldata\">".get_percent_string($c->sol->type->population,1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus total</td><td class=\"tbldata\">".get_percent_string(array($c->type->population,$user['race']['population'],$c->sol->type->population),1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Bevölkerungszuwachs pro Stunde</td><td class=\"tbldata\">".nf($people_div)."</td></tr>";
			infobox_end(1);
		}
		else
			echo "Es sind noch keine Gebäude gebaut, in denen deine Bevölkerung wohnen oder arbeiten kann!";

	}
?>