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

		echo "<h1>Bev&ouml;lkerungs&uuml;bersicht des Planeten ".$c->name."</h1>";
		echo "<div id=\"population_info\"></div>"; // Nur zu testzwecken
		$c->resBox();

		$res = dbquery("
		SELECT
            buildings.building_store_factor,
            buildings.building_name,
            buildings.building_people_place,
            buildlist.buildlist_current_level
		FROM
            ".$db_table['buildlist'].",
            ".$db_table['buildings']."
		WHERE
            buildlist.buildlist_building_id=buildings.building_id
            AND buildlist.buildlist_planet_id=".$c->id."
            AND buildings.building_people_place>0;");
		if (mysql_num_rows($res)>0)
		{
			//
			// Wohnfläche
			//
			infobox_start("Wohnfl&auml;che",1,0);
			echo "<tr><td class=\"tbldata\">Grundwohnfl&auml;che</td><td class=\"tbldata\">".nf($conf['user_start_people']['p1'])."</td></tr>";
			$pcnt=$conf['user_start_people']['p1'];
			while ($arr=mysql_fetch_array($res))
			{
				$place = floor($arr['building_people_place'] * pow($arr['building_store_factor'],$arr['buildlist_current_level']-1));
				echo "<tr><td class=\"tbldata\">".$arr['building_name']."</td><td class=\"tbldata\">".nf($place)."</td></tr>";
				$pcnt+=$place;
			}
			echo "<tr><td class=\"tbldata\"><b>TOTAL</b></td><td class=\"tbldata\"><b>".nf($pcnt)."</b></td></tr>";
			infobox_end(1);


  		//
  		// Arbeiter zuteilen
  		//
			if ($_POST['submit_people_work']!="" && checker_verify())
			{
				//zählt gesperrte Arbeiter
        $check_res = dbquery("
        SELECT
        	SUM(buildlist_people_working)
        FROM
        	".$db_table['buildlist']."
        WHERE
        	buildlist_planet_id=".$c->id."
        	AND buildlist_people_working_status='1';");
        $check_arr = mysql_fetch_array($check_res);

				$free_people=floor($c->people)-$check_arr[0];
				if (count($_POST['people_work'])>0)
				{
					foreach ($_POST['people_work'] as $id=>$num)
					{
						$working+=nf_back($num);
					}
					$available = min($free_people,$working);
					foreach ($_POST['people_work'] as $id=>$num)
					{
						$num = nf_back($num);
						
						if ($available>0)
							$work = min($num,$available);
						else
							$work = 0;
						$available-=$num;

            dbquery("
            UPDATE
                ".$db_table['buildlist']."
            SET
                buildlist_people_working='".$work."'
            WHERE
                buildlist_building_id='".$id."'
                AND buildlist_planet_id=".$c->id."");
					}
				}
			}


			//überprüft tätigkeit des Schiffswerftes
			$sql = "
			SELECT
				COUNT(queue_id)
			FROM
				".$db_table['ship_queue']."
			WHERE
            	queue_planet_id='".$c->id."'
                AND queue_user_id='".$s['user']['id']."'
                AND queue_starttime>'0'
                AND queue_endtime>'0';";
			$tres = dbquery($sql);
			$tarr=mysql_fetch_row($tres);
			$w[SHIP_BUILDING_ID]=$tarr[0];

			//überprüft tätigkeit der waffenfabrik
			$sql = "
			SELECT
				COUNT(queue_id)
			FROM
				".$db_table['def_queue']."
			WHERE
				queue_planet_id='".$c->id."'
				AND queue_user_id='".$s['user']['id']."'
				AND queue_starttime>'0'
	            AND queue_endtime>'0';";
			$tres = dbquery($sql);
			$tarr=mysql_fetch_row($tres);
			$w[DEF_BUILDING_ID]=$tarr[0];

			//überprüft tätigkeit des forschungslabors
			$sql = "
			SELECT
				COUNT(techlist_id)
			FROM
            	".$db_table['techlist']."
			WHERE
            	techlist_planet_id='".$c->id."'
                AND techlist_user_id='".$s['user']['id']."'
                AND techlist_build_start_time>'0'
                AND techlist_build_end_time>'0';";
			$tres = dbquery($sql);
			$tarr=mysql_fetch_row($tres);
			$w[TECH_BUILDING_ID]=$tarr[0];

			//überprüft tätigkeit des bauhofes
			$sql = "
			SELECT
				COUNT(buildlist_id)
			FROM
	        	".$db_table['buildlist']."
			WHERE
	        	buildlist_planet_id='".$c->id."'
	            AND buildlist_user_id='".$s['user']['id']."'
	            AND buildlist_build_start_time>'0'
	            AND buildlist_build_end_time>'0';";
			$tres = dbquery($sql);
			$tarr=mysql_fetch_row($tres);
			$w[BUILD_BUILDING_ID]=$tarr[0];

			// Alle Arbeiter freistellen (solange sie nicht noch an einer Arbeit sind)
			if ($_POST['submit_people_free']!="" && checker_verify())
			{
				foreach ($w as $id=>$v)
				{
					if ($v==0)
					{
	  					dbquery("
	  					UPDATE
	  						".$db_table['buildlist']."
	  					SET
	  						buildlist_people_working='0'
	  					WHERE
	  						buildlist_building_id='".$id."'
	  						AND buildlist_user_id='".$s['user']['id']."'
	  						AND buildlist_planet_id='".$c->id."'");
	  				}
	  			}
			}

			echo "<form action=\"?page=$page\" method=\"post\">";
			checker_init();
			echo "Wenn einem Geb&auml;ude Arbeiter zugeteilt werden, wird es entsprechend schneller gebaut. Die Arbeiter ben&ouml;tigen jedoch Nahrung. ";
			echo "Die Zuteilung der Arbeiter kann erst ge&auml;ndert werden wenn entsprechende Bauauftr&auml;ge abgeschlossen sind. ";
			echo "Die gesamte Nahrung f&uuml;r die Arbeiter wird beim Start eines Bauvorgangs sofort vom Planetenkonto abgezogen.<br/><br/>";
			infobox_start("Arbeiter zuteilen",1,0);
			echo "<tr><th class=\"tbltitle\">Geb&auml;ude</th><th class=\"tbltitle\">Arbeiter</th><th class=\"tbltitle\">Zus&auml;tzliche Nahrung</th></tr>";

			// Gebäudede mit Arbeitsplätzen auswählen
			$sp_res = dbquery("
			SELECT
                buildlist.buildlist_people_working,
                buildings.building_name,
                buildings.building_people_place,
                buildings.building_id
			FROM
                ".$db_table['buildlist'].",
                ".$db_table['buildings']."
			WHERE
                buildlist.buildlist_building_id=buildings.building_id
                AND buildings.building_workplace='1'
                AND buildlist.buildlist_planet_id='".$c->id."'
            ORDER BY
            	buildings.building_id;");
			$work_available=false;
			if (mysql_num_rows($sp_res)>0)
			{			
				$work_available=true;
				while ($sp_arr=mysql_fetch_array($sp_res))
				{
                    echo "<tr><td class=\"tbldata\" width=\"150\">";
                    if (BUILD_BUILDING_ID==$sp_arr['building_id'])
                        echo "Bauhof";
                    else
                        echo $sp_arr['building_name'];
                    echo "</td><td class=\"tbldata\">";

                    if ($w[$sp_arr['building_id']]>0)
                    {
                        echo $sp_arr['buildlist_people_working'];

                        //Sperrt arbeiter
	  					dbquery("
	  					UPDATE
	  						".$db_table['buildlist']."
	  					SET
	  						buildlist_people_working_status='1'
	  					WHERE
	  						buildlist_building_id='".$sp_arr['building_id']."'
	  						AND buildlist_user_id='".$s['user']['id']."'
	  						AND buildlist_planet_id='".$c->id."'");
                    }
                    else
                    {
                        echo "<input type=\"text\" id=\"".$sp_arr['building_id']."\" name=\"people_work[".$sp_arr['building_id']."]\" value=\"".$sp_arr['buildlist_people_working']."\" size=\"8\" maxlength=\"20\" onKeyUp=\"FormatNumber(this.id,this.value, ".$c->people.", '', '');\"/>";
                        
                        //onKeyPress=\"return nurZahlen(event)\"
												//FormatNumber
												//xajax_formatNumbers(this.id,this.value,1,".$c->people.");
												
                        //Entsperrt arbeiter
	  					dbquery("
	  					UPDATE
	  						".$db_table['buildlist']."
	  					SET
	  						buildlist_people_working_status='0'
	  					WHERE
	  						buildlist_building_id='".$sp_arr['building_id']."'
	  						AND buildlist_user_id='".$s['user']['id']."'
	  						AND buildlist_planet_id='".$c->id."'");
                    }
                    echo "</td><td class=\"tbldata\">".(nf($sp_arr['buildlist_people_working']*12))." t</td></tr>";
				}
			}

			if ($work_available)
			{
				echo "<tr><td class=\"tbldata\"&nbsp;</td>
				<td class=\"tbldata\"><input type=\"submit\" name=\"submit_people_work\" value=\"Speichern\" /></td>
				<td class=\"tbldata\"><input type=\"submit\" name=\"submit_people_free\" value=\"Alle Arbeiter freigeben\" /></td></tr>";
			}
			infobox_end(1);
			echo "</form>";



  		// Zählt alle arbeiter die eingetragen snid (besetzt oder nicht) für die anszeige!
			$bres = dbquery("
			SELECT
				SUM(buildlist_people_working)
			FROM
				".$db_table['buildlist']."
			WHERE
				buildlist_planet_id=".$c->id.";");
			$barr = mysql_fetch_array($bres);
			$people_working = $barr[0];

  		// Infodaten
			$people_free = floor($c->people)-$people_working;
			$people_div = $c->people/50 * ($conf['people_multiply']['v'] + $c->type->population + $user['race']['population'] + $c->sol->type->population -3);
			if($people_div<=3) $people_div=3;
			infobox_start("Daten",1,0);
			echo "<tr><td class=\"tbldata\" width=\"250\">Bev&ouml;lkerung total</td><td class=\"tbldata\">".nf(floor($c->people))."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Arbeiter</td><td class=\"tbldata\">".nf($people_working)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Freie Leute</td><td class=\"tbldata\">".nf($people_free)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Verarbeitung pro Arbeiter und Stunde</td><td class=\"tbldata\">".$conf['people_work_done']['v']." t/h</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Nahrung pro Arbeiter und Stunde</td><td class=\"tbldata\">".$conf['people_food_require']['v']." t/h</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Grundwachstumsrate</td><td class=\"tbldata\">".get_percent_string($conf['people_multiply']['v'])."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus ".$c->type_name."</td><td class=\"tbldata\">".get_percent_string($c->type->population,1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus ".$user['race']['name']."</td><td class=\"tbldata\">".get_percent_string($user['race']['population'],1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus ".$c->sol_type_name."</td><td class=\"tbldata\">".get_percent_string($c->sol->type->population,1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Wachstumsbonus total</td><td class=\"tbldata\">".get_percent_string(array($c->type->population,$user['race']['population'],$c->sol->type->population),1)."</td></tr>";
			echo "<tr><td class=\"tbldata\" width=\"250\">Bev&ouml;lkerungszuwachs pro Stunde</td><td class=\"tbldata\">".nf($people_div)."</td></tr>";
			infobox_end(1);
		}
		else
			echo "Es sind noch keine Geb&auml;ude gebaut, in denen deine Bev&ouml;lkerung wohnen oder arbeiten kann!";

	}
?>