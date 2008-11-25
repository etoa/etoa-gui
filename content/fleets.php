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
	// 	File: fleets.php
	// 	Created: 01.12.2004
	// 	Last edited: 07.07.2007
	// 	Last edited by: MrCage <mrcage@etoa.ch>
	//	
	/**
	* Shows information about current flights and incomming foreign fleets
	*
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	//
	// Eigene Flotten
	//

	echo "<h1>Flotten</h1>";
	
	echo "<input type=\"button\" onclick=\"document.location='?page=fleetstats'\" value=\"Schiffs&uuml;bersicht anzeigen\" /> &nbsp; ";
	
	if (isset($_GET['mode']) && $_GET['mode']=="alliance" && $cu->allianceId>0) 
	{
		echo "<input type=\"button\" onclick=\"document.location='?page=fleets'\" value=\"Flotten anzeigen\" /><br/><br/>";
		
		$fm = new FleetManager($cu->id,$cu->allianceId);
		$fm->loadAllianceSupport();		
	
		if ($fm->count() > 0)
		{
			$cdarr = array();
			
			echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
			tableStart("Allianz Supportflotten");
			echo "<tr><td class=\"tbltitle\">Start / Ziel</td>
			<td class=\"tbltitle\">Start / Landung</td>
			<td class=\"tbltitle\">Auftrag / Status</td></tr>";
			foreach ($fm->getAll() as $fid=>$fd)
			{
				$cdarr["cd".$fid] = $fd->landTime();
	
				echo "<tr>";
				echo "<td class=\"tbldata\"><b>".$fd->getSource()->entityCodeString()."</b> 
				<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>";
				echo "<b>".$fd->getTarget()->entityCodeString()."</b> 
				<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a></td>";			
				echo "<td class=\"tbldata\">
				".date("d.m.y, H:i:s",$fd->launchTime())."<br/>";
				echo date("d.m.y, H:i:s",$fd->landTime())."</td>";
				echo "<td class=\"tbldata\">
					<a href=\"?page=fleetinfo&id=".$fid."\">
					<span style=\"color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
					".$fd->getAction()->name()."
					</span> [".FleetAction::$statusCode[$fd->status()]."]</a><br/>";
				if ($fd->landTime() < time())
				{
					if ($fd->status() > 0)
					{
						echo "Flotte landet...";
					}
					else
					{
						echo "Zielaktion wird durchgef&uuml;hrt...";
					}
				}
				else
				{
					echo "Ankunft in <b><span id=\"cd".$fid."\">-</span></b>";
				}
				echo "</td></tr>";
			}
			tableEnd();
				
			foreach ($cdarr as $elem=>$t)
			{
				countDown($elem,$t);
			}		
		}
		else
		{
			iBoxStart("Allianz Supportflotten");
			echo "Es sind keine Allianz Supportflotten unterwegs!";
			iBoxEnd();
		}
		
		
		$fm->loadAllianceAttacks();		
		if ($fm->count() > 0)
		{
			$cdarr = array();
			
			echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
			tableStart("Allianz Angriffe");
			echo "<tr><td class=\"tbltitle\">Start / Ziel</td>
			<td class=\"tbltitle\">Start / Landung</td>
			<td class=\"tbltitle\">Auftrag / Status</td></tr>";
			foreach ($fm->getAll() as $fid=>$fd)
			{
				$cdarr["cd".$fid] = $fd->landTime();
	
				echo "<tr>";
				echo "<td class=\"tbldata\"><b>".$fd->getSource()->entityCodeString()."</b> 
				<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>";
				echo "<b>".$fd->getTarget()->entityCodeString()."</b> 
				<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a></td>";			
				echo "<td class=\"tbldata\">
				".date("d.m.y, H:i:s",$fd->launchTime())."<br/>";
				echo date("d.m.y, H:i:s",$fd->landTime())."</td>";
				echo "<td class=\"tbldata\">
					<a href=\"?page=fleetinfo&id=".$fid."&lead_id=".$fid."\">
					<span style=\"color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
					".$fd->getAction()->name()."
					</span> [".FleetAction::$statusCode[$fd->status()]."]</a><br/>";
				if ($fd->landTime() < time())
				{
					if ($fd->status() > 0)
					{
						echo "Flotte landet...";
					}
					else
					{
						echo "Zielaktion wird durchgef&uuml;hrt...";
					}
				}
				else
				{
					echo "Ankunft in <b><span id=\"cd".$fid."\">-</span></b>";
				}
				echo "</td></tr>";
			}
			tableEnd();
				
			foreach ($cdarr as $elem=>$t)
			{
				countDown($elem,$t);
			}		
		}
		else
		{
			iBoxStart("Allianz Angriffe");
			echo "Es sind keine Allianz Angriffe unterwegs!";
			iBoxEnd();
		}
	}
	
	
	else {	
		echo "<input type=\"button\" onclick=\"document.location='?page=fleets&mode=alliance'\" value=\"Allianzflotten anzeigen\" /><br/><br/>";
		
		$fm = new FleetManager($cu->id,$cu->allianceId);
		$fm->loadOwn();		
	
		if ($fm->count() > 0)
		{
			$cdarr = array();
			
			echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
			tableStart("Eigene Flotten");
			echo "<tr><td class=\"tbltitle\">Start / Ziel</td>
			<td class=\"tbltitle\">Start / Landung</td>
			<td class=\"tbltitle\">Auftrag / Status</td></tr>";
			foreach ($fm->getAll() as $fid=>$fd)
			{
				$cdarr["cd".$fid] = $fd->landTime();
	
				echo "<tr>";
				echo "<td class=\"tbldata\"><b>".$fd->getSource()->entityCodeString()."</b> 
				<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>";
				echo "<b>".$fd->getTarget()->entityCodeString()."</b> 
				<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a></td>";			
				echo "<td class=\"tbldata\">
				".date("d.m.y, H:i:s",$fd->launchTime())."<br/>";
				echo date("d.m.y, H:i:s",$fd->landTime())."</td>";
				echo "<td class=\"tbldata\">
					<a href=\"?page=fleetinfo&id=".$fid."\">
					<span style=\"color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
					".$fd->getAction()->name()."
					</span> [".FleetAction::$statusCode[$fd->status()]."]</a><br/>";
				if ($fd->landTime() < time())
				{
					if ($fd->status() > 0)
					{
						echo "Flotte landet...";
					}
					else
					{
						echo "Zielaktion wird durchgef&uuml;hrt...";
					}
				}
				else
				{
					echo "Ankunft in <b><span id=\"cd".$fid."\">-</span></b>";
				}
				echo "</td></tr>";
			}
			tableEnd();
				
			foreach ($cdarr as $elem=>$t)
			{
				countDown($elem,$t);
			}		
		}
		else
		{
			iBoxStart("Eigene Flotten");
			echo "Es sind keine eigenen Flotten unterwegs!";
			iBoxEnd();
		}
	
	
		//
		// Gegnerische Flotten
		//
		$header=0;
		$fm->loadForeign();
		if ($fm->count() > 0)
		{	
			tableStart("Fremde Flotten");
			foreach ($fm->getAll() as $fid=>$fd)
			{
	
				// Is the attitude visible?
				if (SPY_TECH_SHOW_ATTITUDE<=$fm->spyTech())
				{
					$attitude = $fd->getAction()->attitude();
				}
				else
				{
					$attitude = 4;				
				}
				$attitudeColor = FleetAction::$attitudeColor[$attitude];
				$attitudeString = FleetAction::$attitudeString[$attitude];
				
				// Is the number of ships visible?
				if(SPY_TECH_SHOW_NUM<=$fm->spyTech())
				{
					$show_num = 1;
	
					//Zählt gefakte Schiffe wenn Aktion=Fakeangriff
					if($fd->getAction()->code()=="fakeattack")
					{
						$fsres = dbquery("
							SELECT 
								SUM(fs_ship_cnt) 
							FROM 
								fleet_ships
							WHERE 
								fs_fleet_id='".$farr['id']." '
								AND fs_ship_faked='1' 
							GROUP BY 
								fs_fleet_id;");
						 $fsarr= mysql_fetch_row($fsres);
						$shipsCount = $fsarr[0];
					}
					else
					  $shipsCount = $fd->countShips();
				}
				else
				{
					$shipsCount = -1;
				}
				
				//Opfer sieht die einzelnen Schiffstypen in der Flotte
				$shipStr = array();
				if(SPY_TECH_SHOW_SHIPS<=$fm->spyTech())
				{
					$showShips = true;
					if($fd->getAction()->code()=="fakeattack")
					{
						$fshipres = dbquery("
							SELECT
								fs.fs_ship_cnt,
								s.ship_name
							 FROM
							  fleet_ships AS fs
							INNER JOIN
								ships AS s
							ON fs.fs_ship_id=s.ship_id
								AND fs.fs_fleet_id='".$farr['id']."'
								AND fs.fs_ship_faked='1';");
						while ($fshiparr = mysql_fetch_assoc($fshipres))
						{
							$str = "";
							
							//Opfer sieht die genau Anzahl jedes Schifftypes in einer Flotte
							if (SPY_TECH_SHOW_NUMSHIPS<=$fm->spyTech())
							{
								$str= "".$fshiparr["fs_ship_cnt"]." ";
							}
								$str.= "".$fshiparr["ship_name"];
								$shipStr[] = $str;
						}
					}
					else
					{
						foreach ($fd->getShipIds() as $sid=> $scnt)
						{
							$str = "";
							$ship = new Ship($sid);
							
							//Opfer sieht die genau Anzahl jedes Schifftypes in einer Flotte
							if (SPY_TECH_SHOW_NUMSHIPS<=$fm->spyTech())
							{
								$str= "".$scnt." ";
							}
								$str.= "".$ship->name();
								$shipStr[] = $str;
						}
					}
	
					// Show action
					if (SPY_TECH_SHOW_ACTION<=$fm->spyTech())
					{
						$shipAction = $fd->getAction()->displayName();
					}
					else
					{
						$shipAction = $attitudeString;
					}
	
					if ($header!=1) 
					{
						echo "<tr>
							<td class=\"tbltitle\">Start / Ziel</td>
							<td class=\"tbltitle\">Startzeit / Landezeit</td>
							<td class=\"tbltitle\">Gesinnung</td>
							<td class=\"tbltitle\">Spieler</td>
							</tr>";
						$header=1;
					}
					
					echo "<tr>
						<td class=\"tbldata\"><b>".$fd->getSource()->entityCodeString()."</b> 
						<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."&amp;hl=".$fd->getSource()->id()."\">".$fd->getSource()."</a><br/>";
					echo "<b>".$fd->getTarget()->entityCodeString()."</b> 
						<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."&amp;hl=".$fd->getTarget()->id()."\">".$fd->getTarget()."</a></td>";			
					echo "<td class=\"tbldata\">
						".date("d.m.y, H:i:s",$fd->launchTime())."<br/>";
					echo date("d.m.y, H:i:s",$fd->landTime())."</td>";
					echo "<td class=\"tbldata\">
						<span style=\"color:".FleetAction::$attitudeColor[$fd->getAction()->attitude()]."\">
						".$fd->getAction()->name()."
						</span> [".FleetAction::$statusCode[$fd->status()]."]<br/>";				
					echo "<td class=\"tbldata\">
						<a href=\"?page=messages&mode=new&message_user_to=".$fd->ownerId()."\">".get_user_nick($fd->ownerId())."</a>
						</td>";	
					echo "</tr>";
					if ($show_num==1)
					{
						echo "<tr><td class=\"tbldata\" colspan=\"4\">";
						echo "<b>Anzahl:</b> ".$shipsCount."";
						if ($showShips)
						{
							echo ";<br><b>Schiffe:</b> ";
							$count = false;
							foreach ($shipStr as $value) {
								echo $value;
								if ($count) { 
									echo ", "; 
								} else {
									$count = true; 
								}
							}
							if ($shipAction)
							{
								echo ";<br><b>Vorhaben:</b> ".$shipAction."";
							}
						}
						echo "</td></tr>";
					}	
				}
			}
			tableEnd();
		}
		else
		{
			iBoxStart("Fremde Flotten");
			echo "Es sind keine fremden Flotten zu deinen Planeten unterwegs!";
			iBoxEnd();
		}
	}



?>


