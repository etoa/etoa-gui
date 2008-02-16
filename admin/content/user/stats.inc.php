<?PHP
	echo "<h1>Rangliste</h1>";

	$mode = isset($_GET['mode']) && $_GET['mode']!="" ? $_GET['mode'] : "user";

	// Menü
	echo "<br/><table class=\"tbl\">";
	if ($mode=="user")
		echo "<tr><td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=user\" class=\"tabEnabled\">Spieler</a></td>";
	else
		echo "<tr><td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=user\" class=\"tabDefault\">Spieler</a></td>";
	if ($mode=="ships")
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=ships\" class=\"tabEnabled\">Flotten</a></td>";
	else
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=ships\" class=\"tabDefault\">Flotten</a></td>";
	if ($mode=="tech")
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=tech\" class=\"tabEnabled\">Technologien</a></td>";
	else
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=tech\" class=\"tabDefault\">Technologien</a></td>";
	if ($mode=="buildings")
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=buildings\" class=\"tabEnabled\">Geb&auml;ude</a></td>";
	else
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=buildings\" class=\"tabDefault\">Geb&auml;ude</a></td>";
	if ($mode=="alliances")
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabEnabled\">Allianzen</a></td></tr>";
	else
		echo "<td class=\"statsTab\" style=\"width:18%;\"><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabDefault\">Allianzen</a></td></tr>";
	echo "</table><br/>";
	
	//
	// Allianzen
	//

	if ($mode=="alliances")
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
		echo "<a href=\"?index=stats&amp;mode=".$mode."&amp;order_field=upoints&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?index=stats&amp;mode=".$mode."&amp;order_field=upoints&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		if (isset($_GET['order_field']) && $_GET['order_field']=="uavg")
		{
			echo "<th class=\"tbltitle\"><i>User-Schnitt</i> ";
		}
		else
		{
			echo "<th class=\"tbltitle\">User-Schnitt ";
		}
		echo "<a href=\"?index=stats&amp;mode=".$mode."&amp;order_field=uavg&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?index=stats&amp;mode=".$mode."&amp;order_field=uavg&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		echo "</th>";
		if (isset($_GET['order_field']) && $_GET['order_field']=="cnt")
		{
			echo "<th class=\"tbltitle\"><i>User</i> ";
		}
		else
		{
			echo "<th class=\"tbltitle\">User ";
		}
		echo "<a href=\"?index=stats&amp;mode=".$mode."&amp;order_field=cnt&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?index=stats&amp;mode=".$mode."&amp;order_field=cnt&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\".//images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
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
	else
	{
		
		/*
		// Datensatznavigation
		$usrcnt = mysql_fetch_row(dbquery("
		SELECT 
			COUNT(user_id) 
		FROM 
			user_stats 
		"));
		$num = $usrcnt[0];
		if (isset($_GET['limit']) && $_GET['limit']!="")
		{
			$limit = intval($_GET['limit']).",".NUM_OF_ROWS;
			$nextlimit = intval($_GET['limit'])+NUM_OF_ROWS;
			$prevlimit = intval($_GET['limit'])-NUM_OF_ROWS;
		}
		else
		{
			$limit = "0,".NUM_OF_ROWS;
			$nextlimit = NUM_OF_ROWS;
			$prevlimit = -1;
		}
		$lastlimit = (ceil($num/NUM_OF_ROWS)*NUM_OF_ROWS)-NUM_OF_ROWS;
		*/
		echo "<form action=\"?page=$page&amp;sub=$sub&amp;mode=$mode\" method=\"post\"><table class=\"tbl\">";
		/*
		echo "<tr><td class=\"statsNav\" style=\"text-align:right;\">";
		if ($prevlimit>-1 && NUM_OF_ROWS*2<$num)
			echo "<input type=\"button\" value=\" &lt;&lt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=0'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		if ($prevlimit>-1)
			echo "<input type=\"button\" value=\" &lt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=$prevlimit'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		if ($nextlimit<$num)
			echo "<input type=\"button\" value=\" &gt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=$nextlimit'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		if ($nextlimit<$num && NUM_OF_ROWS*2<$num)
			echo "<input type=\"button\" value=\" &gt;&gt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=$lastlimit'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		echo "<select onchange=\"document.location='?index=stats&amp;mode=$mode&amp;limit='+this.options[this.selectedIndex].value\">";
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
		*/
		echo "<tr><td class=\"tbldata\" colspan=\"5\">";
		echo "Suche nach Spieler: <input type=\"text\" name=\"user_nick\" value=\"\" size=\"20\" /> <input type=\"submit\" name=\"search\" value=\"Suchen\" />";
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
		
		/*LIMIT 
				$limit*/
		echo "<table class=\"tbl\">";
		if (mysql_num_rows($res)>0)
		{
			echo "<tr>
				<th class=\"tbltitle\" style=\"width:50px;\">#</th>
				<th class=\"tbltitle\" style=\"\">Nick</th>
				<th class=\"tbltitle\" style=\"\">Rasse</th>
				<th class=\"tbltitle\" style=\"\">Allianz</th>
				<th class=\"tbltitle\" style=\"\">Punkte</th>
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
				echo "</tr>";
				$cnt++;
			}
		}
		else
			echo "<tr><td align=\"center\" class=\"tbldata\"><i>Es wurde keine User gefunden!</i></tr>";
		echo "</table><br/>";

/*
		// Datensatznavigation
		echo "<table class=\"tbl\">";
		echo "<tr><td class=\"statsNav\" style=\"text-align:right;\">";
		if ($prevlimit>-1 && NUM_OF_ROWS*2<$num)
			echo "<input type=\"button\" value=\" &lt;&lt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=0'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		if ($prevlimit>-1)
			echo "<input type=\"button\" value=\" &lt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=$prevlimit'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		if ($nextlimit<$num)
			echo "<input type=\"button\" value=\" &gt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=$nextlimit'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		if ($nextlimit<$num && NUM_OF_ROWS*2<$num)
			echo "<input type=\"button\" value=\" &gt;&gt; \" onclick=\"document.location='?index=stats&amp;mode=$mode&amp;limit=$lastlimit'\" /> &nbsp; ";
		else
			echo "&nbsp;";
		echo "<select onchange=\"document.location='?index=stats&amp;mode=$mode&amp;limit='+this.options[this.selectedIndex].value\">";
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
		echo "</table>";
		*/

	}
	// Legende
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