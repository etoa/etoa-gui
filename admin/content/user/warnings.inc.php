<?PHP
	echo "<h1>Verwarnungen</h1>";
	
	if (isset($_POST['add']))
	{
		dbquery("
		INSERT INTO
			user_warnings
		(
			warning_user_id,
			warning_date,
			warning_text,
			warning_admin_id
		)
		VALUES
		(
			".$_POST['warning_user_id'].",
			UNIX_TIMESTAMP(),
			'".addslashes($_POST['warning_text'])."',
			".$s['user_id']."
		);");
		success_msg("Verwarnung gespeichert!");		
	}
	
	
	echo "<h2>Neue Verwarnung</h2>";
	echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
		<table class=\"tb\">
			<tr>
				<th>User:</th>
				<td>
					<select name=\"warning_user_id\">";
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
						echo "</select>
				</td>
			</tr>
			<tr>
				<th>Verwarnungstext</th>
				<td><textarea name=\"warning_text\" rows=\"5\" cols=\"70\"></textarea></td>
			</tr>
		</table><br/><input type=\"submit\" name=\"add\" value=\"Neue Verwarnung erteilen\" />
	</form><br/>";
	
	echo "<h2>Bestehende Verwarnungen</h2>";
	$res = dbquery("
	SELECT
		user_nick,
		user_points,
		user_id,
		COUNT(*) as cnt
	FROM
		users
	INNER JOIN
		user_warnings
	ON
		warning_user_id=user_id
	GROUP BY
		user_id
	ORDER BY user_nick
	;");
	if (mysql_num_rows($res)>0)
	{
		echo "<table class=\"tb\">
		<tr>
			<th>Nick</th>
			<th>Punkte</th>
			<th>Verwarnungen</th>
			<th>Optionen</th>
		</tr>";
		while ($arr=mysql_fetch_array($res))
		{
			echo "<tr>
			<td>".$arr['user_nick']."</td>
			<td>".nf($arr['user_points'])."</td>
			<td>".nf($arr['cnt'])."</td>
			<td>
				<a href=\"\">Anzeigen</a>
				<a href=\"?page=$page&amp;sub=edit&amp;user_id=".$arr['user_id']."\">Daten</a>
			</td>
			</tr>
			<tr>
				<td colspan=\"4\" style=\"padding:1px;\">
					<table class=\"tb\">";
					$ures = dbquery("
					SELECT
						warning_text,
						warning_date,
						user_nick,
						warning_id
					FROM
						user_warnings
					LEFT JOIN
						admin_users
					ON
						user_id=warning_admin_id
					WHERE
						warning_user_id=".$arr['user_id']."
					ORDER BY
						warning_date DESC
					");
					while ($uarr = mysql_Fetch_array($ures))	
					{
						echo "<tr>
							<td>".stripslashes($uarr['warning_text'])."</td>
							<td>".df($uarr['warning_date'])."</td>	
							<td>Verwarnt von: <b>".$uarr['user_nick']."</b></td>	
							<td>
								<a href=\"?page=$page&amp;sub=$sub&amp;edit=".$uarr['warning_id']."\">Bearbeiten</a>
								<a href=\"?page=$page&amp;sub=$sub&amp;edit=".$uarr['warning_id']."\">Löschen</a>
							</td>
						</tr>";
					}				
					echo "</table>
				</td>
			</tr>";				
		}		
		echo "</table>";
	}
	else
	{
		echo "<i>Keine Verwarnungen vorhanden!</i>";
	}



?>