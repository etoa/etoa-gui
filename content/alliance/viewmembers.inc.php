<?PHP
						echo "<h2>Allianzmitglieder</h2>";
						$rres = dbquery("
						SELECT
                            rank_name,
                            rank_id
						FROM
							".$db_table['alliance_ranks']."
						WHERE
							rank_alliance_id=".$s['user']['alliance_id'].";");
						while ($rarr=mysql_fetch_array($rres))
						{
							$rank[$rarr['rank_id']]=$rarr['rank_name'];
						}
						echo "<form action=\"?page=$page\" method=\"post\">";
						echo "<table width=\"500\" cellspacing=\"".TBL_SPACING."\" cellpadding=\"".TBL_PADDING."\" align=\"center\" class=\"tbl\">";
						echo "<tr>
						<td class=\"tbltitle\">Nick</td>
						<td class=\"tbltitle\">Heimatplanet</td>
						<td class=\"tbltitle\">Punkte</td>
						<td class=\"tbltitle\">Rasse</td>
						<td class=\"tbltitle\">Rang</td>
						<td class=\"tbltitle\">Attack</td>
						<td class=\"tbltitle\">Online</td>
						<td class=\"tbltitle\">Aktionen</td>";
						$ures = dbquery("SELECT u.user_acttime,u.user_id,u.user_points,u.user_nick,p.planet_id,u.user_alliance_rank_id,race_name FROM ".$db_table['users']." AS u,".$db_table['planets']." AS p,".$db_table['races']." WHERE user_race_id=race_id AND u.user_alliance_id='".$s['user']['alliance_id']."' AND p.planet_user_id=u.user_id AND p.planet_user_main=1 AND u.user_alliance_application='' GROUP BY u.user_id ORDER BY u.user_points DESC, u.user_nick;");
						while ($uarr = mysql_fetch_array($ures))
						{
							echo "<tr";
							if (time()-ONLINE_TIME< $uarr['user_last_online'])	echo " style=\"color:#0f0;\";";
							echo ">";
							echo "<td class=\"tbldata\">".$uarr['user_nick']."</td>
							<td class=\"tbldata\">".coords_format2($uarr['planet_id'],1)."</td>
							<td class=\"tbldata\">".nf($uarr['user_points'])."</td>
							<td class=\"tbldata\">".$uarr['race_name']."</td>";
							if ($arr['alliance_founder_id']==$uarr['user_id'])
								echo "<td class=\"tbldata\">Gr&uuml;nder</td>";
							elseif ($rank[$uarr['user_alliance_rank_id']]!="")
								echo "<td class=\"tbldata\">".$rank[$uarr['user_alliance_rank_id']]."</td>";
							else
								echo "<td class=\"tbldata\">-</td>";

							$num=check_fleet_incomming($uarr['user_id']);
							if ($num>0)
								echo "<td BGCOLOR=\"#FF0000\" align=\"center\">".$num."</td>";
							else
								echo "<td class=\"tbldata\">-</td>";

							if ((time()-$conf['online_threshold']['v']*60) < $uarr['user_acttime'])
								echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
							else
								echo "<td class=\"tbldata\">".date("d.m.Y H.i",$uarr['user_acttime'])."</td>";

							if ($s['user']['id']!=$uarr['user_id'])
								echo"<td class=\"tbldata\"><a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$uarr['user_id']."\">Nachricht</a></td></tr>";
							else
								echo "<td class=\"tbldata\">-</td></tr>";
						}
						echo "</table><br>";
						echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
?>