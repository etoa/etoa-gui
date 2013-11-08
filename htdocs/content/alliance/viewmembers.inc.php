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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	//

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
		tableStart();
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
			Max(user_sessionlog.time_action) as last_log,
			user_sessions.time_action,
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
		LEFT JOIN
			user_sessionlog
		ON
			u.user_id=user_sessionlog.user_id
		LEFT JOIN
			user_sessions
		ON
			u.user_id=user_sessions.user_id
		WHERE
			u.user_alliance_id='".$arr['alliance_id']."'
		GROUP BY
			u.user_id
		ORDER BY 
			u.user_points DESC, u.user_nick;");
		$time = time();
		while ($uarr = mysql_fetch_assoc($ures))
		{
			$tp = new Planet($uarr['pid']);
			echo "<tr>";
			echo "<td>".$uarr['user_nick']."</td>
			<td>".$tp."</td>
			<td>".nf($uarr['user_points'])."</td>
			<td>".$uarr['race_name']."</td>";
			if ($arr['alliance_founder_id']==$uarr['user_id'])
			{
				echo "<td>Gr&uuml;nder</td>";
			}
			elseif (isset($rank[$uarr['user_alliance_rank_id']]))
			{
				echo "<td>".$rank[$uarr['user_alliance_rank_id']]."</td>";
			}
			else
			{
				echo "<td>-</td>";
			}
			$num=check_fleet_incomming($uarr['user_id']);
			if ($num>0)
				echo "<td BGCOLOR=\"#FF0000\" align=\"center\">".$num."</td>";
			else
				echo "<td>-</td>";

			if ($uarr['time_action'])
				echo "<td style=\"color:#0f0;\">online</td>";
			elseif ($uarr['last_log'])
				echo "<td>".date("d.m.Y H:i",$uarr['last_log'])."</td>";
			else
				echo "<td>Noch nicht eingeloggt!</td>";

			echo"<td class=\"tbldata\">";
			if ($cu->id!=$uarr['user_id'])
				echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=".$uarr['user_id']."\">Nachricht</a> ";
				
			echo "<a href=\"?page=userinfo&amp;id=".$uarr['user_id']."\">Profil</a>";	
			echo "</td></tr>";
		}
		tableEnd();
		echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
						
	}
?>