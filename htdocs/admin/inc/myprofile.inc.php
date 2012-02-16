<?PHP

	//////////////////////////////////////////////////
	//		 	 ____    __           ______       			//
	//			/\  _`\ /\ \__       /\  _  \      			//
	//			\ \ \L\_\ \ ,_\   ___\ \ \L\ \     			//
	//			 \ \  _\L\ \ \/  / __`\ \  __ \    			//
	//			  \ \ \L\ \ \ \_/\ \L\ \ \ \/\ \   			//
	//	  		 \ \____/\ \__\ \____/\ \_\ \_\  			//
	//			    \/___/  \/__/\/___/  \/_/\/_/  	 		//
	//																					 		//
	//////////////////////////////////////////////////
	// The Andromeda-Project-Browsergame				 		//
	// Ein Massive-Multiplayer-Online-Spiel			 		//
	// Programmiert von Nicolas Perrenoud				 		//
	// www.nicu.ch | mail@nicu.ch								 		//
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	//////////////////////////////////////////////////	
	//
	// 	Dateiname: changepass.inc.php	
	// 	Topic: Passwort-Änderung 
	// 	Autor: Nicolas Perrenoud alias MrCage							
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 31.03.2006
	// 	Kommentar: 	
	//

	echo "<h1>Mein Profil</h1>";
	
	if (isset($_POST['submitpw']))
	{
		if ($cu->checkEqualPassword($_POST['user_password_old']))
		{
			if ($_POST['user_password']==$_POST['user_password2'] && $_POST['user_password_old']!=$_POST['user_password'])
			{
				if (strlen($_POST['user_password'])>=PASSWORD_MINLENGHT)
				{
					$cu->setPassword($_POST['user_password']);
					cms_ok_msg("Das Passwort wurde ge&auml;ndert!");
					add_log(8,$cu->id." ändert sein Passwort",time());
				}
				else
					cms_err_msg("Das Passwort ist zu kurz! Es muss mindestens ".PASSWORD_MINLENGHT." Zeichen lang sein!");			
			}
			else
				cms_err_msg("Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!");			
		}
		else
			cms_err_msg("Das alte Passwort stimmt nicht mit dem gespeicherten Wert &uuml;berein!");
	}

	if (isset($_POST['submitdata']))
	{
		$cu->name = $_POST['user_name'];
		$cu->email = $_POST['user_email'];
		$cu->boardUrl = $_POST['user_board_url'];
		$cu->userTheme = $_POST['user_theme'];
		$cu->ticketEmail = $_POST['ticketmail'];
		$cu->playerId = $_POST['player_id'];
		$cu->save();
		
		if ($cu->playerId != $_POST['player_id'])
		{
			dbquery("UPDATE
						users
					SET
						user_ghost='0'
					WHERE
						user_id='".$arr['player_id']."';");
			
			dbquery("UPDATE
						users
					SET
						user_ghost='1'
					WHERE
						user_id='".$_POST['player_id']."';");
		}
		
		cms_ok_msg("Die Daten wurden ge&auml;ndert!");
		add_log(8,$cu->nick." ändert seine Daten");
	}
	
	echo "<form action=\"?myprofile=1\" method=\"post\">";
	echo "&Auml;ndere hier deine Daten und klicke auf '&Uuml;bernehmen', um die Daten zu speichern:<br/><br/>";
	
	echo "<fieldset><legend>Daten</legend>";
	echo "<br/><table class=\"tbl\">";
	echo "<tr>
		<th class=\"tbltitle\">Realer Name:</th>
		<td class=\"tbldata\"><input type=\"text\" name=\"user_name\" size=\"40\" value=\"".$cu->name."\" /></td></tr>";
	echo "<tr>
		<th class=\"tbltitle\">E-Mail:</th>
		<td class=\"tbldata\"><input type=\"text\" name=\"user_email\" size=\"40\" value=\"".$cu->email."\" /></td></tr>";
	echo "<tr>
		<th class=\"tbltitle\">Forum-Profil:</th>
		<td class=\"tbldata\"><input type=\"text\" name=\"user_board_url\" size=\"80\" value=\"".$cu->boardUrl."\" /></td></tr>";
	echo "<tr>
		<th class=\"tbltitle\">Mail bei Ticket:</th>
		<td class=\"tbldata\">
			<input type=\"radio\" name=\"ticketmail\" value=\"1\" ".($cu->ticketEmail ? " checked=\"checked\"" : "")." /> Ja 
			<input type=\"radio\" name=\"ticketmail\" value=\"0\" ".(!$cu->ticketEmail ? " checked=\"checked\"" : "")." /> Nein
		</td></tr>";
	echo "<tr>
		<th class=\"tbltitle\">Spieler-Account:</th>
		<td class=\"tbldata\"><select name=\"player_id\">";
		echo "<option value=\"0\">(Keiner)</option>";
		$ures = dbquery("
		SELECT
			user_id,
			user_nick
		FROM
			users
		ORDER BY
			user_nick
		");
		if (mysql_num_rows($ures)>0)
		{
			while ($uarr = mysql_fetch_row($ures))
			{
					echo "<option value=\"".$uarr[0]."\"";
					if ($uarr[0] == $cu->playerId)
					{
						echo " selected=\"selected\"";
					}
					echo ">".$uarr[1]."</option>";
			}
		}
		echo "</select></td></tr>";		
	echo "<tr>
		<th class=\"tbltitle\">Design-Theme:</th>
		<td class=\"tbldata\"><select name=\"user_theme\">";
		echo "<option value=\"\">Bitte wählen...</option>";
		if ($d = opendir("themes"))
		{
			while ($f = readdir($d))
			{
				if (is_file("themes/$f") && substr($f,strlen($f)-4)==".css")
				{
					echo "<option value=\"".$f."\"";
					if ($f == $cu->userTheme)
					{
						echo " selected=\"selected\"";
					}
					echo ">".substr($f,0,strrpos($f,".css"))."</option>";
				}
			}
		}
		echo "</select></td></tr>";
	echo "</table><br/><br/><input type=\"submit\" name=\"submitdata\" value=\"&Uuml;bernehmen\" />";
	
	echo "</fieldset><br/>";
	echo "</form>";

	echo "<form action=\"?myprofile=1\" method=\"post\">";
	echo "<fieldset><legend>Passwort</legend>";
	echo "<br/><table class=\"tbl\">";
	echo "<tr><th class=\"tbltitle\">Altes Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password_old\" size=\"40\" autocomplete=\"off\" /></td></tr>";
	echo "<tr><th class=\"tbltitle\">Neues Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password\" size=\"40\"  autocomplete=\"off\" /></td></tr>";
	echo "<tr><th class=\"tbltitle\">Neues Passwort (wiederholen):</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password2\" size=\"40\"  autocomplete=\"off\" /></td></tr>";
	echo "</table><br/><br/><input type=\"submit\" name=\"submitpw\" value=\"&Uuml;bernehmen\" />";
	echo "</fieldset>";

	echo "</form>";
?>
