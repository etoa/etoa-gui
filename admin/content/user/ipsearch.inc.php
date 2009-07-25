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
		$ip = Net::getAddr($_GET['host']);
	else
		$ip = "";
		
	if (isset($_GET['host']) && $_GET['host']!="")
		$host = $_GET['host'];
	elseif ($ip != "")
		$host = Net::getHost($ip);
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
					COUNT(ip_addr) as cnt,
					ip_addr
				FROM	
					user_sessionlog
				WHERE
					user_id=".$user."
				GROUP BY 
					ip_addr
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
						<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['ip_addr']."\">".$arr['ip_addr']."</a></td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;host=".Net::getHost($arr['ip_addr'])."\">".Net::getHost($arr['ip_addr'])."</a></td>
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
					time_action,
					user_agent,
					ip_addr
				FROM	
					user_sessionlog
				WHERE
					user_id=".$user."
				ORDER BY
					time_action DESC
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
						<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['ip_addr']."\">".$arr['ip_addr']."</a></td>
						<td><a href=\"?page=$page&amp;sub=$sub&amp;host=".Net::getHost($arr['ip_addr'])."\">".Net::getHost($arr['ip_addr'])."</a></td>
						<td>".df($arr['time_action'])."</td>        
						<td>".$arr['user_agent']."</td>
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
			echo "<h3>User welche momentan unter dieser Adresse online sind</h3>";
			$res = dbquery("
			SELECT
				users.user_id,
				users.user_nick,
				COUNT(user_sessions.user_id) AS cnt
			FROM	
				user_sessions
			INNER JOIN
				users
			ON
				users.user_id = user_sessions.user_id
				AND user_sessions.ip_addr='".$ip."'
			GROUP BY 
				user_sessions.user_id
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
				users.user_id,
				users.user_nick,
				COUNT(user_sessionlog.user_id) AS cnt
			FROM	
				user_sessionlog
			INNER JOIN
				users
			ON
				users.user_id = user_sessionlog.user_id
				AND user_sessionlog.ip_addr='".$ip."'
			GROUP BY 
				user_sessionlog.user_id
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
			echo "<h3>User welche momentan unter dieser Adresse online sind</h3>";
			$res = dbquery("
			SELECT
				users.user_id,
				users.user_nick,
				user_sessions.time_action,
				user_sessions.user_agent,
				user_sessions.ip_addr
			FROM	
				user_sessions
			INNER JOIN
				users
			ON
				users.user_id = user_sessions.user_id
				AND user_sessions.ip_addr='".$ip."'
			ORDER BY
				time_action DESC			
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
					echo "<div id=\"tt".$arr['user_id']."\" style=\"display:none;\">
					<a href=\"?page=user&amp;sub=ipsearch&amp;user=".$arr['user_id']."\">IP-Adressen suchen</a><br/>
					<a href=\"?page=$page&amp;sub=edit&amp;id=".$arr['user_id']."\">Daten bearbeiten</a><br/>
					</div>";

					echo "<tr>
					<td><a href=\"?page=$page&amp;sub=$sub&amp;user=".$arr['user_id']."\" ".cTT($arr['user_nick'],"tt".$arr['user_id']).">".$arr['user_nick']."</a></td>
					<td>".df($arr['time_action'])."</td>        
					<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['user_ip']."\" ".mTT('IP',$arr['ip_addr']).">".($ip==$arr['ip_addr'] ? 'IP':'-')."</a> / 
					<a href=\"?page=$page&amp;sub=$sub&amp;host=".Net::getHost($arr['user_ip'])."\" ".mTT('Host',Net::getHost($arr['user_ip'])).">".($host==Net::getHost($arr['user_ip']) ? 'Host':'-')."</a></td>
					<td>".$arr['user_agent']."</td>
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
				users.user_id,
				users.user_nick,
				user_sessionlog.time_action,
				user_sessionlog.user_agent,
				user_sessionlog.ip_addr
			FROM	
				user_sessionlog
			INNER JOIN
				users
			ON
				users.user_id = user_sessionlog.user_id
				AND user_sessionlog.ip_addr='".$ip."'
			ORDER BY
				time_action DESC		
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
					echo "<div id=\"tt".$arr['user_id']."\" style=\"display:none;\">
					<a href=\"?page=user&amp;sub=ipsearch&amp;user=".$arr['user_id']."\">IP-Adressen suchen</a><br/>
					<a href=\"?page=$page&amp;sub=edit&amp;id=".$arr['user_id']."\">Daten bearbeiten</a><br/>
					</div>";

					echo "<tr>
					<td><a href=\"?page=$page&amp;sub=$sub&amp;user=".$arr['user_id']."\" ".cTT($arr['user_nick'],"tt".$arr['user_id']).">".$arr['user_nick']."</a></td>
					<td>".df($arr['time_action'])."</td>        
					<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['ip_addr']."\" ".mTT('IP',$arr['ip_addr']).">".($ip==$arr['ip_addr'] ? 'IP':'-')."</a> / 
					<a href=\"?page=$page&amp;sub=$sub&amp;host=".Net::getHost($arr['user_ip'])."\" ".mTT('Host',Net::getHost($arr['user_ip'])).">".($host==Net::getHost($arr['user_ip']) ? 'Host':'-')."</a></td>
					<td>".$arr['user_agent']."</td>
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
				failure_client
			FROM 
				login_failures 
			LEFT JOIN
				users ON
				failure_user_id=user_id
			WHERE
				failure_ip='".$ip."'
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
					echo "<div id=\"tt".$arr['user_id']."\" style=\"display:none;\">
					<a href=\"?page=user&amp;sub=ipsearch&amp;user=".$arr['user_id']."\">IP-Adressen suchen</a><br/>
					<a href=\"?page=$page&amp;sub=edit&amp;id=".$arr['user_id']."\">Daten bearbeiten</a><br/>
					</div>";					
					echo "<tr>
					<td><a href=\"?page=user&amp;sub=$sub&amp;user=".$arr['user_id']."\" ".cTT($arr['user_nick'],"tt".$arr['user_id']).">".$arr['user_nick']."</a></td>
					<td>".df($arr['failure_time'])."</td>
					<td><a href=\"?page=$page&amp;sub=$sub&amp;ip=".$arr['failure_ip']."\" ".mTT('IP',$arr['failure_ip']).">".($ip==$arr['failure_ip'] ? 'IP':'-')."</a> / 
					<a href=\"?page=$page&amp;sub=$sub&amp;host=".Net::getHost($arr['failure_ip'])."\" ".mTT('Host',Net::getHost($arr['failure_ip'])).">".($host==Net::getHost($arr['failure_ip']) ? 'Host':'-')."</a></td>
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