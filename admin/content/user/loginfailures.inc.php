<?PHP
		echo "<h1>Fehlerhafte Logins</h1>";
		echo "Es werden maximal 300 Einträge angezeigt!<br/><br/>";
		
		switch ($_GET['order'])
		{
			case 1:
				$order = "user_nick ASC";
				$orderstring = "Nickname";
				break;
			case 2:
				$order = "failure_ip ASC";
				$orderstring = "IP";
				break;
			case 3:
				$order = "failure_host ASC";
				$orderstring = "Host";
				break;			
			default:
				$order = "failure_time DESC";
				$orderstring = "Datum";
		}
		
		
			$res=dbquery("
			SELECT 
				failure_time,
				failure_pw,
				user_nick,
				user_id,
				failure_ip,
				failure_host 
			FROM 
				login_failures 
			INNER JOIN
				users ON
				failure_user_id=user_id
			ORDER BY 
				".$order."
			LIMIT 
				300;");
			if (mysql_num_rows($res)>0)
			{
				echo "Sortiert nach: ".$orderstring."<br/><br/>";
				echo "<table>";
				echo "<tr><th class=\"tbltitle\"><a href=\"?page=$page&amp;sub=$sub&amp;order=0\">Zeit</a></th>
				<th class=\"tbltitle\"><a href=\"?page=$page&amp;sub=$sub&amp;order=1\">User</a></th>";
				//echo "<th class=\"tbltitle\">Passwort</th>";
				echo "<th class=\"tbltitle\"><a href=\"?page=$page&amp;sub=$sub&amp;order=2\">IP-Adresse</a></th>
				<th class=\"tbltitle\"><a href=\"?page=$page&amp;sub=$sub&amp;order=3\">Hostname</a></th></tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".df($arr['failure_time'])."</td>";
					echo "<td class=\"tbldata\">".$arr['user_nick']."</td>";
					//echo "<td class=\"tbldata\">".$arr['failure_pw']."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_ip']."</td>";
					echo "<td class=\"tbldata\">".$arr['failure_host']."</td>
					<td class=\"tbldata\">
						".edit_button("?page=user&amp;sub=edit&amp;user_id=".$arr['user_id']."")."
						".cb_button("add_user=".$arr['user_id']."")."				
					</td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine fehlgeschlagenen Logins</i>";
			}		
?>