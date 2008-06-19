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
	// 	File: overview.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Welcome page and overview over all planets
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// DEFINITIONEN //
	require_once("inc/fleet_action.inc.php");
	

	// BEGIN SKRIPT //
	echo "<h1>&Uuml;bersicht</h1>";

	if ($firstview)
	{
		$res = dbquery("
		SELECT
			COUNT(failure_user_id)
		FROM
			login_failures
		WHERE
			failure_user_id=".$cu->id()."
			AND failure_time > ".$cu->last_online."
		");
		$arr = mysql_fetch_row($res);
		if ($arr[0]>0)
		{
			infobox_start("Fehlerhafte Logins");
			echo "<div style=\"color:red;\"><b>Seit deinem letzten Login gab es ".$arr[0]." <a href=\"?page=userconfig&amp;mode=logins\">fehlerhafte Loginversuche</a>!</b></div>";
			infobox_end();
		}
	}

	//
	// Admin-Infos
	//
	if ($conf['info']['v']!="")
	{
		infobox_start(": Wichtige Information :");
		echo text2html($conf['info']['v']);
		infobox_end();
	}

	echo "<table class=\"tb\" style=\"margin:0px auto;width:100%\">";
	echo "<tr><th>Rathaus</th><th>Eigene Flotten</th><th>Fremde Flotten</th><th>Forschung</th></tr>";



	/*************
	* Ratshaus   *
	*************/
	
		$anres=dbquery("
		SELECT
			alliance_news_id
		FROM
			".$db_table['alliance_news']."
		WHERE
			(
	      alliance_news_alliance_to_id='".$cu->alliance_id."'
		    OR alliance_news_alliance_to_id = 0 
			)
	    AND alliance_news_date>'".$cu->last_online."'
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



	/***********
	* Flotten  *
	************/
/*
		//
		// Eigene Flotten
		//
		$fres = dbquery("
		SELECT 
			fleet_id 
		FROM 
			fleet 
		WHERE 
			fleet_user_id='".$cu->id()." '
		ORDER BY 
			fleet_landtime ASC;");
		//Mehrere Flotten
		if (mysql_num_rows($fres)>1)
		{
			echo "<td><a href=\"?page=fleets\" style=\"color:#0f0\"><b>".mysql_num_rows($fres)."</b> eigene Flotten</a></td>";
		}
		//Eine Flotte
		elseif (mysql_num_rows($fres)==1)
		{
			echo "<td><a href=\"?page=fleets\" style=\"color:#0f0\"><b>".mysql_num_rows($fres)."</b> eigene Flotte</a></td>";
		}
		//Keine Flotten
		else
		{
			echo "<td>Keine eigenen Flotten</td>";
		}
	
	
		//
		// Fremde Flotten
		//
		$num_friend=check_fleet_incomming_friendly($cu->id()); //Nicht feindliche Flotten
		$num=check_fleet_incomming($cu->id()); //Feindliche Flotten
		$all = $num + $num_friend;
		//Nur feindliche Flotten -> rot
		if($num>0 && $num_friend==0)
		{
			$style="color:#f00";
		}
		//Feindlich- und friedlich gesinnte Flotten -> organge
		elseif($num>0 && $num_friend>0)
		{
			$style="color:orange";
		}
		//Freundliche Flotten -> grün
		elseif($num==0 && $num_friend>0)
		{
			$style="color:#0f0";
		}
	*/
		//Mehrere fremde Flotten
	  if ($all>1)
	  {
	      echo "<td><a href=\"?page=fleets\" style=\"".$style."\"><b>".$all."</b> fremde Flotten</a></td>";
	  }
	  //Eine fremde Flotte
	  elseif($all==1)
	  {
	      echo "<td><a href=\"?page=fleets\" style=\"".$style."\"><b>".$all."</b> fremde Flotte</a></td>";
	  }
	  //Keine fremde Flotten
	  else
	  {
	  	echo "<td>Keine fremden Flotten</td>";
	  }



	/*****************
	* Technologien   *
	******************/
	
		//Lädt forschende Tech
	  $bres = dbquery("
	  SELECT
	      technologies.tech_name,
	      techlist.techlist_build_end_time,
	      techlist.techlist_planet_id
	  FROM
	      ".$db_table['techlist']."
	      INNER JOIN
	      ".$db_table['technologies']."
	      ON technologies.tech_id=techlist.techlist_tech_id
	      AND techlist.techlist_user_id='".$cu->id()."'
	      AND techlist.techlist_build_type>'0';");
		if (mysql_num_rows($bres)>0)
		{
			$barr = mysql_fetch_array($bres);
			echo "<td><a href=\"?page=research&amp;planet_id=".$barr['techlist_planet_id']."\" style=\"color:#ff0\">";
			//Forschung ist fertig
			if($barr['techlist_build_end_time']-time()<=0)
			{
				echo "".$barr['tech_name']." Fertig";
			}
			//Noch am forschen
			else
			{
				echo "".$barr['tech_name']." ".tf($barr['techlist_build_end_time']-time())."";
			}
	
			echo "</a></td>";
		}
		else
		{
			echo "<td>Es wird nirgendwo geforscht!</td></tr>";
		}
		echo "</table><br/>";




	/*******************************************
	* Javascript für dynamischen Planetkreis   *
	********************************************/


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


						//Alle Beschriftungen anzeigen
						document.getElementById("planet_info_text_building").innerHTML ='<a href=\"?page=buildings&planet_id='+planet_id+'\">Bauhof:</a>';
        		document.getElementById("planet_info_text_shipyard").innerHTML ='<a href=\"?page=shipyard&planet_id='+planet_id+'\">Schiffswerft:</a>';
        		document.getElementById("planet_info_text_defense").innerHTML ='<a href=\"?page=defense&planet_id='+planet_id+'\">Waffenfabrik:</a>';
						document.getElementById("planet_info_text_res").firstChild.nodeValue='Ressourcen';
						document.getElementById("planet_info_text_res_metal").firstChild.nodeValue='<?php echo RES_METAL.":";?>';
						document.getElementById("planet_info_text_res_crystal").firstChild.nodeValue='<?php echo RES_CRYSTAL.":";?>';
						document.getElementById("planet_info_text_res_plastic").firstChild.nodeValue='<?php echo RES_PLASTIC.":";?>';
						document.getElementById("planet_info_text_res_fuel").firstChild.nodeValue='<?php echo RES_FUEL.":";?>';
						document.getElementById("planet_info_text_res_food").firstChild.nodeValue='<?php echo RES_FOOD.":";?>';
						document.getElementById("planet_info_text_people").firstChild.nodeValue='Bewohner:';
						document.getElementById("planet_info_text_power").firstChild.nodeValue='Energie:';


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
	        		document.getElementById("planet_info_res_metal").className='PlaniTextCenterStore';
	        	}
	        	else
	        	{
	        		document.getElementById("planet_info_res_metal").className='PlaniTextCenter';
	        	}
	
	        	if (check_crystal<=0)
	        	{
	        		document.getElementById("planet_info_res_crystal").className='PlaniTextCenterStore';
	        	}
	        	else
	        	{
	        		document.getElementById("planet_info_res_crystal").className='PlaniTextCenter';
	        	}
	
	        	if (check_plastic<=0)
	        	{
	        		document.getElementById("planet_info_res_plastic").className='PlaniTextCenterStore';
	        	}
	        	else
	        	{
	        		document.getElementById("planet_info_res_plastic").className='PlaniTextCenter';
	        	}
	
	        	if (check_fuel<=0)
	        	{
	        		document.getElementById("planet_info_res_fuel").className='PlaniTextCenterStore';
	        	}
	        	else
	        	{
	        		document.getElementById("planet_info_res_fuel").className='PlaniTextCenter';
	        	}
	
	        	if (check_food<=0)
	        	{
	        		document.getElementById("planet_info_res_food").className='PlaniTextCenterStore';
	        	}
	        	else
	        	{
	        		document.getElementById("planet_info_res_food").className='PlaniTextCenter';
	        	}
	
	        	if (check_people<=0)
	        	{
	        		document.getElementById("planet_info_people").className='PlaniTextCenterStore';
	        	}
	        	else
	        	{
	        		document.getElementById("planet_info_people").className='PlaniTextCenter';
	        	}
	
	        	if (rest_power<=0)
	        	{
	        		document.getElementById("planet_info_power").className='PlaniTextCenterStore';
	        	}
	        	else
	        	{
	        		document.getElementById("planet_info_power").className='PlaniTextCenterPower';
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

            show_links();

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


	/*****************
	* Planetkreis    *
	******************/

	//Kreis Definitionen
	$division=15;			//Kreis Teilung: So hoch wie die maximale Anzahl Planeten
	$d_planets = $cu->planet_circle_width;	//Durchmesser der Bilder (in Pixel)
	$d_infos = $cu->planet_circle_width;		//Durchmesser der Infos (in Pixel)
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
	echo "<div align=\"center\" style=\"position:relative; left:0px; top:0px; width:".$absolute_width."px; height:".$absolute_height."px; vertical-align:middle;\">";

	echo "<div align=\"center\" style=\"position:relative; left:0px; top:0px; width:".$d_planets."px; height:".$d_planets."px; text-align:center; vertical-align:middle;\">";

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
   		p.planet_user_id=".$cu->id()."
    ORDER BY
      p.planet_user_main DESC,
      p.planet_name;";
		$res_planet = dbquery($psql);
	
		
		while ($arr_planet = mysql_fetch_array($res_planet))
		{
      // Bauhof infos
      $res_building = dbquery("
      SELECT
        b.building_name,
        bl.buildlist_build_end_time,
        bl.buildlist_current_level,
        bl.buildlist_build_type
      FROM
        ".$db_table['buildlist']." AS bl
        INNER JOIN
        ".$db_table['buildings']." AS b
        ON b.building_id=bl.buildlist_building_id
        AND bl.buildlist_planet_id='".$arr_planet['id']."'
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
    		".$db_table['ship_queue']."
    	INNER JOIN
    		".$db_table['ships']."
    		ON queue_ship_id=ship_id
  			AND queue_planet_id='".$arr_planet['id']."'
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
    		".$db_table['def_queue']."
    	INNER JOIN
    		".$db_table['defense']."
    		ON queue_def_id=def_id
  			AND queue_planet_id='".$arr_planet['id']."'
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

        //infos über den raumschiffswerft
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
	
			$planet_info = "<b>".$arr_planet['planet_name']."</b><br>".$building_name." ".$building_level."<br>".$building_time."";
			$planet_image_path = "".IMAGE_PATH."/".IMAGE_PLANET_DIR."/planet".$arr_planet['planet_image']."_middle.gif";
	
	
			// Planet bild mit link zum bauhof und der informationen übergabe beim mouseover
	    $planet_link = "<a href=\"?page=buildings&planet_id=".$arr_planet['id']."\"><img id=\"Planet\" src=\"".$planet_image_path."\" width=\"".$pic_width."\" height=\"".$pic_height."\" border=\"0\" 
	    onMouseOver=\"show_info(
			'".$arr_planet['id']."',
			'".$arr_planet['planet_name']."',
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
			);\"/></a>";
	
	
			if($degree==0)
				$text="center";
			elseif($degree>0 && $degree<=180)
				$text="left";
			else
				$text="right";
	
			$left2=$middle_left+(($d_planets/2)*cos(deg2rad($degree+270)));
			$top2=$middle_top+(($d_planets/2)*sin(deg2rad($degree+270)));
	
			echo "<div style=\"position:absolute; left:".$left2."px; top:".$top2."px; text-align:center; vertical-align:middle;\">".$planet_link."</div>";
	
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
	
			echo "<div style=\"position:absolute; left:".$left."px; top:".$top."px; width:".$info_box_width."px; height:".$info_box_height."px; text-align:".$text."; vertical-align:middle;\">".$planet_info."</div>";
	
			$degree = $degree + (360/$division);
		}


	$top_table=$middle_top+(($d_planets/2)*sin(deg2rad(55+270)));
	echo "<center><table border=\"0\" width=\"75%\" style=\"text-align:center; vertical-align:middle;\">";
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
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_building\" onMouseOver=\"chng('buildings');\">&nbsp;</div></td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_building_name\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_building_time\">&nbsp;</td></tr>

			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_shipyard\" onMouseOver=\"chng('shipyard');\">&nbsp;</div></td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_shipyard_name\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_shipyard_time\">&nbsp;</td>
			</tr>

			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\"><div id=\"planet_info_text_defense\" onMouseOver=\"chng('defense');\">&nbsp;</div></td>
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
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\" id=\"planet_info_text_res_metal\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_metal\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\" id=\"planet_info_text_res_crystal\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_crystal\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\" id=\"planet_info_text_res_plastic\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_plastic\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\" id=\"planet_info_text_res_fuel\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_fuel\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\" id=\"planet_info_text_res_food\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_res_food\">&nbsp;</td>
			</tr>

			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\" id=\"planet_info_text_people\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_people\">&nbsp;</td>
			</tr>
			<tr>
				<td width=\"38%\" class=\"PlaniTextCenterBeschreibung\" id=\"planet_info_text_power\">&nbsp;</td>
				<td width=\"2%\">&nbsp;</td>
				<td width=\"60%\" class=\"PlaniTextCenter\" id=\"planet_info_power\">&nbsp;</td>
			</tr>

	</table></center>";

echo "</div></div></center>";

?>
