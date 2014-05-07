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
	* Welcome page and overview over all planets
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //
	echo "<h1>&Uuml;bersicht</h1>";
	
	if ($s->firstView)
	{
		if ($cfg->get("round_end")==1)
		{
			iBoxStart("Ende der Runde");
			echo "<div style=\"witdh:100%;text-align:center;\">Die Runde endet am <strong>".df($cfg->p1("round_end"))."</strong>!";
			if ($cfg->p2("round_end"))
			{
				echo " ".$cfg->p2("round_end");
			}
			echo "</div>";
			iBoxEnd();	
		}
		$res = dbquery("
		SELECT
			COUNT(failure_user_id)
		FROM
			login_failures
		WHERE
			failure_user_id=".$cu->id."
			AND failure_time > ".$cu->lastOnline."
		");
		$arr = mysql_fetch_row($res);
		if ($arr[0]>0)
		{
			iBoxStart("Fehlerhafte Logins");
			echo "<div style=\"color:red;\"><b>Seit deinem letzten Login gab es ".$arr[0]." <a href=\"?page=userconfig&amp;mode=logins\">fehlerhafte Loginversuche</a>!</b></div>";
			iBoxEnd();
		}
	}

	if ($s->sittingActive)
	{
		iBoxStart("Sitting-Modus aktiv");
		echo "Du sittest diesen Account im Auftrag von ".$cu->nick." bis ".df($s->sittingUntil).". Bitte beachte die speziellen Sittingregeln!";
		iBoxEnd();
	}

	//
	// Admin-Infos
	//
	$tm = new TextManager();
	$infoText = $tm->getText('info');
	if ($infoText->enabled && !empty($infoText->content))
	{
		iBoxStart(": Wichtige Information :");
		echo text2html($infoText->content);
		iBoxEnd();
	}
	


	tableStart("Status");
	echo "<tr>
					<th style=\"width:30%;\">Rathaus</th>
					<th style=\"width:20%;\">Eigene Flotten</th>
					<th style=\"width:20%;\">Fremde Flotten</th>
					<th style=\"width:30%;\">Forschung</th>
				</tr>";



	//
	// Ratshaus   
	//
	
		$anres=dbquery("
		SELECT
			alliance_news_id
		FROM
			alliance_news
		WHERE
			(
	      alliance_news_alliance_to_id='".$cu->allianceId."'
		    OR alliance_news_alliance_to_id = 0 
			)
	    AND alliance_news_date>'".$cu->lastOnline."'
		ORDER BY
	        alliance_news_date DESC
		LIMIT 5;");
		if (mysql_num_rows($anres)>0)
		{
			echo "<tr><td><a href=\"?page=townhall\" style=\"color:#ff0\"><b>".mysql_num_rows($anres)."</b> neue Nachrichten</a></td>";
		}
		else
		{
			echo "<tr><td>Keine neuen Nachrichten</td>";
		}



	//
	// Flotten  
	//

		//
		// Eigene Flotten
		//
		$fm = new FleetManager($cu->id,$cu->allianceId);
		$fm->loadOwn();	
		
		//Mehrere Flotten
		if ($fm->count() > 1)
		{
			echo "<td><a href=\"?page=fleets\" style=\"color:#0f0\"><b>".$fm->count()."</b> eigene Flotten</a></td>";
		}
		//Eine Flotte
		elseif ($fm->count()==1)
		{
			echo "<td><a href=\"?page=fleets\" style=\"color:#0f0\"><b>".$fm->count()."</b> eigene Flotte</a></td>";
		}
		//Keine Flotten
		else
		{
			echo "<td>Keine eigenen Flotten</td>";
		}
	
	
		//
		// Fremde Flotten
		//
		$fm->loadForeign();
		//Mehrere Flotten
		if ($fm->count() > 1)
		{
			echo "<td><a href=\"?page=fleets\" style=\"".$fm->attitude()."\"><b>".$fm->count()."</b> fremde Flotten</a></td>";
		}
		//Eine Flotte
		elseif ($fm->count()==1)
		{
			echo "<td><a href=\"?page=fleets\" style=\"".$fm->attitude()."\"><b>".$fm->count()."</b> fremde Flotte</a></td>";
		}
		//Keine Flotten
		else
		{
			echo "<td>Keine fremden Flotten</td>";
		}



	//
	// Technologien   
	//
	
		//Lädt forschende Tech
	  $bres = dbquery("
	  SELECT
	      technologies.tech_name,
	      techlist.techlist_build_end_time,
	      techlist.techlist_entity_id
	  FROM
	      techlist
	      INNER JOIN
	      technologies
	      ON technologies.tech_id=techlist.techlist_tech_id
	      AND techlist.techlist_user_id='".$cu->id."'
	      AND techlist.techlist_build_type>'0';");
		if (mysql_num_rows($bres)>0)
		{
			$barr = mysql_fetch_array($bres);
			echo "<td><a href=\"?page=research&amp;change_entity=".$barr['techlist_entity_id']."\" id=\"tech_counter\">";
			//Forschung ist fertig
			if($barr['techlist_build_end_time']-time()<=0)
			{
				echo "".$barr['tech_name']." Fertig";
			}
			//Noch am forschen
			else
			{
				echo startTime($barr['techlist_build_end_time']-time(), 'tech_counter', 0, ''.$barr['tech_name'].' TIME');
			}
	
			echo "</a></td></tr>";
		}
		else
		{
			echo "<td>Es wird nirgendwo geforscht!</td></tr>";
		}
		
		//
		// Allianzegebäude 
		//
		
		if($cu->allianceId!=0)
		{
			
			echo "<tr>
							<th>Allianzgebäude</th>
							<th>Supportflotten</th>
							<th>Allianzangriffe</th>
							<th>Allianzforschungen</th>
						</tr>
						<tr>";
						
			// Lädt bauende Allianzgebäude
		  $res = dbquery("
		  SELECT
		    alliance_building_name,
		    alliance_buildlist_build_end_time
		  FROM
		      alliance_buildlist
		    INNER JOIN
		      alliance_buildings
		    ON alliance_building_id=alliance_buildlist_building_id
		  WHERE
		    alliance_buildlist_alliance_id='".$cu->allianceId."'
		    AND alliance_buildlist_build_end_time>'0';");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<td>
								<a href=\"?page=alliance&amp;action=base&amp;action2=buildings\" id=\"alliance_building_counter\">";
								
								//Forschung ist fertig
								if($arr['alliance_buildlist_build_end_time']-time()<=0)
								{
									echo "".$arr['alliance_building_name']." Fertig";
								}
								//Noch am forschen
								else
								{
									echo startTime($arr['alliance_buildlist_build_end_time']-time(), 'alliance_building_counter', 0, ''.$arr['alliance_building_name'].' TIME');
								}
		
					echo "</a>
							</td>";
			}
			else
			{
				echo "<td>Es wird nichts gebaut!</td>";
			}	
			
		//
		// Supportflotten Flotten
		//
		$fm->loadAllianceSupport();
		//Mehrere Flotten
		if ($fm->count() > 1)
		{
			echo "<td><a href=\"?page=fleets&mode=alliance\"><b>".$fm->count()."</b> Supportflotten</a></td>";
		}
		//Eine Flotte
		elseif ($fm->count()==1)
		{
			echo "<td><a href=\"?page=fleets&mode=alliance\"><b>".$fm->count()."</b> Supportflotte</a></td>";
		}
		//Keine Flotten
		else
		{
			echo "<td>Keine Supportflotten</td>";
		}
		
		//
		// Allianzangriffs
		//
		$fm->loadAllianceAttacks();
		//Mehrere Flotten
		if ($fm->count() > 1)
		{
			echo "<td><a href=\"?page=fleets&mode=alliance\"><b>".$fm->count()."</b> Allianzangriffe</a></td>";
		}
		//Eine Flotte
		elseif ($fm->count()==1)
		{
			echo "<td><a href=\"?page=fleets&mode=alliance\"><b>".$fm->count()."</b> Allianzangriff</a></td>";
		}
		//Keine Flotten
		else
		{
			echo "<td>Keine Allianzangriffe</td>";
		}
				
				
			// Lädt forschende Allianztech
		  $res = dbquery("
		  SELECT
		    alliance_tech_name,
		    alliance_techlist_build_end_time
		  FROM
		      alliance_techlist
		    INNER JOIN
		      alliance_technologies
		    ON alliance_tech_id=alliance_techlist_tech_id
		  WHERE
		    alliance_techlist_alliance_id='".$cu->allianceId."'
		    AND alliance_techlist_build_end_time>'0';");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				echo "<td>
								<a href=\"?page=alliance&amp;action=base&amp;action2=research\" id=\"alliance_tech_counter\">";
								
								//Forschung ist fertig
								if($arr['alliance_techlist_build_end_time']-time()<=0)
								{
									echo "".$arr['alliance_tech_name']." Fertig";
								}
								//Noch am forschen
								else
								{
									echo startTime($arr['alliance_techlist_build_end_time']-time(), 'alliance_tech_counter', 0, ''.$arr['alliance_tech_name'].' TIME');
								}
		
					echo "</a>
							</td>";
			}
			else
			{
				echo "<td>Es wird nichts geforscht!</td>";
			}
		}
		
		echo "</tr>";
		
		tableEnd();




	//
	// Javascript für dynamischen Planetkreis   
	//


    ?>
    <script type="text/javascript">
        function show_info(
        planet_id,
        planet_name,
        building_name,
        building_time,
        shipyard_name,
        shipyard_time,
        defense_name,
        defense_time,
        people,
        res_metal,
        res_crystal,
        res_plastic,
        res_fuel,
        res_food,
        use_power,
        prod_power,
        store_metal,
        store_crystal,
        store_plastic,
        store_fuel,
        store_food,
        people_place)
        {

						//Planetinfo Anzeigen
            document.getElementById("planet_info_name").firstChild.nodeValue=planet_name;

            document.getElementById("planet_info_building_name").firstChild.nodeValue=building_name;
            document.getElementById("planet_info_building_time").firstChild.nodeValue=building_time;

            document.getElementById("planet_info_shipyard_name").firstChild.nodeValue=shipyard_name;
            document.getElementById("planet_info_shipyard_time").firstChild.nodeValue=shipyard_time;

            document.getElementById("planet_info_defense_name").firstChild.nodeValue=defense_name;
            document.getElementById("planet_info_defense_time").firstChild.nodeValue=defense_time;
			
			//Überprüfen ob Speicher voll ist
			var check_metal = store_metal-res_metal;
			var check_crystal = store_crystal-res_crystal;
			var check_plastic = store_plastic-res_plastic;
			var check_fuel = store_fuel-res_fuel;
			var check_food = store_food-res_food;
			var check_people = people_place-people;
	
			var rest_power = prod_power-use_power;

			//Wenn Speicher voll, anders darstellen als normal
			if (check_metal<=0)
			{
				document.getElementById("planet_info_res_metal").className='resfullcolor';
			}
			else
			{
				document.getElementById("planet_info_res_metal").className='resmetalcolor';
			}

			if (check_crystal<=0)
			{
				document.getElementById("planet_info_res_crystal").className='resfullcolor';
			}
			else
			{
				document.getElementById("planet_info_res_crystal").className='rescrystalcolor';
			}

			if (check_plastic<=0)
			{
				document.getElementById("planet_info_res_plastic").className='resfullcolor';
			}
			else
			{
				document.getElementById("planet_info_res_plastic").className='resplasticcolor';
			}

			if (check_fuel<=0)
			{
				document.getElementById("planet_info_res_fuel").className='resfullcolor';
			}
			else
			{
				document.getElementById("planet_info_res_fuel").className='resfuelcolor';
			}

			if (check_food<=0)
			{
				document.getElementById("planet_info_res_food").className='resfullcolor';
			}
			else
			{
				document.getElementById("planet_info_res_food").className='resfoodcolor';
			}

			if (check_people<=0)
			{
				document.getElementById("planet_info_people").className='resfullcolor';
			}
			else
			{
				document.getElementById("planet_info_people").className='respeoplecolor';
			}

			if (rest_power<=0)
			{
				document.getElementById("planet_info_power").className='resfullcolor';
			}
			else
			{
				document.getElementById("planet_info_power").className='respowercolor';
			}


            var res_metal = format(res_metal);
            var res_crystal = format(res_crystal);
            var res_plastic = format(res_plastic);
            var res_fuel = format(res_fuel);
            var res_food = format(res_food);
            var people = format(people);
            var use_power = format(use_power);

            var store_metal = format(store_metal);
            var store_crystal = format(store_crystal);
            var store_plastic = format(store_plastic);
            var store_fuel = format(store_fuel);
            var store_food = format(store_food);
            var people_place = format(people_place);
            var prod_power = format(prod_power);

            if (rest_power>=0)
            {
            	var rest_power = format(rest_power);
            }
            else
            {
            	var rest_power ='-'+format(Math.abs(rest_power));
            }


						//Roshtoff Anzeigen
            document.getElementById("planet_info_res_metal").firstChild.nodeValue=''+res_metal+' t';
            document.getElementById("planet_info_res_crystal").firstChild.nodeValue=''+res_crystal+' t';
            document.getElementById("planet_info_res_plastic").firstChild.nodeValue=''+res_plastic+' t';
            document.getElementById("planet_info_res_fuel").firstChild.nodeValue=''+res_fuel+' t';
            document.getElementById("planet_info_res_food").firstChild.nodeValue=''+res_food+' t';
            document.getElementById("planet_info_power").firstChild.nodeValue=rest_power;
            document.getElementById("planet_info_people").firstChild.nodeValue=people;
			

			//Alle Beschriftungen anzeigen
			document.getElementById("planet_info_text_building").innerHTML ='<a href=\"?page=buildings&change_entity='+planet_id+'\">Bauhof:</a>';
			document.getElementById("planet_info_text_shipyard").innerHTML ='<a href=\"?page=shipyard&change_entity='+planet_id+'\">Schiffswerft:</a>';
			document.getElementById("planet_info_text_defense").innerHTML ='<a href=\"?page=defense&change_entity='+planet_id+'\">Waffenfabrik:</a>';
			document.getElementById("planet_info_text_res").firstChild.nodeValue='Ressourcen';
			document.getElementById("planet_info_text_res_metal").className='resmetalcolor';
			document.getElementById("planet_info_text_res_crystal").className='rescrystalcolor';
			document.getElementById("planet_info_text_res_plastic").className='resplasticcolor';
			document.getElementById("planet_info_text_res_fuel").className='resfuelcolor';
			document.getElementById("planet_info_text_res_food").className='resfoodcolor';
			document.getElementById("planet_info_text_people").className='respeoplecolor';
			document.getElementById("planet_info_text_power").className='respowercolor';
			document.getElementById("planet_info_text_res_metal").firstChild.nodeValue='<?php echo RES_METAL.":";?>';
			document.getElementById("planet_info_text_res_crystal").firstChild.nodeValue='<?php echo RES_CRYSTAL.":";?>';
			document.getElementById("planet_info_text_res_plastic").firstChild.nodeValue='<?php echo RES_PLASTIC.":";?>';
			document.getElementById("planet_info_text_res_fuel").firstChild.nodeValue='<?php echo RES_FUEL.":";?>';
			document.getElementById("planet_info_text_res_food").firstChild.nodeValue='<?php echo RES_FOOD.":";?>';
			document.getElementById("planet_info_text_people").firstChild.nodeValue='Bewohner:';
			document.getElementById("planet_info_text_power").firstChild.nodeValue='Energie:';
        }

				//Formatiert Zahlen (der PHP Skript will nicht gehen)
        function format(nummer)
        {
            var nummer = '' + nummer;
            var laenge = nummer.length;
            if (laenge > 3) {
            var mod = laenge % 3;
            var output = (mod > 0 ?
            (nummer.substring(0,mod)) : '');
            for (i=0 ; i < Math.floor(laenge / 3); i++) {
            if ((mod == 0) && (i == 0))
            output += nummer.substring(mod+ 3 * i,
            mod + 3 * i + 3); else
            output+= '`' + nummer.substring(mod + 3 * i,
            mod + 3 * i + 3); } return (output); }
            else return nummer;
        }
    </script>
    <?PHP


	//
	// Planetkreis   
	//

	//Kreis Definitionen
	$division=15;			//Kreis Teilung: So hoch wie die maximale Anzahl Planeten
	$d_planets = $cu->properties->planetCircleWidth;	//Durchmesser der Bilder (in Pixel)
	$d_infos = $cu->properties->planetCircleWidth;		//Durchmesser der Infos (in Pixel)
	$pic_height=75;			//Planet Bildhöhe
	$pic_width=75;			//Planet Bildbreite
	$info_box_height=50;	//Info Höhe
	$info_box_width=150;	//Info Breite
	$degree=0;				//Winkel des Startplanetes (0=Senkrecht (Oben))

	$middle_left=$d_planets/2-$pic_height/2;
	$middle_top=$d_planets/2-$pic_width/2;
	$absolute_width=$d_infos+$info_box_width+$pic_width;
	$absolute_height=$d_infos+$info_box_height+$pic_height;
	
	//Abstand
	echo "<br><br><br><br><br><br><br><br>";
	echo "<center>";
	echo "<div align=\"center\" style=\"position:relative; left:0px; top:0px; width:".$absolute_width."px; height:".$absolute_height."px; vertical-align:middle;\">
	";

	echo "<div align=\"center\" style=\"position:relative; left:0px; top:0px; width:".$d_planets."px; height:".$d_planets."px; text-align:center; vertical-align:middle;\" id=\"planet_circle_inner_container\">
	";

    //Liest alle Planeten des Besitzers aus und gibt benötigte infos
    $psql = "
    SELECT
    	p.planet_name,
      p.id,
      p.planet_image,
      p.planet_people,
      p.planet_res_metal,
      p.planet_res_crystal,
      p.planet_res_plastic,
      p.planet_res_fuel,
      p.planet_res_food,
      p.planet_use_power,
      p.planet_prod_power,
      p.planet_store_metal,
      p.planet_store_crystal,
      p.planet_store_plastic,
      p.planet_store_fuel,
      p.planet_store_food,
      p.planet_people_place
    FROM
    	planets AS p
   	WHERE
   		p.planet_user_id=".$cu->id."
    ORDER BY
      p.planet_user_main DESC,
      p.planet_name;";
		$res_planet = dbquery($psql);
	
		
		while ($arr_planet = mysql_fetch_array($res_planet))
		{
			if ($arr_planet['planet_name']!="")
			{
				$planet_name = $arr_planet['planet_name'];
			}
			else 
			{
				$planet_name = "Unbenannt";
			}
			
      // Bauhof infos
      $res_building = dbquery("
      SELECT
        b.building_name,
        bl.buildlist_build_end_time,
        bl.buildlist_current_level,
        bl.buildlist_build_type
      FROM
        buildlist AS bl
        INNER JOIN
        buildings AS b
        ON b.building_id=bl.buildlist_building_id
        AND bl.buildlist_entity_id='".$arr_planet['id']."'
        AND bl.buildlist_build_type>'0'");

      if (mysql_num_rows($res_building)>0)
      {
        $arr_building = mysql_fetch_array($res_building);

        //infos über den Bauhof
        $building_rest_time=$arr_building['buildlist_build_end_time']-time();
        $building_h=floor($building_rest_time/3600);
        $building_m=floor(($building_rest_time-$building_h*3600)/60);
        $building_s=$building_rest_time-$building_h*3600-$building_m*60;
        $building_zeit="(".$building_h."h ".$building_m."m ".$building_s."s)";

        $building_time = $building_zeit;
        $building_name =  $arr_building['building_name'];
        
        // Zeigt Ausbaulevel bei Abriss
        if($arr_building['buildlist_build_type'] == 2)
        {
        	$building_level =  $arr_building['buildlist_current_level']-1;
        }
        // Bei Ausbau
        else
        {
        	$building_level =  $arr_building['buildlist_current_level']+1;
        }
        
        if($building_rest_time<=0)
        {
        	$building_time="Fertig";
        }
      }
      else
      {
        $building_time = "";
        $building_rest_time = "";
        $building_name = "";
        $building_level = "";
      }


			// Schiffswerft infos
			$res_shipyard = dbquery("
			SELECT
    		ship_name,
    		queue_cnt,
    		queue_starttime,
    		queue_endtime,
    		queue_objtime
			FROM
    		ship_queue
    	INNER JOIN
    		ships
    		ON queue_ship_id=ship_id
  			AND queue_entity_id='".$arr_planet['id']."'
  			AND queue_endtime>'".time()."'
    	ORDER BY
				queue_starttime ASC
			LIMIT 1;");
      if (mysql_num_rows($res_shipyard)>0)
      {
      	$arr_shipyard = mysql_fetch_array($res_shipyard);
      	
      	//Verbleibende Zeit bis zur fertigstellung des aktuellen Auftrages
      	$shipyard_rest_time[$arr_planet['id']] = $arr_shipyard['queue_endtime']-time();
      	//Schiffsname
      	$shipyard_name[$arr_planet['id']] =  $arr_shipyard['ship_name'];
      	
        //infos über den raumschiffswerft
        $shipyard_h=floor($shipyard_rest_time[$arr_planet['id']]/3600);
        $shipyard_m=floor(($shipyard_rest_time[$arr_planet['id']]-$shipyard_h*3600)/60);
        $shipyard_s=$shipyard_rest_time[$arr_planet['id']]-$shipyard_h*3600-$shipyard_m*60;
        $shipyard_zeit[$arr_planet['id']]="(".$shipyard_h."h ".$shipyard_m."m ".$shipyard_s."s)";

        $shipyard_time[$arr_planet['id']] = $shipyard_zeit[$arr_planet['id']];
        if($shipyard_rest_time[$arr_planet['id']]<=0)
        {
            $shipyard_time[$arr_planet['id']]="Fertig";
        }
      }
      else
      {
        $shipyard_time[$arr_planet['id']] = "";
        $shipyard_name[$arr_planet['id']] = "";
      }

      // waffenfabrik infos
      $res_defense = dbquery("
			SELECT
    		def_name,
    		queue_cnt,
    		queue_starttime,
    		queue_endtime,
    		queue_objtime
			FROM
    		def_queue
    	INNER JOIN
    		defense
    		ON queue_def_id=def_id
  			AND queue_entity_id='".$arr_planet['id']."'
  			AND queue_endtime>'".time()."'
    	ORDER BY
				queue_starttime ASC
			LIMIT 1;");
      if (mysql_num_rows($res_defense)>0)
      {
      	$arr_defense = mysql_fetch_array($res_defense);

      	//Verbleibende Zeit bis zur fertigstellung des aktuellen Auftrages
      	$defense_rest_time[$arr_planet['id']] = $arr_defense['queue_endtime']-time();
      	//Defname
      	$defense_name[$arr_planet['id']] = $arr_defense['def_name'];

        // Infos über die Waffenfabrik
        $defense_h=floor($defense_rest_time[$arr_planet['id']]/3600);
        $defense_m=floor(($defense_rest_time[$arr_planet['id']]-$defense_h*3600)/60);
        $defense_s=$defense_rest_time[$arr_planet['id']]-$defense_h*3600-$defense_m*60;
        $defense_zeit[$arr_planet['id']]="(".$defense_h."h ".$defense_m."m ".$defense_s."s)";

        $defense_time[$arr_planet['id']] = $defense_zeit[$arr_planet['id']];
      	$defense_name[$arr_planet['id']] = $defense_name[$arr_planet['id']];
        if($defense_rest_time[$arr_planet['id']]<=0)
        {
            $defense_time[$arr_planet['id']]="Fertig";
        }
      }
      else
      {
        $defense_time[$arr_planet['id']] = "";
        $defense_name[$arr_planet['id']] = "";
      }
	
            // TODO
			$planet_info = "<b class=\"planet_name\">".htmlspecialchars($planet_name, ENT_QUOTES, 'UTF-8')."</b><br>
			".$building_name." ".$building_level."
			";
			$planet_image_path = "".IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr_planet['planet_image']."_middle.gif";
	
			// Planet bild mit link zum bauhof und der informationen übergabe beim mouseover
	    $planet_link = "<a href=\"?page=buildings&change_entity=".$arr_planet['id']."\"><img id=\"Planet\" src=\"".$planet_image_path."\" width=\"".$pic_width."\" height=\"".$pic_height."\" border=\"0\" 
	    onMouseOver=\"show_info(
			'".$arr_planet['id']."',
			'".StringUtils::encodeDBStringToJS($planet_name)."',
			'".$building_name."',
			'".$building_time."',
			'".$shipyard_name[$arr_planet['id']]."',
			'".$shipyard_time[$arr_planet['id']]."',
			'".$defense_name[$arr_planet['id']]."',
			'".$defense_time[$arr_planet['id']]."',
			'".floor($arr_planet['planet_people'])."',
			'".floor($arr_planet['planet_res_metal'])."',
			'".floor($arr_planet['planet_res_crystal'])."',
			'".floor($arr_planet['planet_res_plastic'])."',
			'".floor($arr_planet['planet_res_fuel'])."',
			'".floor($arr_planet['planet_res_food'])."',
			'".floor($arr_planet['planet_use_power'])."',
			'".floor($arr_planet['planet_prod_power'])."',
			'".floor($arr_planet['planet_store_metal'])."',
			'".floor($arr_planet['planet_store_crystal'])."',
			'".floor($arr_planet['planet_store_plastic'])."',
			'".floor($arr_planet['planet_store_fuel'])."',
			'".floor($arr_planet['planet_store_food'])."',
			'".floor($arr_planet['planet_people_place'])."'
			);\"/></a>
			";
	
	
			if($degree==0)
				$text="center";
			elseif($degree>0 && $degree<=180)
				$text="left";
			else
				$text="right";
	
			$left2=$middle_left+(($d_planets/2)*cos(deg2rad($degree+270)));
			$top2=$middle_top+(($d_planets/2)*sin(deg2rad($degree+270)));
	
			echo "<div style=\"position:absolute; left:".$left2."px; top:".$top2."px; text-align:center; vertical-align:middle;\">".$planet_link."</div>
			";
	
			if($degree==0)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))-($info_box_width-$pic_width)/2;
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)))-$info_box_height;
			}
			elseif($degree>0 && $degree<=45)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))+$pic_width;
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)))-$pic_height/2;
			}
			elseif($degree>45 && $degree<135)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))+$pic_width;
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)));
			}
			elseif($degree>=135 && $degree<160)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))+$pic_width;
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)))+$pic_height/2;
			}
			elseif($degree>=160 && $degree<180)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))+15;
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)))+$pic_height;
			}
			elseif($degree>=180 && $degree<=210)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))-($info_box_width+15-$pic_width);
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)))+$pic_height;
			}
			elseif($degree>210 && $degree<=225)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))-$pic_width-($info_box_width-$pic_width);
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)))+$pic_height/2;
			}
			elseif($degree>225 && $degree<315)
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))-$pic_width-($info_box_width-$pic_width);
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)));
			}
			else //315<$degree<360
			{
				$left=$middle_left+(($d_infos/2)*cos(deg2rad($degree+270)))-$pic_width-($info_box_width-$pic_width);
				$top=$middle_top+(($d_infos/2)*sin(deg2rad($degree+270)))-$pic_height/2;
			}
	
			echo "<div id=\"planet_info_".$arr_planet['id']."\" style=\"position:absolute; left:".$left."px; top:".$top."px; width:".$info_box_width."px; height:".$info_box_height."px; text-align:".$text."; vertical-align:middle;\">
			";
			
			echo $planet_info;
			echo '<span id="planet_timer_'.$arr_planet['id'].'">';
			
			// Stellt Zeit Counter dar, wenn ein Gebäude in bau ist
			if(isset($building_rest_time) && $building_rest_time>0)
			{
				echo startTime($building_rest_time, "planet_timer_".$arr_planet['id']."", 0, "<br>(TIME)")."";
			}
			
			echo "</span></div>
			";
			$degree = $degree + (360/$division);
		}


	$top_table=$middle_top+(($d_planets/2)*sin(deg2rad(55+270)));
	echo "<center><table border=\"0\" width=\"65%\" style=\"text-align:center; vertical-align:middle;\">";
	echo "
			<tr height=\"".$top_table."\">
				<td colspan=\"3\">&nbsp;</td>
			</tr>
			<tr>
				<td class=\"PlaniTextCenterPlanetname\" id=\"planet_info_name\" colspan=\"3\" style=\"text-align:center;\">&nbsp;</td>
			</tr>

			<tr>
				<td colspan=\"3\">&nbsp;</td>
			</tr>

			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_building\">&nbsp;</div></td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_building_name\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_building_time\">&nbsp;</td></tr>

			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_shipyard\">&nbsp;</div></td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_shipyard_name\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_shipyard_time\">&nbsp;</td>
			</tr>

			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_defense\">&nbsp;</div></td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_defense_name\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_defense_time\">&nbsp;</td>
			</tr>

			<tr height=\"10\">
				<td colspan=\"3\">&nbsp;</td>
			</tr>
			<tr>
				<td colspan=\"3\" class=\"PlaniTextCenterRessourcen\" id=\"planet_info_text_res\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"28%\" id=\"planet_info_text_res_metal\" >&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_metal\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" id=\"planet_info_text_res_crystal\" >&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_crystal\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" id=\"planet_info_text_res_plastic\" >&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_plastic\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" id=\"planet_info_text_res_fuel\" >&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_fuel\">&nbsp;</td>
			</tr>
			<tr> 
				<td width=\"38%\" id=\"planet_info_text_res_food\" >&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_food\">&nbsp;</td>
			</tr>

			<tr>
				<td width=\"38%\" id=\"planet_info_text_people\" >&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_people\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" id=\"planet_info_text_power\" >&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_power\">&nbsp;</td>
			</tr>

	</table></center>";

echo "</div></div></center>
";

?>
