<?PHP
	echo "<h1>Rangliste</h1>";

	$mode = isset($_GET['mode']) && $_GET['mode']!="" ? $_GET['mode'] : "user";

	// Menü
	echo "<br/><table class=\"tbl\">";
	if ($mode=="user")
		echo "<tr><td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=user\" class=\"tabEnabled\">Spieler</a></td>";
	else
		echo "<tr><td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=user\" class=\"tabDefault\">Spieler</a></td>";
	if ($mode=="ships")
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=ships\" class=\"tabEnabled\">Flotten</a></td>";
	else
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=ships\" class=\"tabDefault\">Flotten</a></td>";
	if ($mode=="tech")
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=tech\" class=\"tabEnabled\">Technologien</a></td>";
	else
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=tech\" class=\"tabDefault\">Technologien</a></td>";
	if ($mode=="buildings")
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=buildings\" class=\"tabEnabled\">Geb&auml;ude</a></td>";
	else
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=buildings\" class=\"tabDefault\">Geb&auml;ude</a></td>";
	if ($mode=="alliances")
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabEnabled\">Allianzen</a></td>";
	else
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabDefault\">Allianzen</a></td>";
	if ($mode=="titles")
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=titles\" class=\"tabEnabled\">Titel</a></td></tr>";
	else
		echo "<td class=\"statsTab\" style=\"width:16%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=titles\" class=\"tabDefault\">Titel</a></td></tr>";

	echo "</table><br/>";

	if ($mode=="titles")
	{
		$titles=array();
		$titles['total']="user_points";
		$titles['fleet']="user_points_ships";
		$titles['tech']="user_points_tech";
		$titles['buildings']="user_points_buildings";
		
		infobox_start("Allgemeine Titel",1);
		$cnt = 0;
		foreach ($titles as $k => $v)
		{
			$res = dbquery("
			SELECT 
				user_nick,
				".$v.",
				user_id
			FROM 
				user_stats
			WHERE 
				user_points>".USERTITLES_MIN_POINTS." 
				AND user_show_stats
			ORDER BY 
				".$v." DESC 
			LIMIT 1;");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_row($res);
				echo "<tr>
					<th class=\"tbltitle\" style=\"width:100px;height:100px;\">
						<img src='../images/medals/medal_".$k.".png' style=\"height:100px;\" />
					</th>
					<td class=\"tbldata\" style=\"font-size:16pt;vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
						".$conf['userrank_'.$k]['v']."
					</td>
					<td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
						<span style=\"font-size:13pt;color:#ff0;\">".$arr[0]."</span><br/><br/>
						".nf($arr[1])." Punkte<br/><br/>
						[<a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr[2]."\">Profil</a>]
					</td>
				</tr>";
			}
			$cnt++;
		}
		if ($cnt==0)
		{
			echo "<tr><td class=\"tbldata\">Keine Titel vorhanden (kein Spieler hat die minimale Punktzahl zum Erwerb eines Titels erreicht)!</td></tr>";
		}
		infobox_end(1);

		infobox_start("Rassenleader",1);
		$rres = dbquery("
		SELECT
			race_id,
			race_leadertitle,
			race_name
		FROM
			races
		ORDER BY
			race_name;
		");
		while ($rarr = mysql_fetch_array($rres))
		{
			$res = dbquery("
			SELECT
				user_nick,
				user_points,
				user_id				
			FROM
				users
			WHERE
				user_race_id=".$rarr['race_id']."
				AND user_show_stats
			ORDER BY
				user_points DESC
			LIMIT 1;
			");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_row($res);
				$cres = dbquery("SELECT COUNT(user_race_id) FROM users WHERE user_race_id=".$rarr['race_id']."");
				$carr = mysql_fetch_row($cres);
				
				echo "<tr>
					<th class=\"tbltitle\" style=\"width:70px;height:70px;\">
						<img src='../images/medals/medal_race.png' style=\"height:70px;\" />
					</th>
					<td class=\"tbldata\" style=\"vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
						<div style=\"font-size:16pt;\">".$rarr['race_leadertitle']."</div>
						".$carr[0]." V&ouml;lker
					</td>	
					<td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
						<span style=\"font-size:13pt;color:#ff0;\">".$arr[0]."</span><br/><br/>
						".nf($arr[1])." Punkte &nbsp;&nbsp;&nbsp;
						[<a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr[2]."\">Profil</a>]
					</td>							
				</tr>";
			}
		}
		infobox_end(1);
		
		infobox_start("Allianzgr&uuml;nder",1);
		$res = dbquery("
		SELECT
			alliance_id,
			alliance_tag,
			alliance_name,
			user_nick,
			user_id
		FROM
			alliances
		INNER JOIN
			users 
			ON user_id=alliance_founder_id
		ORDER BY
			alliance_tag;
		");
		while ($arr = mysql_fetch_array($res))
		{				
			$cres = dbquery("SELECT COUNT(user_alliance_id) FROM users WHERE user_alliance_id=".$arr['alliance_id']."");
			$carr = mysql_fetch_row($cres);					
			echo "<tr>
				<td class=\"tbldata\" style=\"vertical-align:middle;padding:2px 10px 2px 10px;width:360px;\">
					<div style=\"font-size:13pt;padding-bottom:4px;\">[".$arr['alliance_tag']."] ".$arr['alliance_name']."</div><nr/>
					".$carr[0]." Mitglieder [<a href=\"?page=alliances&amp;sub=edit&amp;alliance_id=".$arr['alliance_id']."\">Info</a>]
				</td>	
				<td class=\"tbldata\" style=\"vertical-align:middle;padding-top:0px;padding-left:15px;\">
					<span style=\"font-size:13pt;color:#ff0;\">".$arr['user_nick']."</span>
					[<a href=\"?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."\">Profil</a>]
				</td>							
			</tr>";
		}
		infobox_end(1);


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
		if (isset($_GET['order_field']) && $_GET['order_field']=="upoints")
		{
			echo "<th class=\"tbltitle\"><i>Punkte</i> ";
		}
		else
		{
			echo "<th class=\"tbltitle\">Punkte ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=upoints&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=upoints&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		if (isset($_GET['order_field']) && $_GET['order_field']=="uavg")
		{
			echo "<th class=\"tbltitle\"><i>User-Schnitt</i> ";
		}
		else
		{
			echo "<th class=\"tbltitle\">User-Schnitt ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=uavg&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=uavg&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		echo "</th>";
		if (isset($_GET['order_field']) && $_GET['order_field']=="cnt")
		{
			echo "<th class=\"tbltitle\"><i>User</i> ";
		}
		else
		{
			echo "<th class=\"tbltitle\">User ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=cnt&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=cnt&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		
		echo "<th class=\"tbltitle\" style=\"width:60px;\">Details</th>";
		
		echo "</tr>";
		
		if (isset($_GET['order_field']) && $_GET['order_field']=="uavg")
		{
			$order="uavg";
		}
		elseif (isset($_GET['order_field']) && $_GET['order_field']=="cnt")
		{
			$order="cnt";
		}
		else
		{
			$order="upoints";
		}
		
		if (isset($_GET['order']) && $_GET['order']=="ASC")
		{
			$sort="ASC";
		}
		else
		{
			$sort="DESC";   
		}
		
		$res=dbquery("
		SELECT 
				*
		FROM 
			alliance_stats
		ORDER BY 
			$order $sort;");		
		if (mysql_num_rows($res)>0)
		{
			$cnt=1;
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>";
				echo "<td class=\"tbldata\" align=\"right\">".nf($cnt)."</td>";
				echo "<td class=\"tbldata\">".$arr['alliance_tag']."</td>";
				echo "<td class=\"tbldata\">".$arr['alliance_name']."</td>";
				echo "<td class=\"tbldata\">".nf($arr['upoints'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['uavg'])."</td>";
				echo "<td class=\"tbldata\">".nf($arr['cnt'])."</td>";
				echo "<td $addstyle class=\"tbldata\">".edit_button("?page=alliances&amp;sub=edit&amp;alliance_id=".$arr['alliance_id']."")."</td>";
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
	// Users
	//
	else
	{
		echo "<form action=\"?page=$page&amp;sub=$sub&amp;mode=$mode\" method=\"post\">";
		echo "<table class=\"tbl\">";
		echo "<tr><td class=\"tbldata\" colspan=\"5\">";
		echo "Suche nach Spieler: <input type=\"text\" name=\"user_nick\" value=\"".(isset($_POST['user_nick']) ? $_POST['user_nick'] : '')."\" size=\"20\" /> 
		<input type=\"submit\" name=\"search\" value=\"Suchen\" />";
		if (isset($_POST['user_nick']))			
		{
			echo " &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;mode=$mode'\" value=\"Reset\" />";
		}
		echo "</td></tr></table></form><br/>";

		// Punktetabelle
		if ($mode=="ships")
			$order="user_points_ships";
		elseif ($mode=="tech")
			$order="user_points_tech";
		elseif ($mode=="buildings")
			$order="user_points_buildings";
		else
			$order="user_points";
			
		if (isset($_POST['search']) && $_POST['search']!="" && $_POST['user_nick']!="")
		{
			$res=dbquery("SELECT 
				user_rank_current,
				user_rank_last,
				user_nick,
				user_blocked,
				user_hmod,
				user_inactive,	
				$order AS points,
				race_name,
				alliance_tag					
			FROM 
				user_stats
			WHERE 
				user_nick LIKE '%".$_POST['user_nick']."%' 
			ORDER BY $order DESC,
			user_nick ASC;");
		}
		else
		{
			$res=dbquery("SELECT 
				user_id,
				user_rank_current,
				user_rank_last,
				user_nick,
				$order AS points,
				race_name,
				alliance_tag,
				user_blocked,
				user_hmod,
				user_inactive				
			FROM 
				user_stats
			ORDER BY 
				$order DESC,
				user_rank_current,
				user_nick ASC 
			;");			
		}

		echo "<table class=\"tbl\">";
		if (mysql_num_rows($res)>0)
		{
			echo "<tr>
				<th class=\"tbltitle\" style=\"width:50px;\">#</th>
				<th class=\"tbltitle\" style=\"\">Nick</th>
				<th class=\"tbltitle\" style=\"\">Rasse</th>
				<th class=\"tbltitle\" style=\"\">Allianz</th>
				<th class=\"tbltitle\" style=\"\">Punkte</th>
				<th class=\"tbltitle\" style=\"width:60px;\">Details</th>
			</tr>";
			$cnt=1+$limit;
			while ($arr=mysql_fetch_array($res))
			{
				if ($arr['user_blocked']==1)
				{
					$addstyle=" style=\"color:#ffaaaa;\"";
				}
				elseif ($arr['user_hmod']==1)
				{
					$addstyle=" style=\"color:#aaffaa;\"";
				}
				elseif ($arr['user_inactive']==1)
				{
					$addstyle=" style=\"color:#aaaaaa;\"";
				}
				else
				{
					$addstyle="";
				}
				echo "<tr>";

				if ($mode=="user")
				{
					echo "<td $addstyle class=\"tbldata\" align=\"right\">".nf($arr['user_rank_current'])."";

					if ($arr['user_rank_current']==$arr['user_rank_last'])
						echo "<img src=\"../images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
					elseif ($arr['user_rank_current']<$arr['user_rank_last'])
						echo "<img src=\"../images/stats/stat_up.gif\" alt=\"down\" width=\"9\" height=\"12\" />";
					elseif ($arr['user_rank_current']>$arr['user_rank_last'])
						echo "<img src=\"../images/stats/stat_down.gif\" alt=\"up\" width=\"9\" height=\"11\" />";
				}
				else
				{
					echo "<td $addstyle class=\"tbldata\" align=\"right\">$cnt (".nf($arr['user_rank_last']).")";
				}
				echo "</td>";
				echo "<td $addstyle class=\"tbldata\">".$arr['user_nick']."</td>";
				echo "<td $addstyle class=\"tbldata\">".$arr['race_name']."</td>";
				echo "<td class=\"tbldata\" $addstyle >".$arr['alliance_tag']."</td>";
				echo "<td $addstyle class=\"tbldata\">".nf($arr['points'])."</td>";
				echo "<td $addstyle class=\"tbldata\">
				".edit_button("?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."")."
				".cb_button("add_user=".$arr['user_id']."")."				
				</td>";
				echo "</tr>";
				$cnt++;
			}
		}
		else
			echo "<tr><td align=\"center\" class=\"tbldata\"><i>Es wurde keine User gefunden!</i></tr>";
		echo "</table><br/>";

		echo "<script type=\"text/javascript\">document.forms[1].elements[0].select();</script>";

	}
	
	//
	// Legende
	//
	echo "<div style=\"text-align:center;padding:10px;\">Die Aktualisierung der Punkte erfolgt ";
	$h = $conf['points_update']['v']/3600;
	if ($h>1)
		echo "alle $h Stunden!<br/>";
	elseif ($h==1)
		echo " jede Stunde!<br/>";
	else
	{
		$m = $conf['points_update']['v']/60;
		echo "alle $m Minuten!<br/>";
	}
	echo "Letzte Aktualisierung: <b>".date("d.m.Y",$conf['statsupdate']['v'])."</b> um <b>".date("H:i",$conf['statsupdate']['v'])." Uhr</b><br/>";
	echo "<b>Legende:</b> <span style=\"color:".$conf['color_banned']['v'].";\">Gesperrt</span>, ";
	echo "<span style=\"color:".$conf['color_umod']['v'].";\">Urlaubsmodus</span>, ";
	echo "<span style=\"color:".$conf['color_inactive']['v'].";\">Inaktiv (".USER_INACTIVE_SHOW." Tage)</span></div>";

?>
