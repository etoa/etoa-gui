<?PHP
		// Ignorierung hinzufügen
		if ((isset($_POST['submit_ignore']) && $_POST['target_id']>0) || isset($_GET['add']) && $_GET['add']>0)
		{
			if ($_GET['add'])
			{
				$_POST['target_id']=$_GET['add'];
			}
			dbquery("
			DELETE FROM
				message_ignore
			WHERE
				ignore_owner_id=".$cu->id."
				AND ignore_target_id=".intval($_POST['target_id'])."
			;");				
			dbquery("
			INSERT INTO
				message_ignore
			(
				ignore_owner_id,
				ignore_target_id
			)
			VALUES
			(
			  ".$cu->id.",
			  ".$_POST['target_id']."
			)
			");		
			echo "Spieler wurde ignoriert!<br/><br/>";
		}
		
		
		// Ignorierung löschen
		if (isset($_GET['remove']) && $_GET['remove']>0)
		{
			dbquery("
			DELETE FROM
				message_ignore
			WHERE
				ignore_owner_id=".$cu->id."
				AND ignore_target_id=".intval($_GET['remove'])."
			;");		
			echo "Spieler wurde von der Liste entfernt!<br/><br/>";
		}		
		
		echo 'Falls du von einem Benutzer belästigt wirst kannst du ihn hier ignorieren:<br/><br/>';
		$res=dbquery("SELECT
			user_id,
			user_nick
		FROM
			users
		WHERE
			user_id!=".$cu->id."
		ORDER BY
			user_nick");
		if (mysql_num_rows($res)>0)
		{
			echo '<form action="?page='.$page.'&amp;mode='.$mode.'" method="post"><div>
			<select name="target_id"><option value="0">Spieler wählen...</option>';
			while ($arr=mysql_fetch_array($res))
			{
				echo '<option value="'.$arr['user_id'].'">'.$arr['user_nick'].'</option>';
			}
			echo '</select> <input type="submit" name="submit_ignore" value="Nachrichten dieses Spielers ignorieren" /></div></form><br/>';
		}
		
		// Spieler die man ignoriert
		$res=dbquery("SELECT
			user_id,
			user_nick
		FROM
			message_ignore			
		INNER JOIN
			users
			ON ignore_target_id=user_id
			AND ignore_owner_id=".$cu->id."
		ORDER BY
			user_nick");		
		if (mysql_num_rows($res)>0)
		{
			echo '<table class="tb"><tr><th>Spieler</th><th>Aktionen</th></tr>';
			while ($arr=mysql_fetch_array($res))
			{
				echo '<tr><td>'.$arr['user_nick'].'</td>
				<td><a href="?page='.$page.'&amp;mode=new&amp;message_user_to='.$arr['user_id'].'">Nachricht</a>
				<a href="?page=userinfo&amp;id='.$arr['user_id'].'">Profil</a>
				<a href="?page='.$page.'&amp;mode='.$mode.'&amp;remove='.$arr['user_id'].'">Entfernen</a>
				</td></tr>';
			}
			echo '</table>';
		}				
		else
		{
			echo '<i>Keine ignorierten Spieler vorhanden!</i>';
		}
		
		// Spieler bei denen man ignoriert ist
		$res=dbquery("SELECT
			user_id,
			user_nick
		FROM
			message_ignore			
		INNER JOIN
			users
			ON ignore_owner_id=user_id
			AND ignore_target_id=".$cu->id."
		ORDER BY
			user_nick");			
		if (mysql_num_rows($res)>0)
		{
			echo '<br/><br/>Du wirst von folgenden Spielern ignoriert:<br/><br/><table class="tb"><tr><th>Spieler</th><th>Aktionen</th></tr>';
			while ($arr=mysql_fetch_array($res))
			{
				echo '<tr><td>'.$arr['user_nick'].'</td>
				<td><a href="?page=userinfo&amp;id='.$arr['user_id'].'">Profil</a>
				<a href="?page='.$page.'&amp;mode='.$mode.'&amp;add='.$arr['user_id'].'">Ebenfalls ignorieren</a>
				</td></tr>';
			}
			echo '</table>';
		}	
?>