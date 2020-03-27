<?PHP
$twig->addGlobal("title", "Admin-Management");

	if (isset($_GET['new']))
	{
		echo "<h2>Neu</h2>";

		echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
		echo "<table class=\"tb\" style=\"width:auto;\">";
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
			<td><input type=\"text\" name=\"user_nick\" value=\"\" autocomplete=\"off\" /></td>
		</tr>";
		echo "<tr>
			<th>Passwort (leerlassen generiert eins):</th>
			<td><input type=\"password\" name=\"user_password\" autocomplete=\"off\" /></td>
		</tr>";
		echo "<tr>
			<th>Rollen:</th>
			<td>";
			$rm = new AdminRoleManager();
			foreach ($rm->getRoles() as $k => $v) {
				echo '<input type="checkbox" name="roles[]" value="'.$k.'" id="role_'.$k.'"> <label for="role_'.$k.'">'.$v.'</label><br/>';
			}
			echo "</td>
		</tr>";
		echo "<tr>
			<th>Kontakt anzeigen:</th>
			<td>
				<input type=\"radio\" name=\"is_contact\" value=\"1\" ";
				echo " checked=\"checked\"";
				echo "/> Ja 
				<input type=\"radio\" name=\"is_contact\" value=\"0\" ";
				echo "/> Nein
			</td>
		</tr>";
		echo "</table><br/>
		<input type=\"submit\" name=\"new_submit\" value=\"Speichern\" /> &nbsp; 
		<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub'\" value=\"Abbrechen\" />";
		echo "</form>";
	}
	elseif (isset($_GET['edit']) && $_GET['edit']>0)
	{
		echo "<h2>Bearbeiten</h2>";
		$au = new AdminUser($_GET['edit']);
		if ($au->isValid())
		{
			echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">
			<input type=\"hidden\" name=\"user_id\" value=\"".$au->id."\" />";
			echo "<table class=\"tb\" style=\"width:auto;\">";
			echo "<tr>
				<th>Realer Name:</th>
				<td><input type=\"text\" name=\"user_name\" value=\"".$au->name."\" /></td>
			</tr>";
			echo "<tr>
				<th>E-Mail:</th>
				<td><input type=\"text\" name=\"user_email\" value=\"".$au->email."\" /></td>
			</tr>";
			echo "<tr>
				<th>Nickname:</th>
				<td><input type=\"text\" name=\"user_nick\" value=\"".$au->nick."\"  autocomplete=\"off\" /></td>
			</tr>";
			echo "<tr>
				<th>Neues Passwort:</th>
				<td><input type=\"password\" name=\"user_password\" autocomplete=\"off\" /></td>
			</tr>";
			if (!empty($au->tfaSecret)) {
				echo "<tr>
					<th>Zwei-Faktor-Authentifizierung:</th>
					<td><input type=\"checkbox\" name=\"tfa_remove\" id=\"tfa_remove\" value=\"1\" /> <label for=\"tfa_remove\">Zwei-Faktor-Authentifizierung deaktivieren</label></td>
				</tr>";
			}
			echo "<tr>
				<th>Rollen:</th>
				<td>";
				$rm = new AdminRoleManager();
				foreach ($rm->getRoles() as $k => $v) {
					echo '<input type="checkbox" name="roles[]" value="'.$k.'" id="role_'.$k.'"';
					if (in_array($k, $au->roles)) {
						echo ' checked="checked"';
					}
					echo '> <label for="role_'.$k.'">'.$v.'</label><br/>';
				}
				echo "</td>
			</tr>";
			echo "<tr>
				<th>Gesperrt:</th>
				<td>
					<input type=\"radio\" name=\"user_locked\" value=\"1\" ";
					if ($au->locked) {
						echo " checked=\"checked\"";
					}
					echo "/> Ja 
					<input type=\"radio\" name=\"user_locked\" value=\"0\" ";
					if (!$au->locked) {
						echo " checked=\"checked\"";
					}
					echo "/> Nein
				</td>
			</tr>";
			echo "<tr>
				<th>Kontakt anzeigen:</th>
				<td>
					<input type=\"radio\" name=\"is_contact\" value=\"1\" ";
					if ($au->isContact) {
						echo " checked=\"checked\"";
					}
					echo "/> Ja 
					<input type=\"radio\" name=\"is_contact\" value=\"0\" ";
					if (!$au->isContact) {
						echo " checked=\"checked\"";
					}
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
				$au = new AdminUser();
				$au->nick = $_POST['user_nick'];
				$au->name = $_POST['user_name'];
				$au->email = $_POST['user_email'];
				$au->roles = isset($_POST['roles']) ? $_POST['roles'] : array();
				$au->isContact = ($_POST['is_contact'] > 0);
				$au->save();
                $twig->addGlobal('successMessage', "Gespeichert!");
				add_log(8,"Der Administrator ".$cu->nick." erstellt einen neuen Administrator: ".$_POST['user_nick']."(".$au->id.").");

				if ($_POST['user_password']!="") {
					$pw = $_POST['user_password'];
				} else {
					$pw = generatePasswort();
					echo "Das Passwort ist: $pw<br/><br/>";
				}
				$au->setPassword($pw);
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
				$au = new AdminUser($_POST['user_id']);
				$pw='';
				if ($_POST['user_password']!="")
				{
					$au->setPassword($_POST['user_password']);
					add_log(8,"Der Administrator ".$cu->nick." ändert das Passwort des Administrators ".$_POST['user_nick']."(".$_POST['user_id'].").");
				}
				$au->nick = $_POST['user_nick'];
				$au->name = $_POST['user_name'];
				$au->email = $_POST['user_email'];
				if (isset($_POST['tfa_remove'])) {
					$au->tfaSecret = "";
					add_log(8,"Der Administrator ".$cu->nick." deaktiviert die Zwei-Faktor-Authentifizierung des Administrators ".$_POST['user_nick']."(".$_POST['user_id'].").");
				}
				$au->locked = ($_POST['user_locked'] > 0);
				$au->isContact = ($_POST['is_contact'] > 0);
				$au->roles = isset($_POST['roles']) ? $_POST['roles'] : array();
				$au->save();
                $twig->addGlobal('successMessage', "Gespeichert!");
				add_log(8,"Der Administrator ".$cu->nick." ändert die Daten des Administrators ".$_POST['user_nick']." (ID: ".$_POST['user_id'].").");
			}
			else
			{
				echo "Nick nicht angegeben!<br/><br/>";
			}
		}

		if (isset($_GET['del']) && $_GET['del']>0 && $_GET['del']!=$cu->id) {
			$au = new AdminUser($_GET['del']);
			if ($au->isValid() && $au->delete()) {
				add_log(8, "Der Administrator ".$cu->nick." löscht den Administrator ".$au->nick." (ID: ".$au->id.").");
				echo "Benutzer gel&ouml;scht!<br/><br/>";
			}
		}

		echo "<table class=\"tb\" style=\"width:auto;\">
		<tr>
			<th>Nick</th>
			<th>Name</th>
			<th>E-Mail</th>
			<th>Zwei-Faktor-Authentifizierung</th>
			<th>Rollen</th>
			<th>Gesperrt</th>
			<th></th>
		</tr>";
		foreach (AdminUser::getAll() as $arr) {
			echo "<tr>
				<td>".$arr->nick."</td>
				<td>".$arr->name."</td>
				<td><a href=\"mailto:".$arr->email."\">".$arr->email."</a></td>
				<td>".($arr->tfaSecret ? "Aktiv" : "Nicht aktiviert")."</td>
				<td>".$arr->getRolesStr()."</td>
				<td>".($arr->locked==1 ? "<span style=\"color:red\">Ja</span>" : "Nein")."</td>
				<td style=\"width:40px;\">".edit_button("?page=$page&amp;sub=$sub&amp;edit=".$arr->id."")." ";
				if ($arr->id != $cu->id) {
				 	echo del_button("?page=$page&amp;sub=$sub&amp;del=".$arr->id,"return confirm('Soll der Benutzer wirklich gelöscht werden?')");
				}
				echo "</td>
			</tr>";
		}
		echo "</table><br/> ";
		echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;sub=$sub&amp;new=1'\" value=\"Neuer Benutzer\" />";
	}
?>
