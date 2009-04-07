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
			timestamp DESC;");
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
				$res = dbquery("
				SELECT 
					*
				FROM 
					user_sessionlog
				WHERE
					log_session_key='".$si[0]."'
				LIMIT 1;");
				if (mysql_num_rows($res)>0)
				{
					$arr=mysql_fetch_array($res);
					echo "<tr>";
					echo "<td>".date("d.m.Y H:i",$arr['log_logintime'])."</td>";
					echo "<td>";
					if ($arr['log_acttime']>0)
						echo date("d.m.Y H:i",$arr['log_acttime']);
					else
						echo "-";
					echo "</td>";
					echo "<td>";
					$dur = max($arr['log_logouttime'],$arr['log_acttime'])-$arr['log_logintime'];
					if ($dur>0)
						echo tf($dur);
					else
						echo "-";
					echo "</td>
					<td>".$si[1]."</td>
					<td>".($dur>0 ? round($si[1] / $dur * 60,1) : '-')."</td>
					<td><a href=\"javascript:;\" onclick=\"toggleBox('details".$arr['log_id']."')\">Details</a></td>
					</tr>";
				}
				echo "<tr>
				<td colspan=\"6\" id=\"details".$arr['log_id']."\" style=\"display:none;\">";
				echo "<b>IP:</b> ".$arr['log_ip'].", &nbsp; 
				<b>Host:</b> ".$arr['log_hostname'].", &nbsp; ";
				echo "<b>Client:</b> ".$arr['log_client']."<br/>";
				
				$res = dbquery("SELECT * FROM user_surveillance WHERE session='".$si[0]."' ORDER BY timestamp DESC;");
				if (mysql_num_rows($res)>0)
				{
					tableStart("","100%");
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
				
				echo "</td>
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
			user_nick,
			user_points,
			user_id,
			user_observe,
			user_acttime,
			user_session_key
		FROM 
			users
		WHERE 
			user_observe!=''
		ORDER BY
			user_nick	
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
					if (time()-$conf['user_timeout']['v']< $arr['user_acttime'] && $arr['user_session_key']!='' )
					{
						echo "<td style=\"color:#0f0\">Online</td>";
					}
					else
						echo "<td>".df($arr['user_acttime'])."</td>";
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
