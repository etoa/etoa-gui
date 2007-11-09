<?PHP
			infobox_start("Logins");
			echo "Hier findest du eine Liste der letzten 10 Logins in deinen Account, ebenfalls kannst du weiter unten
			sehen wann dass fehlerhafte Loginversuche stattgefunden haben. Solltest du feststellen, dass jemand unbefugten 
			Zugriff auf deinen Account hatte, solltest du umgehend dein Passwort &auml;ndern und einen Game-Admin informieren.<br/><br/>";
			infobox_end();
    	infobox_start("Letzte 10 Logins",1);
			$res=dbquery("
			SELECT 
				log_logintime,
				log_ip,
				log_hostname 
			FROM 
				".$db_table['user_log']." 
			WHERE
				log_user_id=".$s['user']['id']."
			ORDER BY 
				log_logintime DESC
			LIMIT 
				10;");
			echo "<tr><th class=\"tbltitle\">Zeit</th>
			<th class=\"tbltitle\">IP-Adresse</th>
			<th class=\"tbltitle\">Hostname</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><td class=\"tbldata\">".df($arr['log_logintime'])."</td>";
				echo "<td class=\"tbldata\">".$arr['log_ip']."</td>";
				echo "<td class=\"tbldata\">".$arr['log_hostname']."</td></tr>";
			}
    	infobox_end(1);
    	infobox_start("Letzte 10 fehlgeschlagene Logins",1);
			$res=dbquery("
			SELECT 
				* 
			FROM 
				".$db_table['login_failures']." 
			WHERE
				failure_user_id=".$s['user']['id']."
			ORDER BY 
				failure_time DESC
			LIMIT 
				10;");
			if (mysql_num_rows($res)>0)
			{
				echo "<tr><th class=\"tbltitle\">Zeit</th>";
				//echo "<th class=\"tbltitle\">Passwort</th>";
				echo "<th class=\"tbltitle\">IP-Adresse</th>
				<th class=\"tbltitle\">Hostname</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".df($arr['failure_time'])."</td>";
					//echo "<td class=\"tbldata\">".$arr['failure_pw']."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_ip']."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_host']."</td></tr>";
				}
			}
			else
			{
				echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
			}
    	infobox_end(1);
?>