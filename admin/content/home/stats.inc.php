<?PHP
	echo "<h1>Rangliste</h1>";

	$mode = isset($_GET['mode']) && $_GET['mode']!="" ? $_GET['mode'] : "user";

	// Menü
	echo "<br/><table class=\"tbl\">";
	if ($mode=="user")
		echo "<tr><td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=user\" class=\"tabEnabled\">Spieler</a></td>";
	else
		echo "<tr><td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=user\" class=\"tabDefault\">Spieler</a></td>";
	if ($mode=="ships")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=ships\" class=\"tabEnabled\">Flotten</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=ships\" class=\"tabDefault\">Flotten</a></td>";
	if ($mode=="tech")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=tech\" class=\"tabEnabled\">Technologien</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=tech\" class=\"tabDefault\">Technologien</a></td>";
	if ($mode=="buildings")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=buildings\" class=\"tabEnabled\">Geb&auml;ude</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=buildings\" class=\"tabDefault\">Geb&auml;ude</a></td>";
	if ($mode=="exp")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=exp\" class=\"tabEnabled\">Exp</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=exp\" class=\"tabDefault\">Exp</a></td>";


	if ($mode=="battle")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=battle\" class=\"tabEnabled\">Kampf</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=battle\" class=\"tabDefault\">Kampf</a></td>";
	if ($mode=="trade")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=trade\" class=\"tabEnabled\">Handel</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=trade\" class=\"tabDefault\">Handel</a></td>";
	if ($mode=="diplomacy")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=diplomacy\" class=\"tabEnabled\">Diplomatie</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=diplomacy\" class=\"tabDefault\">Diplomatie</a></td>";
	if ($mode=="alliances")
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabEnabled\">Allianzen</a></td>";
	else
		echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=alliances\" class=\"tabDefault\">Allianzen</a></td>";

	if (ENABLE_USERTITLES==1)
	{
		if ($mode=="titles")
			echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=titles\" class=\"tabEnabled\">Titel</a></td></tr>";
		else
			echo "<td class=\"statsTab\" ><a href=\"?page=$page&amp;sub=$sub&amp;mode=titles\" class=\"tabDefault\">Titel</a></td></tr>";
	}

	echo "</table><br/>";

	if ($mode=="titles")
	{
		include(CACHE_ROOT."/out/usertitles_a.gen");
	}
	
	//
	// Allianzen
	//

	elseif ($mode=="alliances")
	{
		echo "<table class=\"tb\">";
		echo "<tr><th colspan=\"7\" style=\"text-align:center\">Allianzrangliste</th></tr>";
			echo "<tr>";
		echo "<th>#</th>";
		echo "<th>Tag</th>";
		echo "<th>Name</th>";
		if (isset($_GET['order_field']) && $_GET['order_field']=="upoints")
		{
			echo "<th><i>Punkte</i> ";
		}
		else
		{
			echo "<th>Punkte ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=upoints&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=upoints&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		if (isset($_GET['order_field']) && $_GET['order_field']=="uavg")
		{
			echo "<th><i>User-Schnitt</i> ";
		}
		else
		{
			echo "<th>User-Schnitt ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=uavg&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=uavg&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		echo "</th>";
		if (isset($_GET['order_field']) && $_GET['order_field']=="cnt")
		{
			echo "<th><i>User</i> ";
		}
		else
		{
			echo "<th>User ";
		}
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=cnt&amp;order=DESC\" title=\"Absteigend sortieren\"><img src=\"../images/s_desc.png\" alt=\"Absteigend sortieren\" border=\"0\" /></a>";
		echo "<a href=\"?page=$page&amp;sub=$sub&amp;mode=".$mode."&amp;order_field=cnt&amp;order=ASC\" title=\"Absteigend sortieren\"><img src=\"../images/s_asc.png\" alt=\"Aufsteigend sortieren\" border=\"0\" /></a>";
		
		echo "<th style=\"width:60px;\">Details</th>";
		
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
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>";
				echo "<td align=\"right\">".nf($cnt)."</td>";
				echo "<td>".$arr['alliance_tag']."</td>";
				echo "<td>".$arr['alliance_name']."</td>";
				echo "<td>".nf($arr['upoints'])."</td>";
				echo "<td>".nf($arr['uavg'])."</td>";
				echo "<td>".nf($arr['cnt'])."</td>";
				echo "<td>".edit_button("?page=alliances&amp;sub=edit&amp;alliance_id=".$arr['alliance_id']."")."</td>";
				echo "</tr>";
			}
		}
		else
		{
			echo "<tr><td colspan=\"5\" align=\"center\"><i>Keine Allianzen in der Statistik</i></tr>";
		}
		echo "</table>";
	}
	
	//
	// Special Points
	//
	elseif($mode=="diplomacy" || $mode=="battle" || $mode=="trade")
	{
			// Punktetabelle
			if ($mode=="diplomacy")
			{
				$res=dbquery("
				SELECT 
					r.diplomacy_rating,
					user_id,
					user_nick,
					race_name,
					alliance_tag		
				FROM 
					users
				INNER JOIN
					races ON user_race_id=race_id
				INNER JOIN
					user_ratings as r ON user_id=r.id
				LEFT JOIN 
					alliances ON user_alliance_id=alliance_id
				ORDER BY 
					battle_rating DESC
				;");
				echo "<table class=\"tb\">";
				echo "<tr><th colspan=\"9\" style=\"text-align:center\">Diplomatiewertung</th></tr>";
				$cnt=1;
				if (mysql_num_rows($res)>0)
				{
					echo "<tr>
						<th style=\"width:50px;\">#</th>
						<th style=\"\">Nick</th>
						<th style=\"\">Rasse</th>
						<th style=\"\">Allianz</th>
						<th style=\"\">Bewertung</th>
						<th style=\"width:60px;\">Details</th>
					</tr>";
					while ($arr=mysql_fetch_array($res))
					{
						if ($arr['diplomacy_rating']>0)
						{
							echo "<tr>";
							echo "<td>".$cnt."</td>";
							echo "<td>".$arr['user_nick']."</td>";
							echo "<td>".$arr['race_name']."</td>";
							echo "<td >".$arr['alliance_tag']."</td>";
							echo "<td>".nf($arr['diplomacy_rating'])."</td>";
							echo "<td>
							".edit_button("?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."")."
							".cb_button("add_user=".$arr['user_id']."")."				
							</td>";
							echo "</tr>";
							$cnt++;
						}
					}
				}
				if ($cnt==1)
				{
					echo "<tr><td align=\"center\" colspan=\"8\"><i>Es wurde keine User gefunden!</i></tr>";
				}
				echo "</table><br/>";											
			}
			elseif ($mode=="battle")
			{
				$res=dbquery("
				SELECT 
					r.battles_fought,
					r.battles_won,
					r.battles_lost,
					r.battle_rating,
					user_id,
					user_nick,
					race_name,
					alliance_tag		
				FROM 
					users
				INNER JOIN
					races ON user_race_id=race_id
				INNER JOIN
					user_ratings as r ON user_id=r.id
				LEFT JOIN 
					alliances ON user_alliance_id=alliance_id
				ORDER BY 
					battle_rating DESC
				;");
				echo "<table class=\"tb\">";
				echo "<tr><th colspan=\"9\" style=\"text-align:center\">Kampfwertung</th></tr>";
				$cnt=1;
				if (mysql_num_rows($res)>0)
				{
					echo "<tr>
						<th style=\"width:50px;\">#</th>
						<th style=\"\">Nick</th>
						<th style=\"\">Rasse</th>
						<th style=\"\">Allianz</th>
						<th style=\"\">Kämpfe Gewonnen</th>
						<th style=\"\">Kämpfe Verloren</th>
						<th style=\"\">Kämpfe Total</th>
						<th style=\"\">Bewertung</th>
						<th style=\"width:60px;\">Details</th>
					</tr>";
					while ($arr=mysql_fetch_array($res))
					{
						if ($arr['battle_rating']>0)
						{
							echo "<tr>";
							echo "<td>".$cnt."</td>";
							echo "<td>".$arr['user_nick']."</td>";
							echo "<td>".$arr['race_name']."</td>";
							echo "<td >".$arr['alliance_tag']."</td>";
							echo "<td>".nf($arr['battles_won'])."</td>";
							echo "<td>".nf($arr['battles_lost'])."</td>";
							echo "<td>".nf($arr['battles_fought'])."</td>";
							echo "<td>".nf($arr['battle_rating'])."</td>";
							echo "<td>
							".edit_button("?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."")."
							".cb_button("add_user=".$arr['user_id']."")."				
							</td>";
							echo "</tr>";
							$cnt++;
						}
					}
				}
				if ($cnt==1)
				{
					echo "<tr><td align=\"center\"  colspan=\"9\"><i>Es wurde keine User gefunden!</i></tr>";
				}
				echo "</table><br/>";											
			}
			elseif ($mode=="trade")
			{
				$res=dbquery("
				SELECT 
					r.trades_sell,
					r.trades_buy,
					r.trade_rating,
					user_id,
					user_nick,
					race_name,
					alliance_tag		
				FROM 
					users
				INNER JOIN
					races ON user_race_id=race_id
				INNER JOIN
					user_ratings as r ON user_id=r.id
				LEFT JOIN 
					alliances ON user_alliance_id=alliance_id
				ORDER BY 
					battle_rating DESC
				;");
				echo "<table class=\"tb\">";
				echo "<tr><th colspan=\"9\" style=\"text-align:center\">Handelswertung</th></tr>";
				$cnt=1;
				if (mysql_num_rows($res)>0)
				{
					echo "<tr>
						<th style=\"width:50px;\">#</th>
						<th style=\"\">Nick</th>
						<th style=\"\">Rasse</th>
						<th style=\"\">Allianz</th>
						<th style=\"\">Einkäufe</th>
						<th style=\"\">Verkäufe</th>
						<th style=\"\">Bewertung</th>
						<th style=\"width:60px;\">Details</th>
					</tr>";
					while ($arr=mysql_fetch_array($res))
					{
						if ($arr['trade_rating']>0)
						{
							echo "<tr>";
							echo "<td>".$cnt."</td>";
							echo "<td>".$arr['user_nick']."</td>";
							echo "<td>".$arr['race_name']."</td>";
							echo "<td >".$arr['alliance_tag']."</td>";
							echo "<td>".nf($arr['trades_buy'])."</td>";
							echo "<td>".nf($arr['trades_sell'])."</td>";
							echo "<td>".nf($arr['trade_rating'])."</td>";
							echo "<td>
							".edit_button("?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."")."
							".cb_button("add_user=".$arr['user_id']."")."				
							</td>";
							echo "</tr>";
							$cnt++;
						}
					}
				}
				if ($cnt==1)
				{
					echo "<tr><td align=\"center\" colspan=\"8\"><i>Es wurde keine User gefunden!</i></tr>";
				}
				echo "</table><br/>";											
			}
	}		

	//
	// Calculated points
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

		$limit=0;

		// Punktetabelle
		if ($mode=="ships")
		{
			$field="points_ships";
			$order="rank_ships";
			$title="Schiffspunkte";
			$shift="rankshift_ships";
		}
		elseif ($mode=="tech")
		{
			$field="points_tech";
			$order="rank_tech";
			$title="Technologiepunkte";
			$shift="rankshift_tech";
		}
		elseif ($mode=="buildings")
		{
			$field="points_buildings";
			$order="rank_buildings";
			$title="Gebäudepunkte";
			$shift="rankshift_buildings";
		}
		elseif ($mode=="exp")
		{
			$field="points_exp";
			$order="rank_exp";
			$title="Erfahrungspunkte";
			$shift="rankshift_exp";
		}
		else
		{
			$field="points";
			$order="rank";
			$title="Gesamtpunkte";
			$shift="rankshift";
		}
		if (isset($_POST['search']) && $_POST['search']!="" && $_POST['user_nick']!="")
		{
			$res=dbquery("SELECT 
				id,
				nick,
				blocked,
				hmod,
				inactive,	
				".$order." AS rank,
				".$field." AS points,
				".$shift." AS shift,
				race_name,
				alliance_tag,
				sx,
				sy
			FROM 
				user_stats
			WHERE 
				nick LIKE '".$_POST['user_nick']."%' 
			ORDER BY 
				$order ASC,
				nick ASC;");
		}
		else
		{
			$res=dbquery("SELECT 
				id,
				nick,
				blocked,
				hmod,
				inactive,
				".$order." AS rank,
				".$field." AS points,
				".$shift." AS shift,
				race_name,
				alliance_tag,
				sx,
				sy
			FROM 
				user_stats
			ORDER BY 
				$order ASC,
				nick ASC 
			;");			
		}

		echo "<table class=\"tb\">";
		if (mysql_num_rows($res)>0)
		{
			echo "<tr><th colspan=\"7\" style=\"text-align:center;\">".$title."</th></tr>";
			echo "<tr>
				<th style=\"width:50px;\">#</th>
				<th style=\"\">Nick</th>
				<th style=\"\">Rasse</th>
				<th style=\"\">Sektor</th>
				<th style=\"\">Allianz</th>
				<th style=\"\">Punkte</th>
				<th style=\"width:60px;\">Details</th>
			</tr>";
			while ($arr=mysql_fetch_array($res))
			{
				if ($arr['blocked']==1)
				{
					$addstyle=" style=\"color:#ffaaaa;\"";
				}
				elseif ($arr['hmod']==1)
				{
					$addstyle=" style=\"color:#aaffaa;\"";
				}
				elseif ($arr['inactive']==1)
				{
					$addstyle=" style=\"color:#aaaaaa;\"";
				}
				else
				{
					$addstyle="";
				}
				echo "<tr>";

				echo "<td $addstyle  align=\"right\">".nf($arr['rank'])."";
				if ($arr['shift']==2)
					echo "<img src=\"../images/stats/stat_up.gif\" alt=\"down\" width=\"9\" height=\"12\" />";
				elseif ($arr['shift']==1)
					echo "<img src=\"../images/stats/stat_down.gif\" alt=\"up\" width=\"9\" height=\"11\" />";
				else
					echo "<img src=\"../images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
				echo "</td>";
				echo "<td $addstyle >".$arr['nick']."</td>";
				echo "<td $addstyle >".$arr['race_name']."</td>";
				echo "<td  $addstyle >".$arr['sx']."/".$arr['sy']."</td>";
				echo "<td  $addstyle >".$arr['alliance_tag']."</td>";
				echo "<td $addstyle >".nf($arr['points'])."</td>";
				echo "<td $addstyle >
				".edit_button("?page=user&amp;sub=edit&amp;user_id=".$arr['id']."")."
				".cb_button("add_user=".$arr['id']."")."				
				</td>";
				echo "</tr>";
			}
		}
		else
			echo "<tr><td align=\"center\" ><i>Es wurde keine User gefunden!</i></tr>";
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
