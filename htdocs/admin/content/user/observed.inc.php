<?PHP
	echo "<h1>Beobachtungsliste</h1>";
	
	if (isset($_GET['text']))
	{
		$res = dbquery("
		SELECT
			user_nick,
			user_id,
			user_observe
		FROM 
			users
		WHERE 
			user_id='".$_GET['text']."'
		");	
		$arr = mysql_Fetch_array($res);
		echo "<h2>Beobachtungsgrund für <a href=\"?page=$page&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['user_nick']."</a></h2>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
		<textarea name=\"user_observe\" cols=\"80\" rows=\"10\">".stripslashes($arr['user_observe'])."</textarea>
		<input type=\"hidden\" name=\"user_id\" value=\"".$arr['user_id']."\" />
		<br/><br/>
		<input type=\"submit\" name=\"save_text\" value=\"Speichern\" /> &nbsp; 
		<input type=\"submit\" name=\"del_text\" value=\"Löschen\" /> &nbsp; 
		<input type=\"submit\" name=\"cancel\" value=\"Abbrechen\" />";
	}
	
	//
	// Extended observation
	//
	elseif (isset($_GET['surveillance']) && $_GET['surveillance']>0)
	{
		$tu = new User($_GET['surveillance']);

		echo "<h2>Erweiterte Beobachtung von ".$tu."</h2>";
		
		if (!empty($_GET['session']))
		{
			$sid = $_GET['session'];
			
			$res = dbquery("
			SELECT 
				*
			FROM 
				user_sessionlog
			WHERE
				session_id='".$sid."'
			LIMIT 1;");
			if (mysql_num_rows($res)>0)
			{
				$arr=mysql_fetch_array($res);
			}
			else
			{
				$res = dbquery("
				SELECT
					*
				FROM
					user_sessions
				WHERE
					id='".$sid."'
				LIMIT 1;");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
				}
			}
			
			echo "<h3>Session";
			if (isset($arr['time_login']) && $arr['time_login'] > 0) {
				echo " von ".date("d.m.Y H:i",$arr['time_login']);
				if (isset($arr['time_action']) && $arr['time_action'] > 0) {
					echo " bis ". date("d.m.Y H:i",$arr['time_action']);
				}
			} else {
				echo " $sid";
			}
			echo "</h3>";
			
			echo "<p><b>IP:</b> ".$arr['ip_addr']."<br/>
			<b>Host:</b> ".Net::getHost($arr['ip_addr'])."<br/>
			<b>Client:</b> ".$arr['user_agent']."</p>";
			
			echo "<p>".button("Neu laden","?page=$page&amp;sub=$sub&amp;surveillance=".$_GET['surveillance']."&amp;session=".$_GET['session'])." &nbsp; ".
			button("Zurück","?page=$page&amp;sub=$sub&amp;surveillance=".$_GET['surveillance'])."</p>";

			$res = dbquery("SELECT * FROM user_surveillance WHERE session='".$sid."' ORDER BY timestamp DESC;");
			if (mysql_num_rows($res)>0)
			{
				tableStart("","100%");
				echo "<tr><th>Zeit</th><th>Seite</th><th>Request (GET)</th><th>Query String</th><th>Formular (POST)</th></tr>";
				while ($arr=mysql_fetch_assoc($res))
				{
					$req = wordwrap($arr['request'], 60, "\n", true);
					$reqRaw = wordwrap($arr['request_raw'], 60, "\n", true);
					$post = wordwrap($arr['post'], 60, "\n", true);
					echo "<tr>
						<td>".df($arr['timestamp'],1)."</td>
						<td>".$arr['page']."</td>
						<td>".text2html($req)."</td>
						<td>".text2html($reqRaw)."</td>
						<td>".text2html($post)."</td>
					</tr>";
				}
				tableEnd();
			}
		} 
		else 
		{
		
			$sessions = array();
			$sres = dbquery("
			SELECT 
				session,COUNT(id) 
			FROM 
				user_surveillance 
			WHERE 
				user_id=".$_GET['surveillance']." 
			GROUP BY 
				session
			ORDER BY
				timestamp DESC
			LIMIT
				50000;");
			if (mysql_num_rows($sres)>0)
			{
				while ($sarr=mysql_fetch_row($sres))
				{
					$sessions[] = array($sarr[0],$sarr[1]);		
				}			
			}

			echo "<p>Die erweiterte Beobachtung ist automatisch für User unter Beobachtung aktiv!</p>";
			echo "<p>".button("Neu laden","?page=$page&amp;sub=$sub&amp;surveillance=".$_GET['surveillance'])." &nbsp; ".button("Zurück","?page=$page&amp;sub=$sub")."</p>";

			echo "<table class=\"tb\"><tr>";
			echo "<th>Login</th>
			<th>Letzte Aktivit&auml;t</th>";
			echo "<th>Session-Dauer</th>
			<th>Aktionen</th>
			<th>Aktionen/Minute</th>
			<th>Optionen</th>
			</tr>";
			foreach ($sessions as $si)
			{
				if ($si[1]>0)
				{
					$sid = $si[0];
					$res = dbquery("
					SELECT 
						*
					FROM 
						user_sessionlog
					WHERE
						session_id='".$sid."'
					LIMIT 1;");
					if (mysql_num_rows($res)>0)
					{
						$arr=mysql_fetch_array($res);
					}
					else
					{
						$res = dbquery("
						SELECT
							*
						FROM
							user_sessions
						WHERE
							id='".$sid."'
						LIMIT 1;");
						if (mysql_num_rows($res)>0)
						{
							$arr=mysql_fetch_array($res);
						}
					}
					echo "<tr>";
					echo "<td>".(isset($arr['time_login']) && $arr['time_login'] > 0 ? date("d.m.Y H:i",$arr['time_login']) : '-')."</td>";
					echo "<td>".(isset($arr['time_action']) && $arr['time_action'] > 0 ? date("d.m.Y H:i",$arr['time_action']) : '-')."</td>";
					echo "<td>";
					$dur = max($arr['time_logout'],$arr['time_action'])-$arr['time_login'];
					if ($dur>0)
						echo tf($dur);
					else
						echo "-";
					echo "</td>
					<td>".$si[1]."</td>
					<td>".($dur>0 ? round($si[1] / $dur * 60,1) : '-')."</td>
					<td><a href=\"?page=$page&sub=$sub&surveillance=".$_GET['surveillance']."&amp;session=".$si[0]."\">Details</a></td>
					</tr>";
				}
			}
			echo "</table>";

		/*
		$res = dbquery("SELECT * FROM user_surveillance WHERE user_id=".$_GET['surveillance']." ORDER BY timestamp DESC LIMIT 1000;");
		if (mysql_num_rows($res)>0)
		{
			echo "<p>Die erweiterte Beobachtung ist automatisch für User unter Beobachtung aktiv!</p>";
			echo "<p>".button("Neu laden","?page=$page&amp;sub=$sub&amp;surveillance=".$_GET['surveillance'])." &nbsp; ".button("Zurück","?page=$page&amp;sub=$sub")."</p>";
			$tu = new User($_GET['surveillance']);
			tableStart("Aufgezeichnete Aktionen von ".$tu,"100%");
			echo "<tr><th>Zeit</th><th>Seite</th><th>Request (GET)</th><th>Formular (POST)</th></tr>";
			while ($arr=mysql_fetch_assoc($res))
			{
				$req = wordwrap($arr['request'], 60, "\n", true);
				$post = wordwrap($arr['post'], 60, "\n", true);
				echo "<tr>
					<td>".df($arr['timestamp'],1)."</td>
					<td>".$arr['page']."</td>
					<td>".text2html($req)."</td>
					<td>".text2html($post)."</td>
				</tr>";
			}
			tableEnd();
		}
		else
		{
			echo "<p>Keine Einträge vorhanden!</p>";
		}*/
		echo "<p>".button("Zurück","?page=$page&amp;sub=$sub")."</p>";
		
		}
		
	}
	
	//
	// List observed users
	//
	else
	{	
		if (isset($_POST['observe_add']))
		{
			dbquery("
			UPDATE
				users
			SET
				user_observe='".addslashes($_POST['user_observe'])."'
			WHERE
				user_id=".$_POST['user_id']."
			");
		}
		if (isset($_GET['del']))
		{
			dbquery("
			UPDATE
				users
			SET
				user_observe=''
			WHERE
				user_id=".$_GET['del']."
			");
		}
		if (isset($_POST['del_text']))
		{
			dbquery("
			UPDATE
				users
			SET
				user_observe=''
			WHERE
				user_id=".$_POST['user_id']."
			");
		}		
		if (isset($_POST['save_text']))
		{
			dbquery("
			UPDATE
				users
			SET
				user_observe='".addslashes($_POST['user_observe'])."'
			WHERE
				user_id=".$_POST['user_id']."
			");
		}	
		
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
		<fieldset>
		<legend>Hinzufügen</legend>
		<table class=\"tb\">
		<tr><th>
		Benutzer:</th><td><select name=\"user_id\">";
			$res = dbquery("
			SELECT
				user_nick,
				user_id
			FROM 
				users
			WHERE 
				user_observe=''
			ORDER BY
				user_nick	
			");
			if (mysql_num_rows($res)>0)
			{		
				while($arr=mysql_fetch_array($res))
				{
					echo "<option value=\"".$arr['user_id']."\">".$arr['user_nick']."</option>";
				}
			}
			echo "</select></td></tr>
			<tr><th>Grund:</th><td><textarea name=\"user_observe\" cols=\"80\" rows=\"5\">Multiverdacht</textarea></td></tr>
			</table><br/>
		 <input type=\"submit\" name=\"observe_add\" value=\"Zur Beobachtungsliste hinzufügen\" />
		 </fieldset>
		</form><br/>";
		
		echo "Folgende User stehen unter Beobachtung:<br/><br/>";
		$res = dbquery("
		SELECT
			users.user_nick,
			users.user_points,
			users.user_id,
			users.user_observe,
			MAX(user_sessionlog.time_action) AS time_log,
			user_sessions.time_action
		FROM 
			users
		LEFT JOIN
			user_sessionlog
		ON
			users.user_id = user_sessionlog.user_id
		LEFT JOIN
			user_sessions
		ON
			users.user_id = user_sessions.user_id
		WHERE
			users.user_observe!=''
		GROUP BY
			users.user_id
		ORDER BY
			users.user_nick	
		");
		if (mysql_num_rows($res)>0)
		{
			echo "<table class=\"tb\">
			<tr>
				<th style=\"width:150px;\">Nick</th>
				<th style=\"width:100px;\">Punkte</th>
				<th>Text</th>
				<th>Online</th>
				<th>Details</th>
				<th style=\"width:200px;\">Optionen</th>
			</tr>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>
					<td><a href=\"?page=$page&amp;sub=edit&amp;id=".$arr['user_id']."\">".$arr['user_nick']."</a></td>
					<td ".tm("Punkteverlauf","<img src=\"../misc/stats.image.php?user=".$arr['user_id']."\" alt=\"Diagramm\" style=\"width:600px;height:400px;\" />").">".nf($arr['user_points'])."</td>
					<td>".stripslashes($arr['user_observe'])."</td>";
					if ($arr['time_action'])
						echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
					elseif ($arr['time_log'])
						echo "<td class=\"tbldata\">".date("d.m.Y H:i",$arr['time_log'])."</td>";
					else
						echo "<td class=\"tbldata\">Noch nicht eingeloggt!</td>";
					$dres = dbquery("SELECT COUNT(id) FROM user_surveillance WHERE user_id=".$arr['user_id'].";");
					$dnum = mysql_fetch_row($dres);
					echo "<td>".nf($dnum[0])."</td>
					<td>
						<a href=\"?page=$page&amp;sub=$sub&amp;surveillance=".$arr['user_id']."\">Details</a>
						<a href=\"?page=$page&amp;sub=$sub&amp;text=".$arr['user_id']."\">Text ändern</a>
						<a href=\"?page=$page&amp;sub=$sub&amp;del=".$arr['user_id']."\">Entfernen</a>
					</td>
				</tr>";
			}
			echo "</table>";
		}
		else
		{
			echo "<i>Keine gefunden!</i>";
		}
	}
?>
