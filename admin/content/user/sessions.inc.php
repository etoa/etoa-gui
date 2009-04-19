<?php

	echo "<h2>Aktive Sessions</h2>";
	echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	echo "Das User-Timeout betr&auml;gt ".tf($conf['user_timeout']['v']).".";



	$res=dbquery("SELECT * FROM users WHERE user_acttime>".(time()-$conf['user_timeout']['v'])." AND user_session_key!='' ORDER BY user_acttime DESC;");
	if (mysql_num_rows($res)>0)
	{
		echo "Es sind ".mysql_num_rows($res)." Sessions aktiv. <input type=\"submit\" name=\"kick_all\" value=\"Alle User kicken\" onclick=\"return confirm('Sollen wirklich alle User aus dem Spiel geworfen werden?');\" /><br/><br/>";
		echo "<table><tr><th class=\"tbltitle\">Nick</th>
		<th class=\"tbltitle\">Login</th>
		<th class=\"tbltitle\">Letzte Aktion</th>
		<th class=\"tbltitle\">Status</th>
		<th class=\"tbltitle\">IP / Hostname</th>
		<th class=\"tbltitle\">Client</th>
		<th class=\"tbltitle\">Dauer</th>
		<th class=\"tbltitle\">Info</th>
		</tr>";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<tr><td class=\"tbldata\"><a href=\"?page=user&sub=edit&user_id=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
			<td class=\"tbldata\">".date("d.m.Y H:i",$arr['user_logintime'])."</td>
			<td class=\"tbldata\">".date("d.m.Y  H:i",$arr['user_acttime'])."</td>";
			if (time()-$conf['user_timeout']['v']< $arr['user_acttime'] && $arr['user_session_key']!='' )
			{
				echo "<td class=\"tbldata\" style=\"color:#0f0\">Online [<a href=\"?page=$page&amp;sub=$sub&amp;kick=".$arr['user_id']."\">kick</a>]</td>";
			}
			else
				echo "<td class=\"tbldata\" style=\"color:#f72\">offline</td>";
			echo "<td class=\"tbldata\">".$arr['user_ip']."<br/>".$arr['user_hostname']."</td>";
			echo "<td class=\"tbldata\">".$arr['user_client']."</td>";
			echo "<td class=\"tbldata\">";
			if (max($arr['user_logouttime'],$arr['user_acttime'])-$arr['user_logintime']>0)
				echo tf(max($arr['user_logouttime'],$arr['user_acttime'])-$arr['user_logintime']);
			else
				echo "-";
			echo "</td>";
			echo "<input type=\"hidden\" name=\"user_id\" value=\"".$arr['user_id']."\">";
			echo "<td class=\"tbldata\"><input type=\"button\" onclick=\"document.location='?page=$page&sub=$sub&id=".$arr['user_id']."'\" value=\"Info\" /></td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "</form><br/>";
	}
	else
		echo "<br/><br/><i>Keine Eintr&auml;ge vorhanden!</i><br/>";


?>
