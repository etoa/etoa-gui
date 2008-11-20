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
		echo "<h2>Beobachtungsgrund für ".$arr['user_nick']."</h2>";
		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
		<textarea name=\"user_observe\" cols=\"80\" rows=\"10\">".stripslashes($arr['user_observe'])."</textarea>
		<input type=\"hidden\" name=\"user_id\" value=\"".$arr['user_id']."\" />
		<br/><br/><input type=\"submit\" name=\"save_text\" value=\"Speichern\" />";
	}
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
		if (isset($_POST['save_text']))
		{
			dbquery("
			UPDATE
				users
			SET
				user_observe='".addslashes($_POST['user_observe'])."',
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
			user_observe
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
				<th style=\"width:250px;\">Optionen</th>
			</tr>";
			while ($arr=mysql_fetch_array($res))
			{
				echo "<tr>
					<td>".$arr['user_nick']."</td>
					<td ".tm("Punkteverlauf","<img src=\"../misc/stats.image.php?user=".$arr['user_id']."\" alt=\"Diagramm\" style=\"width:600px;height:400px;\" />").">".nf($arr['user_points'])."</td>
					<td>".stripslashes($arr['user_observe'])."</td>
					<td>
						<a href=\"?page=$page&amp;sub=edit&amp;user_id=".$arr['user_id']."\">Daten</a>
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