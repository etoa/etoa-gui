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
	// $Author$
	// $Date$
	// $Rev$
	//

			iBoxStart("Logins");
			echo "Hier findest du eine Liste der letzten 10 Logins in deinen Account, ebenfalls kannst du weiter unten
			sehen wann dass fehlerhafte Loginversuche stattgefunden haben. Solltest du feststellen, dass jemand unbefugten 
			Zugriff auf deinen Account hatte, solltest du umgehend dein Passwort &auml;ndern und ein ".ticketLink("Ticket",16)." schreiben.";
			iBoxEnd();
    	tableStart("Letzte 10 Logins");
			$res=dbquery("
			SELECT 
				time_login,
				ip_addr ,
				user_agent  
			FROM 
				user_sessionlog 
			WHERE
				user_id=".$cu->id."
			ORDER BY 
				time_login DESC
			LIMIT 
				10;");
			echo "<tr><th>Zeit</th>
			<th>IP-Adresse</th>
			<th>Hostname</th>
			<th>Client</th></tr>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr><td>".df($arr['time_login'])."</td>";
				echo "<td>".$arr['ip_addr']."</td>";
				echo "<td>".Net::getHost($arr['ip_addr'])."</td>";
				echo "<td>".$arr['user_agent']."</td></tr>";
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
				<th>Hostname</th>
				<th>Client</th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td>".df($arr['failure_time'])."</td>";
					//echo "<td>".$arr['failure_pw']."</td>";
					echo "<td>".$arr['failure_ip']."</td>";
					echo "<td>".Net::getHost($arr['failure_ip'])."</td>";
					echo "<td>".$arr['failure_client']."</td></tr>";
				}
			}
			else
			{
				echo "<tr><td>Keine fehlgeschlagenen Logins</td></tr>";
			}
    	tableEnd();
?>