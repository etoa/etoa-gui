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
	// 	Last edited: 19.06.2008
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about a given fleet
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2008 by EtoA Gaming, www.etoa.net
	*/
	
	if (intval($_GET['lead_id'])>0)
	$lead_id=intval($_GET['lead_id']);

	//
	// Flottendaten laden und überprüfen ob die Flotte existiert
	//
	if ($lead_id)
		$fd = new Fleet($fleet_id,-1,$lead_id);
	if ($fd->valid())
	{
		// Flugabbruch auslösen
		if (isset($_POST['cancel'])!="" && checker_verify())
		{
			if ($fd->cancelFlight())
			{
				ok_msg("Flug erfolgreich abgebrochen!");
				add_log(13,"Der Spieler [b]".$s['user']['nick']."[/b] bricht den Flug seiner Flotte [b]".$fleet_id."[/b] ab",time());
			}
			else
			{
				err_msg("Flug konnte nicht abgebrochen werden. ".$fd->getError());
			}
		}
		
		//ToDo
		if (isset($_POST['cancel_alliance'])!="" && checker_verify())
		{
			if ($fd->cancelFlight())
			{
				ok_msg("Flug erfolgreich abgebrochen!");
				add_log(13,"Der Spieler [b]".$s['user']['nick']."[/b] bricht den Flug seiner Flotte [b]".$fleet_id."[/b] ab",time());
			}
			else
			{
				err_msg("Flug konnte nicht abgebrochen werden. ".$fd->getError());
			}
		}


		tableStart("","","double");

		// Flugdaten
		tableStart("Flugdaten","50%");
		
		echo "<tr>
			<td class=\"tbltitle\">Auftrag:</td>
			<td class=\"tbldata\" ".tm($fd->getAction()->name(),$fd->getAction()->desc())." style=\"color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
			".$fd->getAction()->name()." [".FleetAction::$statusCode[$fd->status()]."]</td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Leaderflotte:</td>
			<td class=\"tbldata\">";
			if ($fd->id() == $fd->leaderId() && $lead_id)
				echo "Das ist der Gesammte Angriff!</td></tr>";
			elseif ($fd->id() == $fd->leaderId())
			{
				echo "Das ist die Leaderflotte!<br />
				<a href=\"?page=fleetinfo&amp;id=".$fd->leaderId()."&lead_id=".$fd->leaderId()."\">Gesammter Angriff anzeigen</a></td></tr>";
			}
			else
				echo "<a href=\"?page=fleetinfo&amp;id=".$fd->leaderId()."&lead_id=".$fd->leaderId()."\">".$cu->allianceTag()."-".$fd->leaderId()." Besitzer: ".get_user_nick($fd->ownerId())."</a></td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Startkoordinaten:</td>
			<td class=\"tbldata\">
				<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a>
				 (".$fd->getSource()->entityCodeString().")</td></tr>";
		echo "<tr>
			<td class=\"tbltitle\">Zielkoordinaten:</td>
			<td class=\"tbldata\">
				<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a>
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
		if ($fd->id() == $fd->leaderId() && $lead_id)
		{
			echo "<td class=\"tbltitle\">Teilflotten:</td>
				<td class=\"tbldata\">
				<a href=\"?page=fleetinfo&amp;id=".$fd->Id()."\">".$cu->allianceTag()."-".$fd->Id()." Besitzer: ".get_user_nick($fd->ownerId())."</a><br />";
			foreach ($fd->fleets as $f)
			{
				echo "<a href=\"?page=fleetinfo&amp;id=".$f->Id()."\">".$cu->allianceTag()."-".$f->Id()." Besitzer: ".get_user_nick($f->ownerId())."</a><br />";
			}
			echo "</td></tr>";
		}
		tableEnd();

		tableStart("Piloten &amp; Verbrauch","50%");
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">".RES_ICON_PEOPLE."Piloten:</td>
			<td class=\"tbldata\">".nf($fd->pilots())."</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL.":</td>
			<td class=\"tbldata\">".nf($fd->usageFuel())."</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD.":</td>
			<td class=\"tbldata\">".nf($fd->usageFood())."</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_POWER." ".RES_POWER.":</td>
			<td class=\"tbldata\">".nf($fd->usagePower())."</td></tr>";
		tableEnd();

		echo "</td><td style=\"width:5%;vertical-align:top;\"></td><td style=\"width:45%;vertical-align:top;\">";

		// Frachtraum
		tableStart("Frachtraum","50%");
		echo "<tr><td class=\"tbltitle\">".RES_ICON_METAL."".RES_METAL."</td><td class=\"tbldata\">".nf($fd->resMetal())." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td><td class=\"tbldata\" >".nf($fd->resCrystal())." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</td><td class=\"tbldata\">".nf($fd->resPlastic())." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</td><td class=\"tbldata\">".nf($fd->resFuel())." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</td><td class=\"tbldata\">".nf($fd->resFood())." t</td></tr>";
		echo "<tr><td class=\"tbltitle\">".RES_ICON_POWER."".RES_POWER."</td><td class=\"tbldata\">".nf($fd->resPower())." t</td></tr>";
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">Freier Frachtraum:</td><td class=\"tbldata\">".nf($fd->getFreeCapacity())." t</td></tr>";
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">Totaler Frachtraum:</td><td class=\"tbldata\">".nf($fd->getCapacity())." t</td></tr>";
		tableEnd();

		tableStart("Passagierraum","50%");
		echo "<tr><td class=\"tbltitle\">".RES_ICON_PEOPLE."Passagiere</td><td class=\"tbldata\">".nf($fd->resPeople())."</td></tr>";
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">Freier Platz:</td><td class=\"tbldata\">".nf($fd->getFreePeopleCapacity())."</td></tr>";
		echo "<tr><td class=\"tbltitle\" style=\"width:150px;\">Totaler Platz:</td><td class=\"tbldata\">".nf($fd->getPeopleCapacity())."</td></tr>";
		tableEnd();

		echo "</td></tr>";
		tableEnd();

		// Schiffe laden
		if ($fd->countShips() > 0)
		{
			// Schiffe anzeigen
			tableStart("Schiffe");
			echo "<tr>
				<td class=\"tbltitle\" colspan=\"2\">Schifftyp</td>
				<td class=\"tbltitle\" width=\"50\">Anzahl</td></tr>";
			foreach ($fd->getShipIds() as $sid=> $scnt)
			{
				$ship = new Ship($sid);
				echo "<tr>
					<td class=\"tbldata\" style=\"width:40px;background:#000\">
						".$ship->imgSmall()."</td>";
				echo "<td class=\"tbldata\">
					<b>".$ship->name()."</b><br/>
				".text2html($ship->shortComment())."</td>";
				echo "<td class=\"tbldata\" style=\"width:50px;\">".nf($scnt)."</td></tr>";
			}
			tableEnd();
		}

		echo "<form action=\"?page=$page&amp;id=$fleet_id\" method=\"post\">";
		echo "<input type=\"button\" onClick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\"> &nbsp;";

		// Abbrechen-Button anzeigen
		if ($fd->status() == 0 && $fd->landTime() > time())
		{
			checker_init();
			if ($fd->id() == $fd->leaderId())
				echo "<input type=\"submit\" name=\"cancel_alliance\" value=\"Allianzangriff abbrechen und zum Heimatplanet zur&uuml;ckkehren\"  onclick=\"return confirm('Willst du diesen Flug wirklich abbrechen? Damit beendest du den ganze Alianzangriff');\">";
			else
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
