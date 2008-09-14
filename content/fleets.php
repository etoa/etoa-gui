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
	
	echo "<input type=\"button\" onclick=\"document.location='?page=fleetstats'\" value=\"Schiffs&uuml;bersicht anzeigen\" /><br/><br/>";
	
	$fm = new FleetManager($cu->id());
	$fm->loadOwn();		

	if ($fm->count() > 0)
	{
		$cdarr = array();
		
		echo "Klicke auf den Auftrag um die Details einer Flotte anzuzeigen<br/><br/>";
		infobox_start("Eigene Flotten",1);
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
		infobox_end(1);
			
		foreach ($cdarr as $elem=>$t)
		{
			countDown($elem,$t);
		}		
	}
	else
	{
		infobox_start("Eigene Flotten");
		echo "Es sind keine eigenen Flotten unterwegs!";
		infobox_end();
	}


	//
	// Gegnerische Flotten
	//


	$tl = new TechList($cu->id());
	$mySpyTech = $tl->getLevel(SPY_TECH_ID);
		
	$fm->loadForeign();
	if ($fm->count() > 0)
	{	
		infobox_start("Fremde Flotten",1);
		foreach ($fm->getAll() as $fid=>$fd)
		{
		
			$otl = new TechList($fd->ownerId());
			$opTarnTech = $otl->getLevel(TARN_TECH_ID);

	    //Tarn Bonus nur durch tarntechnik
	    if ($opTarnTech - $mySpyTech < 0)
	    {
	    	$diff_time_factor=0;
	    }
	    elseif ($opTarnTech-$mySpyTech>9)
	    {
	      $diff_time_factor=9;
	    }
	    else
	    {
	      $diff_time_factor = $opTarnTech - $mySpyTech;
	    }

			$special_ship_bonus_tarn = 0;

/*
			//Liest Tarnbonus von den Spezialschiffen aus
			$special_boni_res=dbquery("
			SELECT
	      s.special_ship_bonus_tarn,
	      fs.fs_special_ship_bonus_tarn
      FROM
      	".$db_table['fleet_ships']." AS fs
      	INNER JOIN
        ".$db_table['ships']." AS s
        ON fs.fs_ship_id=s.ship_id
        AND	fs.fs_fleet_id='".$farr['id']."'
				AND fs.fs_special_ship='1';");
      if(mysql_num_rows($special_boni_res)>0)
      {
        while ($special_boni_arr=mysql_fetch_array($special_boni_res))
        {
          $special_ship_bonus_tarn+=$special_boni_arr['special_ship_bonus_tarn'] * $special_boni_arr['fs_special_ship_bonus_tarn'];
        }
      }
*/
			//Flotte kann maximum zu 90% des Fluges getarnt werden, auch mit Spezialschiffsboni
			$tarned = $diff_time_factor+$special_ship_bonus_tarn;
			$tarned = min($tarned,9);

			// Is the attitude visible?
			if (SPY_TECH_SHOW_ATTITUDE<=$mySpyTech)
			{
				$attitude = $fd->getAction->attitude();
			}
			else
			{
				$attitude = 4;				
			}
			$attitudeColor = FleetAction::$attitudeColor[$attitude];
			$attitudeString = FleetAction::$attitudeString[$attitude];
			
			// Is the number of ships visible?
			if(SPY_TECH_SHOW_NUM<=$mySpyTech)
			{
				/*
				$show_num = 1;

				//Zählt gefakte Schiffe wenn Aktion=Fakeangriff
        if($fake==1)
        {
            $fsres = dbquery("
            SELECT 
            	SUM(fs_ship_cnt) 
            FROM 
            	".$db_table['fleet_ships']." 
            WHERE 
            	fs_fleet_id='".$farr['id']." '
            	AND fs_ship_faked='1' 
            GROUP BY 
            	fs_fleet_id;");
            $fsarr= mysql_fetch_row($fsres);
            $ships_count = $fsarr[0];
        }
				//Zählt alle nicht getarnten Schiffe bei einem Tarnangriff
        elseif($farr['action']=="vo" && $show_tarn==1)
        {
            $fsres = dbquery("
            SELECT 
            	SUM(fs_ship_cnt) 
            FROM 
            	".$db_table['fleet_ships']." 
            	INNER JOIN
            	".$db_table['ships']." 
            	ON fs_ship_id=ship_id
            	AND fs_fleet_id='".$farr['id']." '
            	AND ship_tarned!='1' 
            GROUP BY 
            	fs_fleet_id;");
            $fsarr= mysql_fetch_row($fsres);
            $ships_count = $fsarr[0];
        }
        else
        {*/
       $shipCount = $fd->countShips();
			}
			else
			{
				$shipCount = -1;
			}
			
			//Opfer sieht die einzelnen Schiffstypen in der Flotte
			$shipStr = array();
			if(SPY_TECH_SHOW_SHIPS<=$mySpyTech)
			{

/*
        if ($fake==1)
        {
            $fshipres = dbquery("
            SELECT
                fs.fs_ship_cnt,
                s.ship_name
            FROM
                ".$db_table['fleet_ships']." AS fs
               	INNER JOIN
                ".$db_table['ships']." AS s
                ON fs.fs_ship_id=s.ship_id
                AND fs.fs_fleet_id='".$farr['id']."'
                AND fs.fs_ship_faked='1';");
        }
        elseif($farr['action']=="vo" && $show_tarn==1)
        {
            $fshipres = dbquery("
            SELECT
                fs.fs_ship_cnt,
                s.ship_name
            FROM
                ".$db_table['fleet_ships']." AS fs
               	INNER JOIN
                ".$db_table['ships']." AS s
                ON fs.fs_ship_id=s.ship_id
                AND fs.fs_fleet_id='".$farr['id']."'
                AND s.ship_tarned!='1';");
        }
        else
        {*/
				foreach ($fd->getShipIds() as $sid=> $scnt)
				{
        	$str = "";
        	$ship = new Ship($sid);
        	//Opfer sieht die genau Anzahl jedes Schifftypes in einer Flotte
          if (SPY_TECH_SHOW_NUMSHIPS<=$mySpyTech)
          {
          	$str= "".$scnt." ";
          }
          $str.= "".$ship->name();
          $shipStr[] = $str;
        }
			}

			// Show action
			if (SPY_TECH_SHOW_ACTION<=$spy_tech_level)
			{
				$shipAction = $fd->getAction->displayName();
			}
			else
			{
				$shipAction = $attitudeString;
			}

			if (time() - $fd->landTime() - ($fd->launchTime() - $fd->landTime()) * (1-(0.1*$tarned))>0 )
			{
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
				echo "<td class=\"tbldata\" style=\"".$style."\">
					<a href=\"?page=messages&mode=new&message_user_to=".$fd->ownerId()."\">".get_user_nick($fd->ownerId())."</a>
												</td>";
								
								echo "</tr>";
								if ($show_num==1)
								{
									echo "<tr><td class=\"tbldata\" style=\"".$style."\" colspan=\"4\">";
									echo "<b>Anzahl:</b> ".$ships_count."";
				
									if ($show_ships==1)
									{
										echo ";<br><b>Schiffe:</b> ";
				
										echo "".$ship_infos."";
				
										if ($show_action==1)
										{
											echo ";<br><b>Vorhaben:</b> ".$ship_action."";
										}
									}
									echo "</td></tr>";
								}	
				
				
			}


		}
		infobox_end(1);
	}
	else
	{
		infobox_start("Fremde Flotten");
		echo "Es sind keine fremden Flotten zu deinen Planeten unterwegs!";
		infobox_end();
	}	
	
	



?>


