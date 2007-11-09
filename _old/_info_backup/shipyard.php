<?PHP

	echo "<h2>Raumschiffe</h2>";

	$race=get_races_array();
	define(TECH_SPEED_CAT,1);

	//
	// Details
	//
	if ($_GET['id']!="")
	{
		$res = dbquery("SELECT * FROM ".$db_table['ships']." WHERE ship_id='".$_GET['id']."' AND special_ship=0;");
		if ($arr = @mysql_fetch_array($res))
		{
			helpNavi(array("Schiffe","shipyard"),array(text2html($arr['ship_name']),$arr['ship_id']),1);
			echo "<select onchange=\"document.location='?page=help&site=shipyard&id='+this.options[this.selectedIndex].value\">";
			$bres=dbquery("SELECT 
				ship_id,
				ship_name 
			FROM 
				".$db_table['ships']." 
			WHERE 
				ship_buildable=1
				AND special_ship=0
			ORDER BY 
				ship_name;");
			while ($barr=mysql_fetch_array($bres))		
			{
				echo "<option value=\"".$barr['ship_id']."\"";
				if ($barr['ship_id']==$_GET['id']) echo " selected=\"selected\"";
				echo ">".$barr['ship_name']."</option>";
			}
			echo "</select><br/><br/>";		
			
			if ($arr['ship_colonialize']==1) $colonialize="Ja"; else $colonialize="Nein";
			if ($arr['ship_invade']==1) $invade="Ja"; else $invade="Nein";
			if ($arr['ship_recycle']==1) $recycle="Ja"; else $recycle="Nein";
			if ($arr['ship_nebula']==1) $gas="Ja"; else $gas="Nein";
			if ($arr['ship_asteroid']==1) $asteroid="Ja"; else $asteroid="Nein";
			if ($arr['ship_antrax']==1) $antrax="Ja"; else $antrax="Nein";
			if ($arr['ship_forsteal']==1) $forsteal="Ja"; else $forsteal="Nein";
			if ($arr['ship_build_destroy']==1) $build_destroy="Ja"; else $build_destroy="Nein";
			if ($arr['ship_tarned']==1) $tarn="Ja"; else $tarn="Nein";
			if ($arr['ship_fake']==1) $fake="Ja"; else $fake="Nein";
			if ($arr['ship_heal']>=1) $heal="Ja"; else $heal="Nein";
			if ($arr['ship_antrax_food']==1) $antrax_food="Ja"; else $antrax_food="Nein";
			if ($arr['ship_deactivade']==1) $deactivade="Ja"; else $deactivade="Nein";
			if ($arr['ship_tf']==1) $tf="Ja"; else $tf="Nein";

			$vres=dbquery("Select tech_id,tech_name,req_req_tech_level FROM ".$db_table['ship_requirements'].",".$db_table['technologies']." WHERE
			req_ship_id=".$arr['ship_id']."
			AND tech_type_id='".TECH_SPEED_CAT."'
			AND req_req_tech_id=tech_id
			GROUP BY req_id;");

			infobox_start($arr['ship_name'],1);

    	echo "<tr><td class=\"tbltitle\" style=\"width:220px;\"><img src=\"".IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id'].".".IMAGE_EXT."\" width=\"220\" height=\"220\" alt=\"Schiff\" /></td>";
    	echo "<td class=\"tbldata\" colspan=\"3\">".text2html($arr['ship_longcomment'])."</td></tr>";

    	if ($arr['ship_race_id']>0)
    	{
    		echo "<tr><th class=\"tbltitle\">Rasse:</th><td colspan=\"3\" class=\"tbldata\">Diese Schiff kann exklusiv nur durch das Volk der <b>".$race[$arr['ship_race_id']]['race_name']."</b> gebaut werden!</td></tr>";
    	}

    	echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">Kosten ".$rsc['metal']."</td><td class=\"tbldata\">".nf($arr['ship_costs_metal'])." t</td>";
			echo "<td class=\"tbltitle\" style=\"width:150px;\">Gas saugen?</td><td class=\"tbldata\">$gas</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['crystal']."</td><td class=\"tbldata\">".nf($arr['ship_costs_crystal'])." t</td>";
	    echo "<td class=\"tbltitle\">Asteroiden sammeln?</td><td class=\"tbldata\">$asteroid</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['plastic']."</td><td class=\"tbldata\">".nf($arr['ship_costs_plastic'])." t</td>";
	    echo "<td class=\"tbltitle\">Giftgas?</td><td class=\"tbldata\">$antrax</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['fuel']."</td><td class=\"tbldata\">".nf($arr['ship_costs_fuel'])." t</td>";
	    echo "<td class=\"tbltitle\">Antrax?</td><td class=\"tbldata\">$antrax_food</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Kosten ".$rsc['food']."</td><td class=\"tbldata\">".nf($arr['ship_costs_food'])." t</td>";
	    echo "<td class=\"tbltitle\">Bombadieren?</td><td class=\"tbldata\">$build_destroy</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Trit. /100 AE</td><td class=\"tbldata\">".nf($arr['ship_fuel_use'])." t</td>";
	    echo "<td class=\"tbltitle\">Deaktivieren?</td><td class=\"tbldata\">$deactivade</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Trit. f&uuml;r Start</td><td class=\"tbldata\">".nf($arr['ship_fuel_use_launch'])." t</td>";
	    echo "<td class=\"tbltitle\">Fakeangriff?</td><td class=\"tbldata\">$fake</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Trit. f&uuml;r Landung</td><td class=\"tbldata\">".nf($arr['ship_fuel_use_landing'])." t</td>";
	    echo "<td class=\"tbltitle\">Forschung stehlen?</td><td class=\"tbldata\">$forsteal</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Kapazit&auml;t</td><td class=\"tbldata\">".nf($arr['ship_capacity'])." t</td>";
	    echo "<td class=\"tbltitle\">Tarnangriff?</td><td class=\"tbldata\">$tarn</td></tr>";

  	  echo "<tr><td class=\"tbltitle\">Geschwindigkeit</td><td class=\"tbldata\">".nf($arr['ship_speed']/FLEET_FACTOR_F)." AE/h</td>";
	    echo "<td class=\"tbltitle\">Heilen?</td><td class=\"tbldata\">$heal</td></tr>";

	    echo "<tr><td class=\"tbltitle\">Startdauer</td><td class=\"tbldata\">".tf($arr['ship_time2start']/FLEET_FACTOR_S)."</td>";
	    echo "<td class=\"tbltitle\">Tr&uuml;mmerfeld erstellen?</td><td class=\"tbldata\">$tf</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Landedauer</td><td class=\"tbldata\">".tf($arr['ship_time2land']/FLEET_FACTOR_L)."</td>";
			echo "<td class=\"tbltitle\">Struktur</td><td class=\"tbldata\">".nf($arr['ship_structure'])."</td></tr>";

  	  echo "<tr><td class=\"tbltitle\">Kolonialisieren?</td><td class=\"tbldata\">$colonialize</td>";
			echo "<td class=\"tbltitle\">Schutzschild</td><td class=\"tbldata\">".nf($arr['ship_shield'])."</td></tr>";

	    echo "<tr><td class=\"tbltitle\">Invasieren?</td><td class=\"tbldata\">$invade</td>";
	    echo "<td class=\"tbltitle\">Waffen</td><td class=\"tbldata\">".nf($arr['ship_weapon'])."</td></tr>";

    	echo "<tr><td class=\"tbltitle\">Recyclen?</td><td class=\"tbldata\">$recycle</td>";
	    echo "<td class=\"tbltitle\">Antriebstechnologien</td><td class=\"tbldata\">";
			if (mysql_num_rows($vres)>0)
			{
				while ($varr=mysql_fetch_array($vres))
				{
					echo "<a href=\"?page=help&amp;site=research&amp;id=".$varr['tech_id']."\">".$varr['tech_name']."</a> (Stufe ".$varr['req_req_tech_level'].")<br/>";
				}
			}
	    echo "</td></tr>";



	    infobox_end(1);
		}
		else
		  echo "Schiffdaten nicht gefunden!<br><br>";

		echo "<input type=\"button\" value=\"Schiff&uuml;bersicht\" onclick=\"document.location='?page=$page&site=$site'\" /> &nbsp; ";
		echo "<input type=\"button\" value=\"Technikbaum\" onclick=\"document.location='?page=techtree&mode=ships'\" /> &nbsp; ";
		if ($_SESSION['lastpage']=="haven")
			echo "<input type=\"button\" value=\"Zur&uuml;ck zum Hafen\" onclick=\"document.location='?page=haven'\" /> &nbsp; ";
		if ($_SESSION['lastpage']=="shipyard")
			echo "<input type=\"button\" value=\"Zur&uuml;ck zur Raumschiffwerft\" onclick=\"document.location='?page=shipyard'\" /> &nbsp; ";

	}

	//
	// &Uuml;bersicht
	//
	else
	{
		helpNavi(array("Schiffe","shipyard"));
	
		if ($_GET['order']!="")
		{
			$order="ship_".$_GET['order'];
			if ($_SESSION['help']['orderfield']==$_GET['order'])
			{
				if ($_SESSION['help']['ordersort']=="DESC")
					$sort="ASC";
				else
					$sort="DESC";
			}
			else
			{
				if ($_GET['order']=="name")
					$sort="ASC";
				else
					$sort="DESC";
			}
			$_SESSION['help']['orderfield']=$_GET['order'];
			$_SESSION['help']['ordersort']=$sort;
		}
		else
		{
			$order="ship_name";
			$sort="ASC";
		}


		$res = dbquery("
		SELECT 
			* 
		FROM 
			".$db_table['ships']." 
		WHERE 
			ship_buildable=1 
			AND special_ship=0 
		ORDER BY 
			$order $sort;");
		if (mysql_num_rows($res)>0)
		{
			infobox_start("Raumschiff&uuml;bersicht",1);
			echo "<tr><th class=\"tbltitle\" colspan=\"2\"><a href=\"?page=$page&amp;site=$site&amp;order=name\">Name</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=race_id\">Rasse</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=capacity\">Kapazit&auml;t</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=speed\">Speed</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=fuel_use\">Treibstoff</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=weapon\">Waffen</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=structure\">Struktur</a></th>";
			echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;site=$site&amp;order=shield\">Schild</a></th>";
			//echo "<th class=\"tbltitle\">&nbsp;</th></tr>";
			while ($arr = mysql_fetch_array($res))
			{
				$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$arr['ship_id']."_small.".IMAGE_EXT;
				echo "<tr><td class=\"tbldata\"><a href=\"?page=$page&site=$site&id=".$arr['ship_id']."\"><img src=\"$s_img\" alt=\"Schiffbild\" width=\"40\" height=\"40\" border=\"0\"/></a></td>";
				echo "<td class=\"tbltitle\" ".tm("Info",text2html($arr['ship_shortcomment'])).">".$arr['ship_name']."</td>";
				echo "<td class=\"tbldata\">";
				if ($arr['ship_race_id']>0)
					echo $race[$arr['ship_race_id']]['race_name'];
				else
					echo "-";
				echo "<td class=\"tbldata\">".nf($arr['ship_capacity'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['ship_speed'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['ship_fuel_use'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['ship_weapon'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['ship_structure'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['ship_shield'])."</td></tr>";
				//echo "<td class=\"tbldata\">".$arr['ship_shortcomment']."</td>";
				//echo "<td class=\"tbldata\"><a href=\"?page=$page&site=$site&id=".$arr['ship_id']."\">Details</a></td></tr>";
			}
			infobox_end(1);
		}
		else
			echo "<i>Keine Daten vorhanden!</i>";
	}
?>
