<?PHP
	echo "<h1>Admin-Management</h1>";

	if (isset($_GET['new']))
	{
		echo "<h2>Neu</h2>";

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "<table class=\"tb\">";
		echo "<tr>
			<th>Realer Name:</th>
			<td><input type=\"text\" name=\"user_name\" value=\"\" /></td>
		</tr>";
		echo "<tr>
			<th>E-Mail:</th>
			<td><input type=\"text\" name=\"user_email\" value=\"\" /></td>
		</tr>";
		echo "<tr>
			<th>Nickname:</th>
			<td><input type=\"text\" name=\"user_nick\" value=\"\" /></td>
		</tr>";
		echo "<tr>
			<th>Passwort (leerlassen generiert eins):</th>
			<td><input type=\"password\" name=\"user_password\" /></td>
		</tr>";
		echo "<tr>
			<th>Gruppe:</th>
			<td><select name=\"user_admin_rank\">";
			$gres = dbquery("
			SELECT
				*
			FROM
				admin_groups
			ORDER BY
				group_name
			");
			while ($garr=mysql_fetch_array($gres))
			{
				echo "<option value=\"".$garr['group_id']."\"";
				echo ">".$garr['group_name']."</option>";
			}
			echo "</select></td>
		</tr>";
		echo "</table><br/>
		<input type=\"submit\" name=\"new_submit\" value=\"Speichern\" /> &nbsp; 
		<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Abbrechen\" />";
		echo "</form>";
	}	
	elseif ($_GET['edit']>0)
	{
		echo "<h2>Bearbeiten</h2>";
		$res = dbquery("
		SELECT
			*
		FROM
			admin_users
		WHERE
			user_id=".$_GET['edit']."
		");		
		if (mysql_num_rows($res)>0)
		{
			$arr = mysql_fetch_array($res);
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
			<input type=\"hidden\" name=\"user_id\" value=\"".$arr['user_id']."\" />";
			echo "<table class=\"tb\">";
			echo "<tr>
				<th>Realer Name:</th>
				<td><input type=\"text\" name=\"user_name\" value=\"".$arr['user_name']."\" /></td>
			</tr>";
			echo "<tr>
				<th>E-Mail:</th>
				<td><input type=\"text\" name=\"user_email\" value=\"".$arr['user_email']."\" /></td>
			</tr>";
			echo "<tr>
				<th>Nickname:</th>
				<td><input type=\"text\" name=\"user_nick\" value=\"".$arr['user_nick']."\" /></td>
			</tr>";
			echo "<tr>
				<th>Neues Passwort:</th>
				<td><input type=\"password\" name=\"user_password\" /></td>
			</tr>";
			echo "<tr>
				<th>Gruppe:</th>
				<td><select name=\"user_admin_rank\">";
				$gres = dbquery("
				SELECT
					*
				FROM
					admin_groups
				ORDER BY
					group_name
				");
				while ($garr=mysql_fetch_array($gres))
				{
					echo "<option value=\"".$garr['group_id']."\"";
					if ($garr['group_id']==$arr['user_admin_rank'])
					{
						echo " selected=\"selected\"";
					}
					echo ">".$garr['group_name']."</option>";
				}
				echo "</select></td>
			</tr>";
			echo "<tr>
				<th>Gesperrt:</th>
				<td>
					<input type=\"radio\" name=\"user_locked\" value=\"1\" ";
					if ($arr['user_locked']==1)
						echo " checked=\"checked\"";
					echo "/> Ja 
					<input type=\"radio\" name=\"user_locked\" value=\"0\" ";
					if ($arr['user_locked']==0)
						echo " checked=\"checked\"";
					echo "/> Nein
				</td>
			</tr>";			
			echo "</table><br/>
			<input type=\"submit\" name=\"edit_submit\" value=\"Speichern\" /> &nbsp; 
			<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Abbrechen\" />";
			echo "</form>";
		}
		else
		{
			echo "ID nicht vorhanden!";
		}
	}
	else
	{	
		echo "<h2>&Uuml;bersicht</h2>";
	
		if (isset($_POST['new_submit']))
		{
			if ($_POST['user_nick']!="")
			{
				dbquery("
				INSERT INTO
					admin_users
				(
					user_nick,
					user_name,
					user_email,
					user_admin_rank
				)
				VALUES
				(
					'".$_POST['user_nick']."',
					'".$_POST['user_name']."',
					'".$_POST['user_email']."',
					'".$_POST['user_admin_rank']."'
				)
				");
				$id = mysql_insert_id();
				echo "Gespeichert!<br/><br/>";
				add_log(8,"Der Administrator ".$s['user_nick']." erstellt einen neuen Administrator: ".$_POST['user_nick']." (ID: ".$id.").",time());					

				if ($_POST['user_password']!="")
				{
					$pw = $_POST['user_password'];
				}				
				else
				{
					$pw = mt_rand(1000000,9999999);
					echo "Das Passwort ist: $pw<br/><br/>";
				}
				dbquery("UPDATE admin_users SET user_password='".pw_salt($pw,$id)."' WHERE user_id=".$id.";");
			}
			else
			{
				echo "Nick nicht angegeben!<br/><br/>";
			}			
		}
		
		if (isset($_POST['edit_submit']))
		{
			if ($_POST['user_nick']!="")
			{
				$pw='';
				if ($_POST['user_password']!="")
				{
					$pw = ", user_password='".pw_salt($_POST['user_password'],$_POST['user_id'])."'";
					add_log(8,"Der Administrator ".$s['user_nick']." ändert das Passwort des Administrators ".$_POST['user_nick']." (ID: ".$_POST['user_id'].").",time());					
				}				
				dbquery("
				UPDATE
					admin_users
				SET
					user_nick='".$_POST['user_nick']."',
					user_name='".$_POST['user_name']."',
					user_email='".$_POST['user_email']."',
					user_admin_rank='".$_POST['user_admin_rank']."',
					user_locked=".$_POST['user_locked']."
					".$pw."
				WHERE
					user_id=".$_POST['user_id']."				
				");
				echo "Gespeichert!<br/><br/>";
				add_log(8,"Der Administrator ".$s['user_nick']." ändert die Daten des Administrators ".$_POST['user_nick']." (ID: ".$_POST['user_id'].").",time());					
			}
			else
			{
				echo "Nick nicht angegeben!<br/><br/>";
			}			
		}
		
		if ($_GET['del']>0 && $_GET['del']!=$s['user_id'])
		{
			$res = dbquery("
			SELECT
				user_nick
			FROM
				admin_users
			WHERE
				user_id=".$_GET['del']."
			");		
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
				dbquery("DELETE FROM admin_users WHERE user_id=".$_GET['del']."");
				add_log(8,"Der Administrator ".$s['user_nick']." löscht den Administrator ".$arr['user_nick']." (ID: ".$_GET['del'].").",time());					
				echo "Benutzer gel&ouml;scht!<br/><br/>";
			}
		}
		
		
		$res = dbquery("
		SELECT
			user_id,
			user_nick,
			user_name,
			group_name,
			user_email,
			user_locked
		FROM
			admin_users
		LEFT JOIN
			admin_groups
			ON user_admin_rank=group_id
		ORDER BY
			user_nick ASC
		");
		echo "<table class=\"tb\">
		<tr>
			<th>Nick</th>
			<th>Name</th>
			<th>E-Mail</th>
			<th>Gruppe</th>
			<th>Gesperrt</th>
		</tr>";
		while ($arr=mysql_fetch_Array($res))
		{
			echo "<tr>
				<td>".$arr['user_nick']."</td>
				<td>".$arr['user_name']."</td>
				<td><a href=\"mailto:".$arr['user_email']."\">".$arr['user_email']."</a></td>
				<td>".$arr['group_name']."</td>
				<td>".($arr['user_locked']==1 ? "<span style=\"color:red\">Ja</span>" : "Nein")."</td>
				<td style=\"width:40px;\">".edit_button("?page=$page&amp;sub=$sub&amp;edit=".$arr['user_id']."")." ";
				if ($arr['user_id']!=$s['user_id'])
				 	echo del_button("?page=$page&amp;sub=$sub&amp;del=".$arr['user_id'],"return confirm('Soll der Benutzer wirklich gelöscht werden?')");
				echo "</td>
			</tr>";
		}		
		echo "</table><br/> ";
		echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;new=1'\" value=\"Neuer Benutzer\" />";
	}
?>