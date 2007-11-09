<?PHP
		echo "<h1>Fehlerhafte Logins</h1>";
		echo "Es werden maximal 300 Einträge angezeigt!<br/><br/>";
			$res=dbquery("
			SELECT 
				failure_time,
				failure_pw,
				user_nick,
				failure_ip,
				failure_host 
			FROM 
				".$db_table['login_failures']." 
			INNER JOIN
				users ON
				failure_user_id=user_id
			ORDER BY 
				failure_time DESC
			LIMIT 
				300;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table>";
				echo "<tr><th class=\"tbltitle\">Zeit</th>
				<th class=\"tbltitle\">User</th>";
				//echo "<th class=\"tbltitle\">Passwort</th>";
				echo "<th class=\"tbltitle\">IP-Adresse</th>
				<th class=\"tbltitle\">Hostname</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".df($arr['failure_time'])."</td>";
					echo "<td class=\"tbldata\">".$arr['user_nick']."</td>";
					//echo "<td class=\"tbldata\">".$arr['failure_pw']."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_ip']."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_host']."</td></tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine fehlgeschlagenen Logins</i>";
			}		
?>