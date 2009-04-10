<?PHP
			iBoxStart("Logins");
			echo "Hier findest du eine Liste der letzten 10 Logins in deinen Account, ebenfalls kannst du weiter unten
			sehen wann dass fehlerhafte Loginversuche stattgefunden haben. Solltest du feststellen, dass jemand unbefugten 
			Zugriff auf deinen Account hatte, solltest du umgehend dein Passwort &auml;ndern und ein ".ticketLink("Ticket",16)." schreiben.";
			iBoxEnd();
    	tableStart("Letzte 10 Logins");
			$res=dbquery("
			SELECT 
				log_logintime,
				log_ip,
				log_hostname 
			FROM 
				user_sessionlog 
			WHERE
				log_user_id=".$cu->id."
			ORDER BY 
				log_logintime DESC
			LIMIT 
				10;");
			echo "<tr><th>Zeit</th>
			<th>IP-Adresse</th>
			<th>Hostname</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><td>".df($arr['log_logintime'])."</td>";
				echo "<td>".$arr['log_ip']."</td>";
				echo "<td>".$arr['log_hostname']."</td></tr>";
			}
    	tableEnd();
    	tableStart("Letzte 10 fehlgeschlagene Logins");
			$res=dbquery("
			SELECT 
				* 
			FROM 
				login_failures 
			WHERE
				failure_user_id=".$cu->id."
			ORDER BY 
				failure_time DESC
			LIMIT 
				10;");
			if (mysql_num_rows($res)>0)
			{
				echo "<tr><th>Zeit</th>";
				//echo "<th>Passwort</th>";
				echo "<th>IP-Adresse</th>
				<th>Hostname</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td>".df($arr['failure_time'])."</td>";
					//echo "<td>".$arr['failure_pw']."</td>";
					echo "<td>".$arr['failure_ip']."</td>";
					echo "<td>".$arr['failure_host']."</td></tr>";
				}
			}
			else
			{
				echo "<tr><td>Keine fehlgeschlagenen Logins</td></tr>";
			}
    	tableEnd();
?>