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
		$res=dbquery("SELECT user_password FROM admin_users WHERE user_id='".$s['user_id']."';");
		$arr=mysql_fetch_array($res);

		if (pw_salt($_POST['user_password_old'],$s['user_id'])==$arr['user_password'])
		{
			if ($_POST['user_password']==$_POST['user_password2'] && $_POST['user_password_old']!=$_POST['user_password'])
			{
				if (strlen($_POST['user_password'])>=PASSWORD_MIN_LENGTH)
				{
					dbquery("
					UPDATE 
						admin_users 
					SET 
						user_password='".pw_salt($_POST['user_password'],$s['user_id'])."',
						user_force_pwchange=0
					WHERE 
						user_id='".$s['user_id']."';");
					cms_ok_msg("Das Passwort wurde ge&auml;ndert!");
					add_log(8,$s['user_nick']." ändert sein Passwort",time());
				}
				else
					cms_err_msg("Das Passwort ist zu kurz! Es muss mindestens ".PASSWORD_MIN_LENGTH." Zeichen lang sein!");			
			}
			else
				cms_err_msg("Die Kennwortwiederholung stimmt nicht oder das alte und das neue Passwort sind gleich!");			
		}
		else
			cms_err_msg("Das alte Passwort stimmt nicht mit dem gespeicherten Wert &uuml;berein!");
	}
	
	if (isset($_POST['submitdata']))
	{
		dbquery("
		UPDATE 
			admin_users 
		SET 
			user_name='".$_POST['user_name']."',
			user_email='".$_POST['user_email']."',
			user_board_url='".$_POST['user_board_url']."',
			user_theme='".$_POST['user_theme']."'
		WHERE 
			user_id='".$s['user_id']."';");
		cms_ok_msg("Die Daten wurden ge&auml;ndert!");
		add_log(8,$s['user_nick']." ändert seine Daten",time());
	}		
	
	echo "<form action=\"?myprofile=1\" method=\"post\">";
	echo "&Auml;ndere hier deine Daten und klicke auf '&Uuml;bernehmen', um die Daten zu speichern:<br/><br/>";
	
	$dres = dbquery("
	SELECT
		user_name,
		user_email,
		user_board_url,
		user_theme
	FROM
		admin_users
	WHERE
		user_id=".$s['user_id'].";");
	$darr = mysql_fetch_array($dres);
	echo "<fieldset><legend>Daten</legend>";
	echo "<br/><table class=\"tbl\">";
	echo "<tr>
		<th class=\"tbltitle\">Realer Name:</th>
		<td class=\"tbldata\"><input type=\"text\" name=\"user_name\" size=\"40\" value=\"".$darr['user_name']."\" /></td></tr>";
	echo "<tr>
		<th class=\"tbltitle\">E-Mail:</th>
		<td class=\"tbldata\"><input type=\"text\" name=\"user_email\" size=\"40\" value=\"".$darr['user_email']."\" /></td></tr>";
	echo "<tr>
		<th class=\"tbltitle\">Forum-Profil:</th>
		<td class=\"tbldata\"><input type=\"text\" name=\"user_board_url\" size=\"80\" value=\"".$darr['user_board_url']."\" /></td></tr>";
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
					if ($f == $darr['user_theme'])
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

	echo "<fieldset><legend>Passwort</legend>";
	echo "<br/><table class=\"tbl\">";
	echo "<tr><th class=\"tbltitle\">Altes Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password_old\" size=\"40\" /></td></tr>";
	echo "<tr><th class=\"tbltitle\">Neues Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password\" size=\"40\" /></td></tr>";
	echo "<tr><th class=\"tbltitle\">Neues Passwort (wiederholen):</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password2\" size=\"40\" /></td></tr>";
	echo "</table><br/><br/><input type=\"submit\" name=\"submitpw\" value=\"&Uuml;bernehmen\" />";
	echo "</fieldset>";

	echo "</form>";
?>
