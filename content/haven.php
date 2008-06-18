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
	// 	File: haven.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Sends ships on their flights
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	// BEGIN SKRIPT //

	if ($cp)
	{

		echo "<h1>Raumschiffhafen des Planeten ".$cp->name."</h1>";
		$cp->resBox();
	
		$fm = new FleetManager($cu->id());
		$fleets_away = $fm->countControlledByEntity($cp->id());

		$bl = new BuildList($cp->id());
		$fleetControlLevel = $bl->getLevel(FLEET_CONTROL_ID);

		$fleets_start_possible = FLEET_NOCONTROL_NUM + $fleetControlLevel;
		$fleets_start_possible-= $fleets_away;

		//
		// Kampfsperre prüfen
		//
		if ($conf['battleban']['v']!=0 && $conf['battleban_time']['p1']<=time() && $conf['battleban_time']['p2']>time())
		{
			infobox_start("Kampfsperre");
			echo "Es ist momentan nicht m&ouml;glich andere Spieler anzugreifen. Grund: ".text2html($conf['battleban']['p1'])."<br>Die Sperre dauert vom ".date("d.m.Y",$conf['battleban_time']['p1'])." um ".date("H:i",$conf['battleban_time']['p1'])." Uhr bis am ".date("d.m.Y",$conf['battleban_time']['p2'])." um ".date("H:i",$conf['battleban_time']['p2'])." Uhr!";
			infobox_end();
		}

		//
		// Flottensperre prüfen
		//
		if ($conf['flightban']['v']!=0 && $conf['flightban_time']['p1']<=time() && $conf['flightban_time']['p2']>time())
		{
			infobox_start("Flottensperre");
			echo "Es ist momentan nicht m&ouml;glich Fl&uuml;ge zu starten. Grund: ".text2html($conf['flightban']['p1'])."<br>Die Sperre dauert vom ".date("d.m.Y",$conf['flightban_time']['p1'])." um ".date("H:i",$conf['flightban_time']['p1'])." Uhr bis am ".date("d.m.Y",$conf['flightban_time']['p2'])." um ".date("H:i",$conf['flightban_time']['p2'])." Uhr!";
			infobox_end();
		}
		
		//
		// Prüfen ob dieses Gebäude deaktiviert wurde
		//
		elseif ($dt = $bl->getDeactivated(FLEET_CONTROL_ID))
 		{
			infobox_start("Geb&auml;ude nicht bereit");
			echo "Dieser Raumschiffhafen ist bis ".df($dt)." deaktiviert.";
			infobox_end();
		}
		//
		// Hafen-Optionen
		//
		else
		{
						
			// Piloten
			$people_available = floor($cp->people() - $bl->totalPeopleWorking());			

			// Set vars for xajax
			$_SESSION['haven'] = Null;
		
			$_SESSION['haven']['user_id'] = $cu->id();
			$_SESSION['haven']['planet_id'] = $cp->id(); 
			$_SESSION['haven']['people_available'] = $people_available;
			$_SESSION['haven']['fleets_start_possible'] = $fleets_start_possible;
			$_SESSION['haven']['race_speed_factor'] = $cu->raceSpeedFactor();

			if (isset ($_GET['target']) && intval($_GET['target'])>0)
			{
				$_SESSION['haven']['targetId']=$_GET['target'];
			}

			// Fleet object
			$fleet = new FleetLaunch();
			$fleet->setSource($cp);
			$fleet->setOwnerId($cu->id());
			$_SESSION['haven']['fleetObj']=serialize($fleet);
			
			// Infobox
    	infobox_start("Hafen-Infos",1);
    		
			// Flotten unterwegs
    	echo "<tr><th class=\"tbltitle\">Aktive Flotten:</th><td class=\"tbldata\">";
			if ($fleets_away>1)
				echo "<b>$fleets_away</b> Flotten dieses Planeten sind <a href=\"?page=fleets\">unterwegs</a>.";
			elseif ($fleets_away==1)
				echo "<b>Eine</b> Flotte dieses Planeten ist <a href=\"?page=fleets\">unterwegs</a>.";
			else
				echo "Es sind <b>keine</b> Flotten dieses Planeten unterwegs.";
			echo "</td></tr>";
		
			// Flotten startbar?
    	echo "<tr><th class=\"tbltitle\">Flottenstart:</th><td class=\"tbldata\">";
 			if ($fleets_start_possible>1 )
				echo "<b>$fleets_start_possible</b> Flotten k&ouml;nnen von diesem Planeten starten!";
			elseif ($fleets_start_possible==1 )
				echo "<b>Eine</b> Flotte kann von diesem Planeten starten!";
			else
				echo "Es k&ouml;nnen <b>keine</b> Flotten von diesem Planeten starten!";
			echo " (Flottenkontrolle: Stufe ".$fleetControlLevel.")</td></tr>";
		
			// Piloten		
    	echo "<tr><th class=\"tbltitle\">Piloten:</th><td class=\"tbldata\">";
			if ($people_available>1)
				echo "<b>".$people_available."</b> Piloten k&ouml;nnen eingesetzt werden.";
			elseif ($people_available==1)
				echo "<b>Ein</b> Pilot kann eingesetzt werden.";
			else
				echo "Es sind <b>keine</b> Piloten verf&uuml;gbar.";
			echo "</td></tr>";
					
			// Rasse		
  		if ($cu->raceSpeedFactor() != 1)
  		{
  			echo "<tr><th class=\"tbltitle\">Piloten:</th><td class=\"tbldata\">";
				echo "Die Schiffe fliegen aufgrund deiner Rasse <b>".$cu->raceName()."</b> mit ".get_percent_string($cu->raceSpeedFactor(),1)." Geschwindigkeit!";
				echo "</td></tr>";
			}
			infobox_end(1);
			
			echo "<div id=\"havenContent\">
			<div id=\"havenContentShips\" style=\"\">
			<div style=\"padding:20px\"><img src=\"images/loading.gif\" alt=\"Loading\" /> Lade Daten...</div>
			</div>
			<div id=\"havenContentTarget\" style=\"display:none;\"></div>
			<div id=\"havenContentAction\" style=\"display:none;\"></div>
			
			
			</div>";
			echo "<script type=\"text/javascript\">xajax_havenShowShips();</script>";	
		}
	}
?>
