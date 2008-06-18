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
			<a href=\"?page=cell&amp;id=".$fd->getSource()->cellId()."\">".$fd->getSource()."</a><br/>";
			echo "<b>".$fd->getTarget()->entityCodeString()."</b> 
			<a href=\"?page=cell&amp;id=".$fd->getTarget()->cellId()."\">".$fd->getTarget()."</a></td>";			
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


	//Liest alle Flotten aus die auf dem weg zu einem eigenen Planeten gehen, ausser:
	//Trümmer sammeln (wo), Trümmerfeld erstellen (zo)
	$spy_tech_level = get_spy_tech($cu->id());
	$fres = dbquery("
	SELECT
      f.id,
      f.user_id,
			f.entity_from,
			f.entity_to,
      f.launchtime,
      f.landtime,
      f.action
	FROM
      fleet AS f
	ORDER BY
		f.landtime ASC;");
	if (mysql_num_rows($fres)>0)
	{
		infobox_start("Fremde Flotten",1);
		$number=mysql_num_rows($fres);
		while ($farr = mysql_fetch_array($fres))
		{
			$number--;
			$fake=0;
			$show_tarn=0;

			//Handelsflotte wird nie getarnt
			if ($farr['action']=="mo")
			{
				$show_tarn=1;
			}

      //sucht Schiffe die NICHT getarnt sind in der Flotte
      $tarn_ship_res=dbquery("
      SELECT
          s.ship_id
      FROM
          ".$db_table['fleet_ships']." AS fs
          INNER JOIN
          ".$db_table['ships']." AS s
     			ON s.ship_id=fs.fs_ship_id
     			AND fs.fs_fleet_id='".$farr['id']."'
          AND s.ship_tarned!='1';");
      if(mysql_num_rows($tarn_ship_res)>0)
      {
          $show_tarn=1;
      }

		//
		//Errechnet ob User diese Flotte sieht oder nicht
		//
			$special_ship_bonus_tarn = 0;

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

			//Gegnerische tarntech
      $tarn_res=dbquery("
      SELECT
          techlist_current_level
      FROM
          ".$db_table['techlist']."
      WHERE
          techlist_user_id='".$farr['user_id']."'
          AND techlist_tech_id='11'");
      $tarn_arr=mysql_fetch_array($tarn_res);

      //Tarn Bonus nur durch tarntechnik
      if ($tarn_arr['techlist_current_level']-$spy_tech_level<0)
      {
          $diff_time_factor=0;
      }
      elseif ($tarn_arr['techlist_current_level']-$spy_tech_level>9)
      {
          $diff_time_factor=9;
      }
      else
      {
          $diff_time_factor=$tarn_arr['techlist_current_level']-$spy_tech_level;
      }


			//Fakeangriff
			if ($farr['action']=="eo")
			{
				$farr['action']="ao";
				$fake=1;
			}


			//Opfer sieht nur Gesinnung des Gegners (Freund/Feind)
			if (SPY_TECH_SHOW_ATTITUDE<=$spy_tech_level)
			{
				$show_attitude=1;

				if ($farr['action']=="ao" || $farr['action']=="io" || $farr['action']=="so" || $farr['action']=="bo" || $farr['action']=="xo" || $farr['action']=="vo" || $farr['action']=="lo" || $farr['action']=="do" || $farr['action']=="ho")
				{
					$style = "color:#f00;";
					$action="Feindlich";
					$act_color=2;
				}
				else
				{
					$diff_time_factor=0;
					$style = "color:#0f0";
					$action="Friedlich";
					$act_color=1;
				}

			}
			//Opfer sieht nur das eine Flotte in anflug ist, sonst nichts
			else
			{
				$style = "color:#fff";
				$action="Unbekannt";
				$act_color=0;
			}
			//Opfer sieht die anzahl aller Schiffe in der Flotte
			if(SPY_TECH_SHOW_NUM<=$spy_tech_level)
			{
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
        {
						$fsres = dbquery("
						SELECT 
							SUM(fs_ship_cnt) 
						FROM 
							".$db_table['fleet_ships']." 
						WHERE 
							fs_fleet_id='".$farr['id']."'
						GROUP BY 
							fs_fleet_id;");
						$fsarr= mysql_fetch_row($fsres);
						$ships_count = $fsarr[0];        	
        }

			}
			//Opfer sieht die einzelnen Schiffstypen in der Flotte
			if(SPY_TECH_SHOW_SHIPS<=$spy_tech_level)
			{
				$show_ships = 1;
				$ship_infos = "";

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
                AND fs.fs_fleet_id='".$farr['id']."';");
        }
        $cnt=1;
        while ($fshiparr = mysql_fetch_array($fshipres))
        {

        	//Opfer sieht die genau Anzahl jedes Schifftypes in einer Flotte
            if (SPY_TECH_SHOW_NUMSHIPS<=$spy_tech_level)
            {
            	$ship_infos.= "".$fshiparr['fs_ship_cnt']." ";
            }

            $ship_infos.= "".$fshiparr['ship_name'];

            if ($cnt<mysql_num_rows($fshipres))
            {
            	$ship_infos.= ", ";
            }

            $cnt++;
        }

			}
			if (SPY_TECH_SHOW_ACTION<=$spy_tech_level)
			{
				$show_action = 1;
				if($farr['action']=='vo')
				{
					$farr['action']="ao";
				}
				$ship_action = fa($farr['action']);
			}

			$tarned = $diff_time_factor+$special_ship_bonus_tarn;
			//Flotte kann maximum zu 90% des Fluges getarnt werden, auch mit Spezialschiffsboni
			if($tarned>9)
			{
				$tarned=9;
			}


			//Zeigt die Infos an, sofern die Flotte nicht getarnt ist. (Infos richten sich nach dem spiotechlevels des opfers)
			if (time() - $farr['landtime'] - ($farr['launchtime'] - $farr['landtime']) * (1-(0.1*$tarned))>0 && $show_tarn==1)
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
				$deal=1;
				
			$ef = Entity::createFactoryById($farr['entity_from']);
			$et = Entity::createFactoryById($farr['entity_to']);
				
				echo "<tr>
								<td class=\"tbldata\" style=\"".$style."\">
									<b>".$ef->entityCodeString()."</b> ".$ef."<br/>
									<b>".$et->entityCodeString()."</b> ".$et."
								</td>
								<td class=\"tbldata\" style=\"".$style."\">
									".date("d.m.y, H:i:s",$farr['launchtime'])."<br/>
									".date("d.m.y H:i:s",$farr['landtime'])."
								</td>
								<td class=\"tbldata\" style=\"".$style."\">".$action."</td>";
								if($farr['action']=='mo')
								{
									$ress = "";
									if($farr['res_metal']>0)
									{
										$ress .= "".RES_METAL.": ".nf($farr['res_metal'])."";
									}
									
									if($farr['res_crystal']>0)
									{
										$ress .= "".RES_CRYSTAL.": ".nf($farr['res_crystal'])."";
									}
									
									if($farr['res_plastic']>0)
									{
										$ress .= "".RES_PLASTIC.": ".nf($farr['res_plastic'])."";
									}
									
									if($farr['res_fuel']>0)
									{
										$ress .= "".RES_FUEL.": ".nf($farr['res_fuel'])."";
									}
									
									if($farr['res_food']>0)
									{
										$ress .= "".RES_FOOD.": ".nf($farr['res_food'])."";
									}
									if($farr['res_metal']>0
										OR $farr['res_crystal']>0
										OR $farr['res_plastic']>0
										OR $farr['res_fuel']>0
										OR $farr['res_food']>0)
									{
										echo "<td class=\"tbldata\" ".tm("Marktanlieferung",$ress).">Markt</td>";
									}
									else
									{
										echo "<td class=\"tbldata\">Markt</td>";
									}
								}
								else
								{
									echo "<td class=\"tbldata\" style=\"".$style."\">
													<a href=\"?page=messages&mode=new&message_user_to=".$farr['user_id']."\">".get_user_nick($farr['user_id'])."</a>
												</td>";
								}
								
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
			elseif ($num!=1 && $number==0 && $deal!=1)
			{
			 	echo "<tr><td colspan=\"4\" class=\"tbldata\"><div align=\"center\">Es sind keine fremden Flotten zu deinen Planeten unterwegs!</div></td></tr>";
				$num=1;
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


