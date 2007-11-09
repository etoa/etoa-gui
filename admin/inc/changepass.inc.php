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

	echo "<h1>Passwort &auml;ndern</h1>";
	
	if (isset($_POST['submit']))
	{
		$res=dbquery("SELECT user_password FROM ".$db_table['admin_users']." WHERE user_id='".$_SESSION[SESSION_NAME]['user_id']."';");
		$arr=mysql_fetch_array($res);

		if (md5($_POST['user_password_old'])==$arr['user_password'])
		{
			if ($_POST['user_password']==$_POST['user_password2'] && $_POST['user_password_old']!=$_POST['user_password'])
			{
				if (strlen($_POST['user_password'])>=PASSWORD_MIN_LENGTH)
				{
					dbquery("UPDATE ".$db_table['admin_users']." SET user_password='".md5($_POST['user_password'])."' WHERE user_id='".$_SESSION[SESSION_NAME]['user_id']."';");
					cms_ok_msg("Das Passwort wurde ge&auml;ndert!");
					add_log(8,$_SESSION[SESSION_NAME]['user_nick']." ändert sein Passwort",time());
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
	
	echo "<form action=\"?page=home&sub=change_pass\" method=\"post\">";
	echo "&Auml;ndere hier deine Daten und klicke auf '&Uuml;bernehmen', um die Daten zu speichern:<br/><br/>";
	echo "<table class=\"tbl\">";
	echo "<tr><th class=\"tbltitle\">Altes Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password_old\" size=\"40\" /></td></tr>";
	echo "<tr><th class=\"tbltitle\">Neues Passwort:</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password\" size=\"40\" /></td></tr>";
	echo "<tr><th class=\"tbltitle\">Neues Passwort (wiederholen):</th><td class=\"tbldata\"><input type=\"password\" name=\"user_password2\" size=\"40\" /></td></tr>";
	echo "</table><br/><br/><input type=\"submit\" name=\"submit\" value=\"&Uuml;bernehmen\" />";
	echo "</form>";
?>
