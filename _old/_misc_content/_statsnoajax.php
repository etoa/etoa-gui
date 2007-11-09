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
	// 	Dateiname: stats.php
	// 	Topic: Statistikanzeige
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar:
	//
	// DEFINITIONEN //


	// BEGIN SKRIPT //

	echo "<h1>Statistiken</h1>";

	//
	// Details anzeigen
	//

	if ($_GET['userdetail']>0)
	{
		$res=dbquery("SELECT user_nick,user_points,user_rank_current,user_id FROM ".$db_table['users']." WHERE user_id='".$_GET['userdetail']."';");
		if (mysql_num_rows($res)>0)
		{
			$arr=mysql_fetch_array($res);
			echo "<h2>Punktedetails für ".$arr['user_nick']."</h2>";
			echo "<b>Punkte aktuell:</b> ".nf($arr['user_points']).", <b>Rang aktuell:</b> ".$arr['user_rank_current']."<br/><br/>";
			echo "<img src=\"inc/statsdiag.php?user=".$arr['user_id']."\" alt=\"Diagramm\" /><br/><br/>";
			$pres=dbquery("SELECT * FROM ".$db_table['user_points']." WHERE point_user_id='".$_GET['userdetail']."' ORDER BY point_timestamp DESC LIMIT 48; ");
			if (mysql_num_rows($pres)>0)
			{
				$points=array();
				while ($parr=mysql_fetch_array($pres))
				{
					$points[$parr['point_timestamp']]=$parr['point_points'];
					$fleet[$parr['point_timestamp']]=$parr['point_ship_points'];
					$tech[$parr['point_timestamp']]=$parr['point_tech_points'];
					$buildings[$parr['point_timestamp']]=$parr['point_building_points'];
				}
				echo "<table width=\"400\" class=\"tbl\">";
				echo "<tr><th class=\"tbltitle\">Datum</th><th class=\"tbltitle\">Zeit</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Flotte</th><th class=\"tbltitle\">Forschung</th><th class=\"tbltitle\">Gebäude</th></tr>";
				foreach ($points as $time=>$val)
				{
					echo "<tr><td class=\"tbldata\">".date("d.m.Y",$time)."</td><td class=\"tbldata\">".date("H:i",$time)."</td>";
					echo "<td class=\"tbldata\">".nf($val)."</td><td class=\"tbldata\">".nf($fleet[$time])."</td><td class=\"tbldata\">".nf($tech[$time])."</td><td class=\"tbldata\">".nf($buildings[$time])."</td></tr>";
				}
				echo "</table><br/>";
				echo "<input type=\"button\" value=\"Userdetails anzeigen\" onclick=\"document.location='?page=userinfo&id=".$arr['user_id']."'\" /> &nbsp; ";
			}
			else
				echo "<i>Keine Punktedaten vorhanden!</i>";
		}
		else
			echo "<i>Datensatz wurde nicht gefunden!</i>";
		if ($_GET['limit']>0) $limit=$_GET['limit']; else $limit=0;
		echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page&mode=$mode&limit=".$limit."'\" /> &nbsp; ";
	}

	//
	// Tabellen anzeigen
	//

	else
	{
		$race = get_races_array();
		$alliances = get_alliance_names();
		if ($_GET['mode']!="") $mode=$_GET['mode'];
		else $mode="user";

		// Menu
		show_tab_menu("mode",array("user"=>"Spieler","ships"=>"Flotten","tech"=>"Technologien","buildings"=>"Gebäude","alliances"=>"Allianzen","pillory"=>"Pranger"));
		echo "<br>";

		//
		// Pranger
		//
		if ($mode=="pillory")
		{
			$res = dbquery("SELECT user_nick,user_blocked_from,user_blocked_to,user_ban_reason, user_ban_admin_id FROM ".$db_table['users']." WHERE user_blocked_from<".time()." AND user_blocked_to>".time()." ORDER BY user_blocked_from DESC;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tbl\"><tr><td class=\"tbltitle\">Nick</td><td class=\"tbltitle\">Von:</td><td class=\"tbltitle\">Bis:</td><td class=\"tbltitle\">Admin</td><td class=\"tbltitle\">Grund der Sperrung</td></tr>";
				while ($arr = mysql_fetch_array($res))
				{
				$res_admin=dbquery("SELECT user_name FROM admin_users WHERE user_id=".$arr['user_ban_admin_id'].";");
				$arr_admin=mysql_fetch_array($res_admin);
					$admin = $arr_admin['username'];
					if ($arr['user_ban_admin_id']>0)
					{
						$ares = dbquery("SELECT user_nick,user_email FROM ".$db_table['admin_users']." WHERE user_id=".$arr['user_ban_admin_id']."");
						if (mysql_num_rows($ares)>0)
						{
							$aarr = mysql_fetch_array($ares);
							$admin = "<a href=\"mailto:".$aarr['user_email']."\">".$aarr['user_nick']."</a>";
						}
					}
					echo "<tr><td class=\"tbldata\" valign=\"top\" width=\"90\">".$arr['user_nick']."</td><td class=\"tbldata\" valign=\"top\">".date("d.m.Y H:i",$arr['user_blocked_from'])."</td><td class=\"tbldata\" valign=\"top\">".date("d.m.Y H:i",$arr['user_blocked_to'])."</td><td class=\"tbldata\">$admin</td><td class=\"tbldata\">".text2html($arr['user_ban_reason'])."</td></tr>";
				}
				echo "</table>";
			}
			else
				echo "<p><i>Keine Einträge vorhanden!</i></p>";
		}

		//
		// Allianzen
		//
		elseif ($mode=="alliances")
		{
			echo "<table class=\"tbl\">";

			echo "<tr>";
			echo "<th class=\"tbltitle\">#</th>";
			echo "<th class=\"tbltitle\">Tag</th>";
			echo "<th class=\"tbltitle\">Name</th>";
			if ($_GET['order_field']=="upoints")
				echo "<th class=\"tbltitle\"><i>Punkte</i> ";
			else
				echo "<th class=\"tbltitle\">Punkte ";
			echo "<a href=\"?page=$page&amp;mode=".$mode."&amp;order_field=upoints&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
			echo "<a href=\"?page=$page&amp;mode=".$mode."&amp;order_field=upoints&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
			if ($_GET['order_field']=="uavg")
				echo "<th class=\"tbltitle\"><i>User-Schnitt</i> ";
			else
				echo "<th class=\"tbltitle\">User-Schnitt ";
			echo "<a href=\"?page=$page&amp;mode=".$mode."&amp;order_field=uavg&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
			echo "<a href=\"?page=$page&amp;mode=".$mode."&amp;order_field=uavg&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
			echo "</th>";
			if ($_GET['order_field']=="cnt")
				echo "<th class=\"tbltitle\"><i>User</i> ";
			else
				echo "<th class=\"tbltitle\">User ";
			echo "<a href=\"?page=$page&amp;mode=".$mode."&amp;order_field=cnt&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
			echo "<a href=\"?page=$page&amp;mode=".$mode."&amp;order_field=cnt&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";

			echo "<th class=\"tbltitle\">Aktionen</th>";
			echo "</tr>";
			if ($_GET['order_field']!="" && $_GET['order']!="")
				$res=dbquery("
				SELECT
                    a.alliance_tag,
                    a.alliance_name,
                    a.alliance_id,
                    COUNT(*) AS cnt,
                    SUM(u.user_points) AS upoints,
                    AVG(u.user_points) AS uavg
				FROM
                    ".$db_table['alliances']." AS a,
                    ".$db_table['users']." AS u
				WHERE
                    u.user_alliance_id=a.alliance_id
                    AND u.user_alliance_application=''
                    AND user_show_stats=1
				GROUP BY
					a.alliance_id
				ORDER BY
					".$_GET['order_field']." ".$_GET['order'].",
					a.alliance_foundation_date DESC;");
			else
				$res=dbquery("SELECT a.alliance_tag,a.alliance_name,a.alliance_id,COUNT(*) AS cnt, SUM(u.user_points) AS upoints, AVG(u.user_points) AS uavg, u.user_alliance_application FROM ".$db_table['alliances']." AS a,".$db_table['users']." AS u WHERE u.user_alliance_id=a.alliance_id AND u.user_alliance_application='' AND user_show_stats=1 GROUP BY a.alliance_id ORDER BY upoints DESC,a.alliance_foundation_date DESC;");
			if (mysql_num_rows($res)>0)
			{
				$cnt=1;
				while ($arr=mysql_fetch_array($res))
				{
					if($_SESSION[ROUNDID]['user']['alliance_id']==$arr['alliance_id'] && $arr['alliance_id']!='0' && $arr['user_alliance_application']=="" && $_SESSION[ROUNDID]['user']['alliance_application']==0)
					{
						$style="style=\"color:".COLOR_ALLIANCE.";\"";
					}
					else
					{
						$style="";
					}
                        if ($arr['upoints']>0 && $conf['points_update']['p2']>0)
                            $apoints = floor($arr['upoints'] / $conf['points_update']['p2']);
                        echo "<tr>";
                        echo "<td class=\"tbldata\" align=\"right\" $style>".nf($cnt)."</td>";
                        echo "<td class=\"tbldata\" $style>".text2html($arr['alliance_tag'])."</td>";
                        echo "<td class=\"tbldata\" $style>".text2html($arr['alliance_name'])."</td>";
                        echo "<td class=\"tbldata\" $style>".nf($apoints)."</td>";
                        echo "<td class=\"tbldata\" $style>".nf($arr['uavg'])."</td>";
                        echo "<td class=\"tbldata\" $style>".nf($arr['cnt'])."</td>";
                        echo "<td class=\"tbldata\"><a href=\"?page=alliance&amp;info_id=".$arr['alliance_id']."\">Info</a></td>";
                        echo "</tr>";
                        $cnt++;
				}
			}
			else
			{
				echo "<tr><td colspan=\"5\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
			}
			echo "</table>";
		}

		//
		// User
		//
		else
		{
			// Datensatznavigation
			$usrcnt = mysql_fetch_row(dbquery("SELECT count(*) FROM ".$db_table['users']." WHERE user_show_stats=1;"));
			$num = $usrcnt[0];
			if ($_GET['limit']!="")
			{
				$limit = $_GET['limit'].",".NUM_OF_ROWS;
				$nextlimit = $_GET['limit']+NUM_OF_ROWS;
				$prevlimit = $_GET['limit']-NUM_OF_ROWS;
			}
			else
			{
				$limit = "0,".NUM_OF_ROWS;
				$nextlimit = NUM_OF_ROWS;
				$prevlimit = -1;
			}
			$lastlimit = (ceil($num/NUM_OF_ROWS)*NUM_OF_ROWS)-NUM_OF_ROWS;

			echo "<table class=\"tbl\">";
			echo "<tr>";
			if ($prevlimit>-1 && NUM_OF_ROWS*2<$num)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=0\" title=\"Erste Seite\">Erste Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			if ($prevlimit>-1)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=$prevlimit\" title=\"Vorherige Seite\">Vorherige Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			if ($nextlimit<$num)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=$nextlimit\" title=\"Nächste Seite\">Nächste Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			if ($nextlimit<$num && NUM_OF_ROWS*2<$num)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=$lastlimit\" title=\"Letzte Seite\">Letzte Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			echo "<td class=\"statsNav\"><select onchange=\"document.location='?page=$page&mode=$mode&limit='+this.options[this.selectedIndex].value\">";
			for ($x=1;$x<=$num;$x+=NUM_OF_ROWS)
			{
				$dif = $x+NUM_OF_ROWS-1;
				if ($dif>$num) $dif=$num;
				$oval=$x-1;
				echo "<option value=\"$oval\"";
				if ($limit==$oval) echo " selected=\"selected\"";
				echo ">$x - $dif</option>";
			}
			echo "</select></td></tr>";

			echo "<tr><td class=\"tbldata\" colspan=\"5\">";

			// Punktetabelle
			if ($mode=="ships")
				$order="user_points_ships";
			elseif ($mode=="tech")
				$order="user_points_tech";
			elseif ($mode=="buildings")
				$order="user_points_buildings";
			else
				$order="user_points";
			if ($_POST['search']!="" && $_POST['user_nick']!="" && checker_verify())
				$res=dbquery("SELECT user_registered,user_rank_current,user_rank_last,user_id,user_nick,user_blocked_from,user_blocked_to,user_hmode_from,user_hmode_to,user_race_id,user_alliance_application,user_alliance_id,$order AS points,user_last_online FROM ".$db_table['users']." WHERE user_nick LIKE '%".$_POST['user_nick']."%' AND user_show_stats=1 ORDER BY $order DESC,user_registered DESC,user_nick ASC;");
			else
				$res=dbquery("SELECT user_registered,user_rank_current,user_rank_last,user_id,user_nick,user_blocked_from,user_blocked_to,user_hmode_from,user_hmode_to,user_race_id,user_alliance_application,user_alliance_id,$order AS points,user_last_online FROM ".$db_table['users']." WHERE user_show_stats=1 AND user_rank_current>0 ORDER BY $order DESC,user_registered DESC,user_nick ASC LIMIT $limit;");


			echo "<form action=\"?page=$page&amp;mode=$mode\" method=\"post\">";
			checker_init();
			echo "Suche nach Spieler: <input type=\"text\" name=\"user_nick\" value=\"\" size=\"\" /> <input type=\"submit\" name=\"search\" value=\"Suchen\" />";
			echo "</td></tr>";
			echo "</table></form>";

			echo "<table width=\"600\" class=\"tbl\">";
			if (mysql_num_rows($res)>0)
			{
				echo "<tr>";
				if ($mode=="user")
					echo "<th class=\"tbltitle\" colspan=\"2\">Rang</th>";
				else
					echo "<th class=\"tbltitle\">Rang</th>";
				echo "<th class=\"tbltitle\">Nick</th><th class=\"tbltitle\">Rasse</th><th class=\"tbltitle\">Allianz</th><th class=\"tbltitle\">Punkte</th><th class=\"tbltitle\">Sektor</th><th class=\"tbltitle\" style=\"width:220px;\">Aktion</th></tr>";
				$cnt=1+$limit;
				while ($arr=mysql_fetch_array($res))
				{
					if ($arr['user_blocked_from']>0 && $arr['user_blocked_from']<time() && $arr['user_blocked_to']>time())
						$addstyle=" style=\"color:".COLOR_BANNED.";\"";
					elseif ($arr['user_hmode_from']>0 && $arr['user_hmode_from']<time())
						$addstyle=" style=\"color:".COLOR_UMOD.";\"";
					elseif ($arr['user_last_online']< time()-(USER_INACTIVE_SHOW*3600*24) && $arr['user_registered']< time()-(USER_INACTIVE_SHOW*3600*24))
						$addstyle=" style=\"color:".COLOR_INACTIVE.";\"";
					elseif ($arr['user_alliance_id']==$_SESSION[ROUNDID]['user']['alliance_id'] AND $_SESSION[ROUNDID]['user']['alliance_id']!='0' AND $_SESSION[ROUNDID]['user']['alliance_application']=='' AND $arr['user_alliance_application']=='')
						$addstyle=" style=\"color:".COLOR_ALLIANCE.";\"";
					else
						$addstyle="";
					if ($arr['user_id']==$_SESSION[ROUNDID]['user']['id'])
						$_SESSION[ROUNDID]['user']['points']=$arr['points'];
					echo "<tr";
					if ($arr['user_id']==$_SESSION[ROUNDID]['user']['id']) echo " style=\"font-weight:bold;\"";
					echo ">";
					echo "<td $addstyle class=\"tbldata\" align=\"right\">";
					if ($mode=="user")
						echo nf($arr['user_rank_current'])."</td>";
					else
						echo "$cnt (".nf($arr['user_rank_current']).")</td>";

					if ($mode=="user")
					{
						echo "<td $addstyle class=\"tbldata\" align=\"right\">";
						if ($arr['user_rank_current']==$arr['user_rank_last'])
							echo "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
						elseif ($arr['user_rank_current']<$arr['user_rank_last'])
							echo "<img src=\"images/stats/stat_up.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
						elseif ($arr['user_rank_current']>$arr['user_rank_last'])
							echo "<img src=\"images/stats/stat_down.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
						echo "</td>";
					}
					echo "<td $addstyle class=\"tbldata\">".$arr['user_nick']."</td>";
					echo "<td $addstyle class=\"tbldata\">".$race[$arr['user_race_id']]['race_name']."</td>";
					echo "<td class=\"tbldata\" $addstyle >";
					if ($arr['user_alliance_application']=="" && $arr['user_alliance_id']!="")
						echo $alliances[$arr['user_alliance_id']]['tag'];
					echo "</td>";
					echo "<td $addstyle class=\"tbldata\">".nf($arr['points'])."</td>";
					echo "<td $addstyle class=\"tbldata\">".get_sector_of_main_planet($arr['user_id'],1)."</td>";
					echo "<td $addstyle class=\"tbldata\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=".$_GET['limit']."&amp;userdetail=".$arr['user_id']."\">Punktedetails</a> ";
					echo "<a href=\"?page=userinfo&id=".$arr['user_id']."\" title=\"Userinfo\">Info</a> ";
					if ($arr['user_id']!=$_SESSION[ROUNDID]['user']['id'])
					{
						echo "<a href=\"?page=messages&mode=new&message_user_to=".$arr['user_id']."\" title=\"Nachricht senden\">Mail</a> ";
						echo "<a href=\"?page=buddylist&add_id=".$arr['user_id']."\" title=\"Info\">Buddy</a></td>";
					}
					echo "</tr>";
					$cnt++;
				}
			}
			else
				echo "<tr><td align=\"center\" class=\"tbldata\"><i>Es wurden keine Spieler gefunden!</i></tr>";
			echo "</table>";

			// Datensatznavigation

			echo "<br><table class=\"tbl\">";
			echo "<tr>";
			if ($prevlimit>-1 && NUM_OF_ROWS*2<$num)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=0\" title=\"Erste Seite\">Erste Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			if ($prevlimit>-1)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=$prevlimit\" title=\"Vorherige Seite\">Vorherige Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			if ($nextlimit<$num)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=$nextlimit\" title=\"Nächste Seite\">Nächste Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			if ($nextlimit<$num && NUM_OF_ROWS*2<$num)
				echo "<td class=\"statsNav\"><a href=\"?page=$page&amp;mode=$mode&amp;limit=$lastlimit\" title=\"Letzte Seite\">Letzte Seite</a></td>";
			else
				echo "<td class=\"statsNav\">&nbsp;</td>";
			echo "<td class=\"statsNav\"><select onchange=\"document.location='?page=$page&mode=$mode&limit='+this.options[this.selectedIndex].value\">";
			for ($x=1;$x<=$num;$x+=NUM_OF_ROWS)
			{
				$dif = $x+NUM_OF_ROWS-1;
				if ($dif>$num) $dif=$num;
				$oval=$x-1;
				echo "<option value=\"$oval\"";
				if ($limit==$oval) echo " selected=\"selected\"";
				echo ">$x - $dif</option>";
			}
			echo "</select></td></tr></table>";


		}
		// Legende
		echo "<p align=\"center\">Die Aktualisierung der <span ".tm("Punkteberechnung","F&uuml;r ".STATS_USER_POINTS."t verbaute Rohstoffe bekommt der Spieler 1 Punkt in der Statistik<br>F&uuml;r ".STATS_ALLIANCE_POINTS." Spielerpunkte bekommt die Allianz 1 Punkt in der Statisik")."><u>Punkte</u></span> erfolgt ";
		$h = $conf['points_update']['v']/3600;
		if ($h>1)
			echo "alle $h Stunden!<br>";
		elseif ($h==1)
			echo " jede Stunde!<br>";
		else
		{
			$m = $conf['points_update']['v']/60;
			echo "alle $m Minuten!<br>";
		}
		echo "Neu angemeldete Benutzer erscheinen erst nach der ersten Aktualisierung in der Liste.<br/>";
		echo "Letzte Aktualisierung: <b>".date("d.m.Y",$conf['statsupdate']['v'])."</b> um <b>".date("H:i",$conf['statsupdate']['v'])." Uhr</b><br/>";
		echo "<b>Legende:</b> <span style=\"color:".$conf['color_banned']['v'].";\">Gesperrt</span>, ";
		echo "<span style=\"color:".$conf['color_umod']['v'].";\">Urlaubsmodus</span>, ";
		echo "<span style=\"color:".$conf['color_inactive']['v'].";\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span>, ";
		echo "<span style=\"color:".$conf['color_alliance']['v'].";\">";
		if ($mode=="alliances")
			echo "Allianz";
		else
			echo "Allianzmitglied";
		echo "</span></p>";
	}
?>
