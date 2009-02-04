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
	// 	Created: 11.11.2008
	// 	Last edited: 11.11.2008
	// 	Last edited by: glaubinix <glaubinix@etoa.ch>
	//	
	/**
	* Shows information about a given fleet (support)
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2008 by EtoA Gaming, www.etoa.net
	*/	
	
	if ($cu->alliance->getBuildingLevel("Flottenkontrolle")>=ALLIANCE_FLEET_SHOW_DETAIL)
	{
		if ($cu->alliance->checkActionRightsNA('fleetminister') || $cu->id==$fd->ownerId())
		{
			// Flugabbruch auslösen
			if (isset($_POST['cancel'])!="" && checker_verify())
			{
				if (($cu->alliance->getBuildingLevel("Flottenkontrolle")>=ALLIANCE_FLEET_SEND_HOME || $cu->id==$fd->ownerId()) && $fd->cancelFlight())
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
						".$fd->getAction()->name()." [".FleetAction::$statusCode[$fd->status()]."]
					</td>
				</tr>";
	
			if ($fd->status() == 3)
			{
				echo "<tr>
						<td class=\"tbltitle\">Heimatplanet:</td>
						<td class=\"tbldata\">
							<a href=\"?page=cell&amp;id=".$fd->getHome()->cellId()."&amp;hl=".$fd->getHome()->id()."\">".$fd->getHome()."</a> 
							(".$fd->getHome()->entityCodeString().")
						</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Supportplanet:</td>
						<td class=\"tbldata\">
							<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a> 
							(".$fd->getTarget()->entityCodeString().")
						</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Startzeit:</td>
						<td class=\"tbldata\">".date("d.m.Y H:i:s",$fd->launchTime())."</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Ende des Supports:</td>
						<td class=\"tbldata\">".date("d.m.Y H:i:s",$fd->landTime())."</td>
					</tr>";
			}
			else
			{
				echo "<tr>
						<td class=\"tbltitle\">Startkoordinaten:</td>
						<td class=\"tbldata\">
							<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a> 
							(".$fd->getSource()->entityCodeString().")
						</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Zielkoordinaten:</td>
						<td class=\"tbldata\">
							<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a> 
							(".$fd->getTarget()->entityCodeString().")
						</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Startzeit:</td>
						<td class=\"tbldata\">".date("d.m.Y H:i:s",$fd->launchTime())."</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">Ende des Fluges:</td>
						<td class=\"tbldata\">".date("d.m.Y H:i:s",$fd->landTime())."</td>
					</tr>";
			}
			echo "<tr>
					<td class=\"tbltitle\">Verbleibend:</td>
					<td class=\"tbldata\" id=\"flighttime\" style=\"color:#ff0\">-</td>
				</tr>";
			tableEnd();
			
			tableStart("Piloten &amp; Verbrauch","50%");
			echo "<tr>
					<td class=\"tbltitle\" style=\"width:150px;\">".RES_ICON_PEOPLE."Piloten:</td>
					<td class=\"tbldata\">".nf($fd->pilots())."</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL.":</td>
					<td class=\"tbldata\">".nf($fd->usageFuel())."</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD.":</td>
					<td class=\"tbldata\">".nf($fd->usageFood())."</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">".RES_ICON_POWER." ".RES_POWER.":</td>
					<td class=\"tbldata\">".nf($fd->usagePower())."</td>
				</tr>";
			tableEnd();
			
			echo "</td><td style=\"width:5%;vertical-align:top;\"></td><td style=\"width:45%;vertical-align:top;\">";
			
			// Frachtraum
			tableStart("Frachtraum","50%");
			echo "<tr>
					<td class=\"tbltitle\">".RES_ICON_METAL."".RES_METAL."</td>
					<td class=\"tbldata\">".nf($fd->resMetal())." t</td>
				</tr>
				<tr>
					<td class=\"tbltitle\">".RES_ICON_CRYSTAL."".RES_CRYSTAL."</td>
					<td class=\"tbldata\" >".nf($fd->resCrystal())." t</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">".RES_ICON_PLASTIC."".RES_PLASTIC."</td>
						<td class=\"tbldata\">".nf($fd->resPlastic())." t</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">".RES_ICON_FUEL."".RES_FUEL."</td>
						<td class=\"tbldata\">".nf($fd->resFuel())." t</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">".RES_ICON_FOOD."".RES_FOOD."</td>
						<td class=\"tbldata\">".nf($fd->resFood())." t</td>
					</tr>
					<tr>
						<td class=\"tbltitle\">".RES_ICON_POWER."".RES_POWER."</td>
						<td class=\"tbldata\">".nf($fd->resPower())." t</td>
					</tr>
					<tr>
						<td class=\"tbltitle\" style=\"width:150px;\">Freier Frachtraum:</td>
						<td class=\"tbldata\">".nf($fd->getFreeCapacity())." t</td>
					</tr>
					<tr>
						<td class=\"tbltitle\" style=\"width:150px;\">Totaler Frachtraum:</td>
						<td class=\"tbldata\">".nf($fd->getCapacity())." t</td>
					</tr>";
			tableEnd();
			
			tableStart("Passagierraum","50%");
			echo "<tr>
					<td class=\"tbltitle\">".RES_ICON_PEOPLE."Passagiere</td>
					<td class=\"tbldata\">".nf($fd->resPeople())."</td>
				</tr>
				<tr>
					<td class=\"tbltitle\" style=\"width:150px;\">Freier Platz:</td>
					<td class=\"tbldata\">".nf($fd->getFreePeopleCapacity())."</td>
				</tr>
				<tr>
					<td class=\"tbltitle\" style=\"width:150px;\">Totaler Platz:</td>
					<td class=\"tbldata\">".nf($fd->getPeopleCapacity())."</td>
				</tr>";
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
						<td class=\"tbltitle\" width=\"50\">Anzahl</td>
					</tr>";
				foreach ($fd->getShipIds() as $sid=> $scnt)
				{
					$ship = new Ship($sid);
					echo "<tr>
							<td class=\"tbldata\" style=\"width:40px;background:#000\">
								".$ship->imgSmall()."
							</td>
							<td class=\"tbldata\">
								<b>".$ship->name()."</b><br/>
								".text2html($ship->shortComment())."
							</td>
							<td class=\"tbldata\" style=\"width:50px;\">".nf($scnt)."</td>
						</tr>";
				}
				tableEnd();
			}
			
			echo "<form action=\"?page=$page&amp;id=$fleet_id\" method=\"post\">";
			echo "<input type=\"button\" onClick=\"document.location='?page=fleets'\" value=\"Zur&uuml;ck zur Flotten&uuml;bersicht\"> &nbsp;";
			
			// Abbrechen-Button anzeigen
			if (($cu->alliance->getBuildingLevel("Flottenkontrolle")>=ALLIANCE_FLEET_SEND_HOME || $cu->id==$fd->ownerId()) && ($fd->status() == 0 || $fd->status() == 4) && $fd->landTime() > time())
			{
				checker_init();
				echo "<input type=\"submit\" name=\"cancel\" value=\"Flug abbrechen und zum Heimatplanet zur&uuml;ckkehren\"  onclick=\"return confirm('Willst du diesen Flug wirklich abbrechen?');\">";
			}
			echo "</form>";
			
			countDown('flighttime',$fd->landTime());
		}
		else
		{
			err_msg("Du besitzt nicht die notwendigen Rechte!");
		}
	}
	else
	{
		err_msg("Die Allianzflottenkontrolle wurde noch nicht genug ausgebaut!");
	}
?>
