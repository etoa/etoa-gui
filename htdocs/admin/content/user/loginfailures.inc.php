<?PHP
		echo "<h1>Fehlerhafte Logins</h1>";
		echo "Es werden maximal 300 Einträge angezeigt!<br/><br/>";

		switch (isset($_GET['order']) ? $_GET['order'] : 0)
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
			case 3:
				$order = "failure_clien ASC";
				$orderstring = "Client";
				break;
			default:
				$order = "failure_time DESC";
				$orderstring = "Datum";
		}


			$res=dbquery("
			SELECT
				failure_id,
				failure_time,
				failure_pw,
				user_nick,
				user_id,
				failure_ip,
				failure_host,
				failure_client
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
				echo "<table class=\"tb\">";
				echo "<tr>
				<th><a href=\"?page=$page&amp;sub=$sub&amp;order=0\">Zeit</a></th>
				<th><a href=\"?page=$page&amp;sub=$sub&amp;order=1\">User</a></th>
				<th><a href=\"?page=$page&amp;sub=$sub&amp;order=2\">IP-Adresse</a></th>
				<th><a href=\"?page=$page&amp;sub=$sub&amp;order=3\">Hostname</a></th>
				<th><a href=\"?page=$page&amp;sub=$sub&amp;order=4\">Client</a></th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr><td class=\"tbldata\">".df($arr['failure_time'])."</td>";
					echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['user_nick']."</a></td>";
					echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=ipsearch&amp;ip=".$arr['failure_ip']."\">".$arr['failure_ip']."</a></td>";
					echo "<td class=\"tbldata\"><a href=\"?page=user&amp;sub=ipsearch&amp;host=".$arr['failure_host']."\">".$arr['failure_host']."</a></td>
					<td class=\"tbldata\">".$arr['failure_client']."</td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine fehlgeschlagenen Logins</i>";
			}
?>
