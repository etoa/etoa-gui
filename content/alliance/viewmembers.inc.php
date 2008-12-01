<?PHP

if (Alliance::checkActionRights('viewmembers'))
{
		echo "<h2>Allianzmitglieder</h2>";
		$rres = dbquery("
		SELECT
      rank_name,
      rank_id
		FROM
			alliance_ranks
		WHERE
			rank_alliance_id=".$arr['alliance_id'].";");
		while ($rarr=mysql_fetch_array($rres))
		{
			$rank[$rarr['rank_id']]=$rarr['rank_name'];
		}
		echo "<form action=\"?page=$page\" method=\"post\">";
		echo "<table class=\"tb\">";
		echo "<tr>
		<th>Nick</th>
		<th>Heimatplanet</th>
		<th>Punkte</th>
		<th>Rasse</th>
		<th>Rang</th>
		<th>Attack</th>
		<th>Online</th>
		<th>Aktionen</th>
		</tr>";
		$ures = dbquery("
		SELECT 
			u.user_acttime,
			u.user_id,
			u.user_points,
			u.user_nick,
			p.id as pid,
			u.user_alliance_rank_id,
			race_name 
		FROM 
			users AS u
		INNER JOIN
			planets AS p
			ON p.planet_user_id=u.user_id 
			AND p.planet_user_main=1 
		INNER JOIN
			races 
			ON user_race_id=race_id 
		WHERE
			u.user_alliance_id='".$arr['alliance_id']."'
		ORDER BY 
			u.user_points DESC, u.user_nick;");
		$time = time();
		while ($uarr = mysql_fetch_assoc($ures))
		{
			$tp = new Planet($uarr['pid']);
			echo "<tr";
			if ($time-ONLINE_TIME< $uarr['user_acttime'])	
			{
				echo " style=\"color:#0f0;\";";
			}
			echo ">";
			echo "<td class=\"tbldata\">".$uarr['user_nick']."</td>
			<td class=\"tbldata\">".$tp."</td>
			<td class=\"tbldata\">".nf($uarr['user_points'])."</td>
			<td class=\"tbldata\">".$uarr['race_name']."</td>";
			if ($arr['alliance_founder_id']==$uarr['user_id'])
			{
				echo "<td class=\"tbldata\">Gr&uuml;nder</td>";
			}
			elseif (isset($rank[$uarr['user_alliance_rank_id']]))
			{
				echo "<td class=\"tbldata\">".$rank[$uarr['user_alliance_rank_id']]."</td>";
			}
			else
			{
				echo "<td class=\"tbldata\">-</td>";
			}
			$num=check_fleet_incomming($uarr['user_id']);
			if ($num>0)
				echo "<td BGCOLOR=\"#FF0000\" align=\"center\">".$num."</td>";
			else
				echo "<td class=\"tbldata\">-</td>";

			if (($time-$conf['online_threshold']['v']*60) < $uarr['user_acttime'])
				echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
			else
				echo "<td class=\"tbldata\">".date("d.m.Y H.i",$uarr['user_acttime'])."</td>";

			echo"<td class=\"tbldata\">";
			if ($cu->id!=$uarr['user_id'])
				echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$uarr['user_id']."\">Nachricht</a> ";
				
			echo "<a href=\"?page=userinfo&amp;id=".$uarr['user_id']."\">Profil</a>";	
			echo "</td></tr>";
		}
		echo "</table><br>";
		echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
						
	}
?>