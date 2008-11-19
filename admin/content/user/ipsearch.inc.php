<?PHP
	echo "<h1>Ip- und Hostsuche</h1>";
	
	if (isset($_POST['search']))
	{
		$_GET['ip'] = $_POST['ip'];
		$_GET['host'] = $_POST['host'];
	}
	
	if (isset($_GET['ip'])  && $_GET['ip']!="")
		$ip = $_GET['ip'];
	elseif (isset($_GET['host'])  && $_GET['host']!="")
		$ip = resolveHostname($_GET['host']);
	else
		$ip = "";
		
	if (isset($_GET['host']) && $_GET['host']!="")
		$host = $_GET['host'];
	elseif ($ip != "")
		$host = resolveIp($ip);
	else	
		$host = "";
	
	if (isset($_GET['user']))
		$user = $_GET['user'];
	else
		$user = 0;
	
	if ($user>0)
	{
		echo "<h2>Suchergebnisse</h2>";

		$res = dbquery("
		SELECT
			user_id,
			user_nick
		FROM	
			users
		WHERE
			user_id='".$user."'
		;");
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_assoc($res);
			echo "<b>Nick:</b> <a href=\"?page=$page&amp;sub=edit&amp;id=".$user."\">".$arr['user_nick']."</a><br/>";

			if (!isset($_SESSION['admin_ipsearch_concat']))
				$_SESSION['admin_ipsearch_concat'] = false;
			if (isset($_GET['cc']) && $_GET['cc']==1)
				$_SESSION['admin_ipsearch_concat'] = true;
			if (isset($_GET['cc']) && $_GET['cc']==0)
				$_SESSION['admin_ipsearch_concat'] = false;
				
			echo "<br/>[ " ;
			if (!$_SESSION['admin_ipsearch_concat'])
				echo "<a href=\"?page=$page&amp;sub=$sub&amp;user=".$user."&amp;cc=1\">Zusammenfassung</a>";
			else	
				echo "Zusammenfassung";
			echo " | ";
			if ($_SESSION['admin_ipsearch_concat'])
				echo "<a href=\"?page=$page&amp;sub=$sub&amp;user=".$user."&amp;cc=0\">Details</a>";
			else
				echo "Details";
			echo " ]<br/>";


			echo "<h3>Adressen mit denen dieser User bereits online war</h3>";
			if ($_SESSION['admin_ipsearch_concat'])
			{
				$res = dbquery("
				SELECT
					COUNT(log_ip) as cnt,
					log_hostname,
					log_ip
				FROM	
					user_sessionlog
				WHERE
					log_user_id=".$user."
				GROUP BY 
					log_ip
				ORDER BY
					cnt DESC
				;");
				if (mysql_num_rows($res)>0)
				{
					echo "<table class=\"tb\">
					<tr>
					<th style=\"width:150px;\">Anzahl</th>
					<th style=\"width:130px;\">IP</th>
					<th style=\"width:130px;\">Host</th>
					</tr>";
					while ($arr = mysql_fetch_array($res))
					{
						echo "<tr>
						<td>".nf($arr['cnt'])."</td>  
						<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['log_ip']."\">".$arr['log_ip']."</a></td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;host=".$arr['log_hostname']."\">".$arr['log_hostname']."</a></td>
						</tr>";
					}
					echo "</table>";
				}
				else
				{
					echo "<i>Nichts gefunden!</i>";
				}	
				
				echo "<h3>Fehlgeschlagene Logins dieses Users</h3>";
				$res=dbquery("
				SELECT 
					COUNT(failure_ip) as cnt,
					failure_ip,
					failure_host
				FROM 
					login_failures 
				WHERE
					failure_user_id=".$user."
				GROUP BY
					failure_ip
				ORDER BY
					cnt DESC
				;");
				if (mysql_num_rows($res)>0)
				{
					echo "<table class=\"tb\">";
					echo "<tr>
					<th>Anzahl</a></th>
					<th>IP</a></th>
					<th>Host</th>
					</tr>";
					while ($arr=mysql_fetch_array($res))
					{
						echo "<tr>
						<td>".$arr['cnt']."</td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['failure_ip']."\">".$arr['failure_ip']."</a></td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;host=".$arr['failure_host']."\">".$arr['failure_host']."</a></td>
						</tr>";
					}
					echo "</table>";
				}
				else
				{
					echo "<i>Keine fehlgeschlagenen Logins</i>";
				}		
				
				
			}
			else
			{
				$res = dbquery("
				SELECT
					log_acttime,
					log_client,
					log_hostname,
					log_ip
				FROM	
					user_sessionlog
				WHERE
					log_user_id=".$user."
				ORDER BY
					log_acttime DESC
				;");
				if (mysql_num_rows($res)>0)
				{
					echo "<table class=\"tb\">
					<tr>
					<th style=\"width:130px;\">IP</th>
					<th style=\"width:130px;\">Host</th>
					<th style=\"width:150px;\">Datum/Zeit</th>
					<th>Client</th></tr>";
					while ($arr = mysql_fetch_array($res))
					{
						echo "<tr>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['log_ip']."\">".$arr['log_ip']."</a></td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;host=".$arr['log_hostname']."\">".$arr['log_hostname']."</a></td>
						<td>".df($arr['log_acttime'])."</td>        
						<td>".$arr['log_client']."</td>
						</tr>";
					}
					echo "</table>";
				}
				else
				{
					echo "<i>Nichts gefunden!</i>";
				}	
				
				echo "<h3>Fehlgeschlagene Logins dieses Users</h3>";
				$res=dbquery("
				SELECT 
					failure_time,
					failure_ip,
					failure_host,
					failure_client
				FROM 
					login_failures 
				WHERE
					failure_user_id=".$user."
				ORDER BY
					failure_time DESC
				;");
				if (mysql_num_rows($res)>0)
				{
					echo "<table class=\"tb\">";
					echo "<tr>
					<th>IP</a></th>
					<th>Host</th>
					<th>Datum/Zeit</th>
					<th>Client</th>
					</tr>";
					while ($arr=mysql_fetch_array($res))
					{
						echo "<tr>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['failure_ip']."\">".$arr['failure_ip']."</a></td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;host=".$arr['failure_host']."\">".$arr['failure_host']."</a></td>
						<td>".df($arr['failure_time'])."</td>
						<td>".$arr['failure_client']."</td>
						</tr>";
					}
					echo "</table>";
				}
				else
				{
					echo "<i>Keine fehlgeschlagenen Logins</i>";
				}		
				
			}

		
		
		}
		else
		{
			echo "<i>Benutzer nicht gefunden!</i>";
		}
		
		echo "<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Zurück zur Suche</a>";	
		
	}	
	
	elseif ($ip!="" || $host!="")
	{
		
		echo "<h2>Suchergebnisse</h2>";
		
		echo "<b>IP:</b> <a href=\"?page=$page&amp;sub=$sub&amp;ip=".$ip."\">".$ip."</a><br/>
		<b>Host:</b> <a href=\"?page=$page&amp;sub=$sub&amp;host=".$host."\">".$host."</a><br/>";

		if (!isset($_SESSION['admin_ipsearch_concat']))
			$_SESSION['admin_ipsearch_concat'] = false;
		if (isset($_GET['cc']) && $_GET['cc']==1)
			$_SESSION['admin_ipsearch_concat'] = true;
		if (isset($_GET['cc']) && $_GET['cc']==0)
			$_SESSION['admin_ipsearch_concat'] = false;
			
		echo "<br/>[ " ;
		if (!$_SESSION['admin_ipsearch_concat'])
			echo "<a href=\"?page=$page&amp;sub=$sub&amp;ip=".$ip."&amp;host=".$host."&amp;cc=1\">Zusammenfassung</a>";
		else	
			echo "Zusammenfassung";
		echo " | ";
		if ($_SESSION['admin_ipsearch_concat'])
			echo "<a href=\"?page=$page&amp;sub=$sub&amp;ip=".$ip."&amp;host=".$host."&amp;cc=0\">Details</a>";
		else
			echo "Details";
		echo " ]<br/>";
		
		if ($_SESSION['admin_ipsearch_concat'])
		{
			echo "<h3>User welche zuletzt unter dieser Adresse online waren</h3>";
			$res = dbquery("
			SELECT
				user_id,
				user_nick,
				COUNT(user_id) AS cnt
			FROM	
				users
			WHERE
				user_ip='".$ip."'
				OR user_hostname='".$host."'
			GROUP BY 
				user_id
			ORDER BY
				cnt DESC			
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">
				<tr>
				<th style=\"width:50px;\">Anzahl</th>
				<th>Nick</th>
				</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr>
					<td>".nf($arr['cnt'])."</td>        
					<td><a href=\"?page=$page&amp;sub=$sub&amp;user=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Nichts gefunden!</i>";
			}			
			
			echo "<h3>User welche schon mal unter dieser Adresse online waren</h3>";
			$res = dbquery("
			SELECT
				user_id,
				user_nick,
				COUNT(log_user_id) as cnt
			FROM	
				user_sessionlog
			INNER JOIN
				users
				ON user_id=log_user_id
				AND
				(
				log_ip='".$ip."'
				OR log_hostname='".$host."'
				)
			GROUP BY
				log_user_id
			ORDER BY
				cnt DESC			
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">
				<tr>
				<th style=\"width:50px;\">Anzahl</th>
				<th>Nick</th>
				</tr>";
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr>
					<td>".nf($arr['cnt'])."</td>        
					<td><a href=\"?page=$page&amp;sub=$sub&amp;user=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Nichts gefunden!</i>";
			}	
			
			echo "<h3>Fehlgeschlagene Logins unter dieser Adresse</h3>";
			$res=dbquery("
			SELECT 
				user_nick,
				user_id,
				COUNT(failure_user_id) as cnt	
			FROM 
				login_failures 
			INNER JOIN
				users ON
				failure_user_id=user_id
			WHERE
				failure_ip='".$ip."'
				OR failure_host='".$host."'
			GROUP BY
				failure_user_id
			ORDER BY
				cnt DESC
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">";
				echo "<tr>
				<th>Anzahl</a></th>
				<th>Nick</a></th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr>
					<td>".nf($arr['cnt'])."</td>
					<td><a href=\"?page=user&amp;sub=$sub&amp;user=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine fehlgeschlagenen Logins</i>";
			}				
			
		}
		else
		{
			echo "<h3>User welche zuletzt unter dieser Adresse online waren</h3>";
			$res = dbquery("
			SELECT
				user_id,
				user_nick,
				user_acttime,
				user_client,
				user_hostname,
				user_ip
			FROM	
				users
			WHERE
				user_ip='".$ip."'
				OR user_hostname='".$host."'
			ORDER BY
				user_acttime DESC			
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">
				<tr>
				<th style=\"width:130px;\">Nick</th>
				<th style=\"width:150px;\">Datum/Zeit</th>
				<th style=\"width:60px;\">Match</th>
				<th>Client</th></tr>";
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr>
					<td><a href=\"?page=$page&amp;sub=$sub&amp;user=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					<td>".df($arr['user_acttime'])."</td>        
					<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['user_ip']."\" ".tm('IP',$arr['user_ip']).">".($ip==$arr['user_ip'] ? 'IP':'-')."</a> / 
					<a href=\"?page=$page&amp;sub=$sub&amp;host=".$arr['user_hostname']."\" ".tm('Host',$arr['user_hostname']).">".($host==$arr['user_hostname'] ? 'Host':'-')."</a></td>
					<td>".$arr['user_client']."</td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Nichts gefunden!</i>";
			}
			
			echo "<h3>User welche schon mal unter dieser Adresse online waren</h3>";
			$res = dbquery("
			SELECT
				user_id,
				user_nick,
				log_acttime,
				log_client,
				log_hostname,
				log_ip
			FROM	
				user_sessionlog
			INNER JOIN
				users
				ON user_id=log_user_id
				AND
				(
				log_ip='".$ip."'
				OR log_hostname='".$host."'
				)
			ORDER BY
				log_acttime DESC			
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">
				<tr>
				<th style=\"width:130px;\">Nick</th>
				<th style=\"width:150px;\">Datum/Zeit</th>
				<th style=\"width:60px;\">Match</th>
				<th>Client</th></tr>";
				while ($arr = mysql_fetch_array($res))
				{
					echo "<tr>
					<td><a href=\"?page=$page&amp;sub=$sub&amp;user=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					<td>".df($arr['log_acttime'])."</td>        
					<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['log_ip']."\" ".tm('IP',$arr['log_ip']).">".($ip==$arr['log_ip'] ? 'IP':'-')."</a> / 
					<a href=\"?page=$page&amp;sub=$sub&amp;host=".$arr['log_hostname']."\" ".tm('Host',$arr['log_hostname']).">".($host==$arr['log_hostname'] ? 'Host':'-')."</a></td>
					<td>".$arr['log_client']."</td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Nichts gefunden!</i>";
			}	
			
			echo "<h3>Fehlgeschlagene Logins unter dieser Adresse</h3>";
			$res=dbquery("
			SELECT 
				failure_time,
				user_nick,
				user_id,
				failure_ip,
				failure_host,
				failure_client
			FROM 
				login_failures 
			LEFT JOIN
				users ON
				failure_user_id=user_id
			WHERE
				failure_ip='".$ip."'
				OR failure_host='".$host."'
			ORDER BY
				failure_time DESC
			;");
			if (mysql_num_rows($res)>0)
			{
				echo "<table class=\"tb\">";
				echo "<tr>
				<th>Nick</a></th>
				<th>Datum/Zeit</th>
				<th>Match</th>
				<th>Client</th>
				</tr>";
				while ($arr=mysql_fetch_array($res))
				{
					echo "<tr>
					<td><a href=\"?page=user&amp;sub=$sub&amp;user=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					<td>".df($arr['failure_time'])."</td>
					<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['failure_ip']."\" ".tm('IP',$arr['failure_ip']).">".($ip==$arr['failure_ip'] ? 'IP':'-')."</a> / 
					<a href=\"?page=$page&amp;sub=$sub&amp;host=".$arr['failure_host']."\" ".tm('Host',$arr['failure_host']).">".($host==$arr['failure_host'] ? 'Host':'-')."</a></td>
					<td>".$arr['failure_client']."</td>
					</tr>";
				}
				echo "</table>";
			}
			else
			{
				echo "<i>Keine fehlgeschlagenen Logins</i>";
			}			
		}
		
		echo "<br/><br/><a href=\"?page=$page&amp;sub=$sub\">Zurück zur Suche</a>";	
		
	}
	else
	{
		echo "<h2>Suchmaske</h2>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
		<table class=\"tb\">
		<tr>
			<th>IP:</th><td><input type=\"text\" name=\"ip\" value=\"\" /></td>
		</tr>
		<tr>
			<th>Host:</th><td><input type=\"text\" name=\"host\" value=\"\" /></td>
		</tr>
		</table><br/>
		<input type=\"submit\" name=\"search\" value=\"Suchen\" />
		</form>";
	}
	
?>