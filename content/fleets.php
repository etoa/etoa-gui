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
	* @package etoa_gameserver
	* @author MrCage <mrcage@etoa.ch>
	* @copyright Copyright (c) 2004-2007 by EtoA Gaming, www.etoa.net
	*/	

	include_once ("inc/fleet_action.inc.php");

	//
	// Eigene Flotten
	//

	echo "<h1>Flotten</h1>";
	
	echo "<input type=\"button\" onclick=\"document.location='?page=fleetstats'\" value=\"Schiffs&uuml;bersicht anzeigen\" /><br/><br/>";
	
	//Lädt Flottendaten
	$fres = dbquery("
	SELECT
		fleet_id,
		fleet_planet_from,
		fleet_planet_to,
		fleet_cell_from,
		fleet_cell_to,
		fleet_launchtime,
		fleet_landtime,
		fleet_action
	FROM
		".$db_table['fleet']."
	WHERE
		fleet_user_id='".$s['user']['id']."'
	ORDER BY
		fleet_landtime DESC;");
	if (mysql_num_rows($fres)>0)
	{
		infobox_start("Eigene Flotten",1);
		echo "<tr><td class=\"tbltitle\">Start / Ziel</td>
		<td class=\"tbltitle\">Start / Landung</td>
		<td class=\"tbltitle\">Auftrag / Status</td></tr>";
		$cnt = 1;
		while ($farr = mysql_fetch_array($fres))
		{
			echo "<tr>";
			if ($farr['fleet_planet_from']!=0)
			{
				echo"<td class=\"tbldata\">".coords_format2($farr['fleet_planet_from'],1)."<br/>";
			}
			else
			{
				echo"<td class=\"tbldata\">".coords_format4($farr['fleet_cell_from'])."<br/>";
			}
				
			if ($farr['fleet_planet_to']!=0)
			{
				echo coords_format2($farr['fleet_planet_to'],1)."</td>";
			}
			else
			{
				echo coords_format4($farr['fleet_cell_to'])."</td>";
			}
				
			echo "<td class=\"tbldata\">".date("d.m.y, H:i:s",$farr['fleet_launchtime'])."<br/>";
			echo date("d.m.y, H:i:s",$farr['fleet_landtime'])."</td>";
			echo "<td class=\"tbldata\"><a href=\"?page=fleetinfo&id=".$farr['fleet_id']."\">".fa($farr['fleet_action'])."</a><br/>";
			
			//Flotte landet
			if ($farr['fleet_landtime']<time())
			{
				if ($farr['fleet_action']=="po")
				{
					echo "Flotte wird stationiert...";
				}
				elseif ($farr['fleet_action']=="ko")
				{
					echo "Kolonie wird errichtet...";
				}
				elseif (stristr($farr['fleet_action'],"c") || stristr($farr['fleet_action'],"r"))
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
				echo "Flotte ist unterwegs";
			}
			echo "</td></tr>";
			$fleet_landtime[$cnt]=$farr['fleet_landtime'];
			$cnt++;
		}
		infobox_end(1);
	}
	else
	{
		infobox_start("Eigene Flotten");
		echo "Es sind keine eigenen Flotten unterwegs!";
		infobox_end();
	}


	//
	//Gegnerische Flotten
	//


	//Liest alle Flotten aus die auf dem weg zu einem eigenen Planeten gehen, ausser:
	//Trümmer sammeln (wo), Trümmerfeld erstellen (zo)
	$spy_tech_level = get_spy_tech($s['user']['id']);
	$fres = dbquery("
	SELECT
      f.fleet_id,
      f.fleet_user_id,
      f.fleet_planet_from,
      f.fleet_planet_to,
      f.fleet_launchtime,
      f.fleet_landtime,
      f.fleet_action
	FROM
      ".$db_table['fleet']." AS f
      INNER JOIN
      ".$db_table['planets']." AS p
			ON f.fleet_planet_to=p.planet_id
      AND f.fleet_user_id!='".$s['user']['id']."'
      AND p.planet_user_id='".$s['user']['id']."'
      AND f.fleet_action!='wo'
      AND f.fleet_action!='zo'
	ORDER BY
		f.fleet_landtime ASC;");
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
			if ($farr['fleet_action']=="mo")
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
     			AND fs.fs_fleet_id='".$farr['fleet_id']."'
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
        AND	fs.fs_fleet_id='".$farr['fleet_id']."'
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
          techlist_user_id='".$farr['fleet_user_id']."'
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
			if ($farr['fleet_action']=="eo")
			{
				$farr['fleet_action']="ao";
				$fake=1;
			}


			//Opfer sieht nur Gesinnung des Gegners (Freund/Feind)
			if (SPY_TECH_SHOW_ATTITUDE<=$spy_tech_level)
			{
				$show_attitude=1;

				if ($farr['fleet_action']=="ao" || $farr['fleet_action']=="io" || $farr['fleet_action']=="so" || $farr['fleet_action']=="bo" || $farr['fleet_action']=="xo" || $farr['fleet_action']=="vo" || $farr['fleet_action']=="lo" || $farr['fleet_action']=="do" || $farr['fleet_action']=="ho")
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
            	fs_fleet_id='".$farr['fleet_id']." '
            	AND fs_ship_faked='1' 
            GROUP BY 
            	fs_fleet_id;");
            $fsarr= mysql_fetch_row($fsres);
            $ships_count = $fsarr[0];
        }
				//Zählt alle nicht getarnten Schiffe bei einem Tarnangriff
        elseif($farr['fleet_action']=="vo" && $show_tarn==1)
        {
            $fsres = dbquery("
            SELECT 
            	SUM(fs_ship_cnt) 
            FROM 
            	".$db_table['fleet_ships']." 
            	INNER JOIN
            	".$db_table['ships']." 
            	ON fs_ship_id=ship_id
            	AND fs_fleet_id='".$farr['fleet_id']." '
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
							fs_fleet_id='".$farr['fleet_id']."'
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
                AND fs.fs_fleet_id='".$farr['fleet_id']."'
                AND fs.fs_ship_faked='1';");
        }
        elseif($farr['fleet_action']=="vo" && $show_tarn==1)
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
                AND fs.fs_fleet_id='".$farr['fleet_id']."'
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
                AND fs.fs_fleet_id='".$farr['fleet_id']."';");
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
				if($farr['fleet_action']=='vo')
				{
					$farr['fleet_action']="ao";
				}
				$ship_action = fa($farr['fleet_action']);
			}

			$tarned = $diff_time_factor+$special_ship_bonus_tarn;
			//Flotte kann maximum zu 90% des Fluges getarnt werden, auch mit Spezialschiffsboni
			if($tarned>9)
			{
				$tarned=9;
			}


			//Zeigt die Infos an, sofern die Flotte nicht getarnt ist. (Infos richten sich nach dem spiotechlevels des opfers)
			if (time() - $farr['fleet_landtime'] - ($farr['fleet_launchtime'] - $farr['fleet_landtime']) * (1-(0.1*$tarned))>0 && $show_tarn==1)
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
				echo "<tr>
								<td class=\"tbldata\" style=\"".$style."\">
									".coords_format2($farr['fleet_planet_from'],1,$act_color)."<br/>".coords_format2($farr['fleet_planet_to'],1,$act_color)."
								</td>
								<td class=\"tbldata\" style=\"".$style."\">
									".date("d.m.y, H:i:s",$farr['fleet_launchtime'])."<br/>
									".date("d.m.y H:i:s",$farr['fleet_landtime'])."
								</td>
								<td class=\"tbldata\" style=\"".$style."\">".$action."</td>";
								if($farr['fleet_action']=='mo')
								{
									$ress = "";
									if($farr['fleet_res_metal']>0)
									{
										$ress .= "".RES_METAL.": ".nf($farr['fleet_res_metal'])."";
									}
									
									if($farr['fleet_res_crystal']>0)
									{
										$ress .= "".RES_CRYSTAL.": ".nf($farr['fleet_res_crystal'])."";
									}
									
									if($farr['fleet_res_plastic']>0)
									{
										$ress .= "".RES_PLASTIC.": ".nf($farr['fleet_res_plastic'])."";
									}
									
									if($farr['fleet_res_fuel']>0)
									{
										$ress .= "".RES_FUEL.": ".nf($farr['fleet_res_fuel'])."";
									}
									
									if($farr['fleet_res_food']>0)
									{
										$ress .= "".RES_FOOD.": ".nf($farr['fleet_res_food'])."";
									}
									if($farr['fleet_res_metal']>0
										OR $farr['fleet_res_crystal']>0
										OR $farr['fleet_res_plastic']>0
										OR $farr['fleet_res_fuel']>0
										OR $farr['fleet_res_food']>0)
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
													<a href=\"?page=messages&mode=new&message_user_to=".$farr['fleet_user_id']."\">".get_user_nick($farr['fleet_user_id'])."</a>
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


