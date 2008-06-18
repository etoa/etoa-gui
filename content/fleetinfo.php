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
	// 	File: fleetinfo.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about a given fleet
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	


	// DEFINITIONEN //

	echo "<h1>Flotten</h1>";
	echo "<h2>Details</h2>";

	// BEGIN SKRIPT //

	if (intval($_GET['id'])>0)
	$fleet_id=intval($_GET['id']);

	//
	// Flugabbruch auslösen
	//
	if (isset($_POST['cancel'])!="" && checker_verify())
	{
		$res=dbquery("
		SELECT
			*
		FROM
			fleet
		WHERE
			fleet_id='".$fleet_id."'
			AND fleet_user_id='".$cu->id()."'
			AND fleet_landtime>".time()."
			AND fleet_action NOT LIKE '%c%'
			AND fleet_action NOT LIKE '%r%'
			AND fleet_updating=0
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			$launchtime=time();
			$landtime=$launchtime+time()-$arr['fleet_launchtime'];
			if ($arr['fleet_action']=="fo")
			{
				$addsql = "
				fleet_res_metal=0,
				fleet_res_crystal=0,
				fleet_res_plastic=0,
				fleet_res_fuel=0,
				fleet_res_food=0,
				";
			}
			$sql = "UPDATE
				fleet
			SET
				fleet_cell_from='".$arr['fleet_cell_to']."',
				fleet_cell_to='".$arr['fleet_cell_from']."',
				fleet_planet_from='".$arr['fleet_planet_to']."',
				fleet_planet_to='".$arr['fleet_planet_from']."',
				fleet_action='".$arr['fleet_action']."c',
				".$addsql."
				fleet_launchtime=$launchtime,
				fleet_landtime=$landtime
			WHERE
				fleet_id='".$fleet_id."';";
			dbquery($sql);
			echo "Flug erfolgreich abgebrochen!<br/><br/>";
			add_log(13,"Der Spieler [b]".$s['user']['nick']."[/b] bricht den Flug seiner Flotte [b]".$fleet_id."[/b] ab",time());
		}
		else
			echo "Flug konnte nicht abgebrochen werden, entweder ist die Flotte schon gelandet, bereits auf dem R&uuml;ckflug oder der Flottenstatus wird gerade aktualisiert!<br/><br/>";
	}

	//
	// Flottendaten laden und überprüfen ob die Flotte existiert
	//
	$fd = new Fleet($fleet_id,$cu->id());
	if ($fd->valid())
	{
		echo "<table style=\"width:100%\"><tr><td style=\"width:50%;vertical-align:top;\">";

		// Flugdaten
		infobox_start("Flugdaten",1);
		
		echo "<tr>
			<td class=\"tbltitle\">Auftrag:</td>
			<td class=\"tbldata\" ".tm($fd->getAction()->name(),$fd->getAction()->desc())." style=\"color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
			".$fd->getAction()->name()." [".FleetAction::$statusCode[$fd->status()]."]</td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Startkoordinaten:</td>
			<td class=\"tbldata\">
				<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."\">".$fd->getSource()."</a>
				 (".$fd->getSource()->entityCodeString().")</td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Zielkoordinaten:</td>
			<td class=\"tbldata\">
				<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."\">".$fd->getTarget()."</a>
				 (".$fd->getTarget()->entityCodeString().")</td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Startzeit:</td>
			<td class=\"tbldata\">".date("d.m.Y H:i:s",$fd->launchTime())."</td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Ende des Fluges:</td>
			<td class=\"tbldata\">".date("d.m.Y H:i:s",$fd->landTime())."</td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Verbleibend:</td>
			<td class=\"tbldata\" id=\"flighttime\" style=\"color:#ff0\">-</td></tr>";
		infobox_end(1);

		infobox_start("Piloten &amp; Verbrauch",1);
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">".RES_ICON_PEOPLE."Piloten:</td>
			<td class=\"tbldata\">".nf($fd->pilots())."</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL.":</td>
			<td class=\"tbldata\">".nf($fd->usageFuel())."</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD.":</td>
			<td class=\"tbldata\">".nf($fd->usageFood())."</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_POWER." ".RES_POWER.":</td>
			<td class=\"tbldata\">".nf($fd->usagePower())."</td></tr>";
		infobox_end(1);

		echo "</td><td style=\"width:5%;vertical-align:top;\"></td><td style=\"width:45%;vertical-align:top;\">";

		// Frachtraum
		infobox_start("Frachtraum",1);
		echo "<tr><td class=\"tbltitle\">".RES_ICON_METAL."".RES_METAL."</td><td class=\"tbldata\">".nf($arr['fleet_res_metal'])." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td><td class=\"tbldata\" >".nf($arr['fleet_res_crystal'])." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</td><td class=\"tbldata\">".nf($arr['fleet_res_plastic'])." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</td><td class=\"tbldata\">".nf($arr['fleet_res_fuel'])." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</td><td class=\"tbldata\">".nf($arr['fleet_res_food'])." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_POWER."".RES_POWER."</td><td class=\"tbldata\">".nf($arr['fleet_res_food'])." t</td></tr>";
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">Freier Frachtraum:</td><td class=\"tbldata\">".nf($arr['fleet_capacity'])." t</td></tr>";
		infobox_end(1);

		infobox_start("Passagierraum",1);
		echo "<tr><td class=\"tbltitle\">".RES_ICON_PEOPLE."Passagiere</td><td class=\"tbldata\">".nf($arr['fleet_res_people'])."</td></tr>";
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">Freier Platz:</td><td class=\"tbldata\">".nf($arr['fleet_capacity'])." t</td></tr>";
		infobox_end(1);

		echo "</td></tr></table>";

		// Schiffe laden
		$sres = dbquery("
		SELECT
            s.ship_id,
            s.ship_name,
            s.ship_shortcomment,
            fs.fs_ship_cnt
		FROM
            ".$db_table['fleet_ships']." AS fs,
            ".$db_table['ships']." AS s
		WHERE
            fs.fs_ship_id=s.ship_id
            AND fs.fs_fleet_id='".$fleet_id."'
            AND fs.fs_ship_cnt>'0'
            AND fs.fs_ship_faked='0'
		GROUP BY
			fs.fs_ship_id;");
		if (mysql_num_rows($sres)>0)
		{
			// Schiffe anzeigen
			infobox_start("Schiffe",1);
			echo "<tr><td class=\"tbltitle\" colspan=\"2\">Schifftyp</td><td class=\"tbltitle\" width=\"50\">Anzahl</td></tr>";
			while ($sarr = mysql_fetch_array($sres))
			{
				$s_img = IMAGE_PATH."/".IMAGE_SHIP_DIR."/ship".$sarr['ship_id']."_small.".IMAGE_EXT;
				echo "<tr><td class=\"tbldata\" style=\"width:40px;\"><img src=\"$s_img\" style=\"width:40px;height:40px;\"/></td>";
				echo "<td class=\"tbldata\"><b>".text2html($sarr['ship_name'])."</b><br/>".text2html($sarr['ship_shortcomment'])."</td>";
				echo "<td class=\"tbldata\" style=\"width:50px;\">".nf($sarr['fs_ship_cnt'])."</td></tr>";
			}
			infobox_end(1);
		}

		echo "<form action=\"?page=$page&amp;id=$fleet_id\" method=\"post\">";
		echo "<input type=\"button\" onClick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\"> &nbsp;";

		// Abbrechen-Button anzeigen
		if (!stristr($arr['fleet_action'],"c") && !stristr($arr['fleet_action'],"r") && $arr['fleet_landtime']>time())
		{
			checker_init();
			echo "<input type=\"submit\" name=\"cancel\" value=\"Flug abbrechen und zum Heimatplanet zur&uuml;ckkehren\"  onclick=\"return confirm('Willst du diesen Flug wirklich abbrechen?');\">";
		}
		echo "</form>";

		countDown('flighttime',$fd->landTime());
	}
	else
	{
		echo "Diese Flotte existiert nicht mehr! Wahrscheinlich sind die Schiffe schon <br/>auf dem Zielplaneten gelandet oder der Flug wurde abgebrochen.<br/><br/>";
		echo "<input type=\"button\" onclick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\">";
	}
?>
