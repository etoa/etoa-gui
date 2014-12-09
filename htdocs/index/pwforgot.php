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
	// 	Dateiname: pwforgot.php
	// 	Topic: Passwort-Erneuerung
	// 	Autor: Nicolas Perrenoud alias MrCage
	// 	Erstellt: 01.12.2004
	// 	Bearbeitet von: Nicolas Perrenoud alias MrCage
	// 	Bearbeitet am: 07.03.2006
	// 	Kommentar:
	//

	echo '<h1>Passwort vergessen?</h1>';

	if (isset($_POST['submit_pwforgot']) && checker_verify())
	{
		if ($_POST['user_nick']!="" && !stristr($_POST['user_nick'],"'") && $_POST['user_email_fix']!="" && !stristr($_POST['user_email_fix'],"'"))
		{
			$res = dbquery("
			SELECT 
				user_id,
				user_registered 
			FROM 
				users 
			WHERE 
				LCASE(user_nick)='".strtolower($_POST['user_nick'])."' 
				AND user_email_fix='".$_POST['user_email_fix']."'
			;");
			if (mysql_num_rows($res)>0)
			{
				$arr = mysql_fetch_array($res);
	
				// Passwort generieren
				$pw = generatePasswort();
	
				// Email schreiben
				$email_text="Hallo ".$_POST['user_nick']."\n\nDu hast ein neues Passwort angefordert.\nHier sind die neuen Daten:\n\nUniversum: ".Config::getInstance()->roundname->v."\n\nNick: ".$_POST['user_nick']."\nPasswort: ".$pw."\n\nWeiterhin viel Spass...\nDas EtoA-Team";
				$mail = new Mail("Passwort-Anforderung",$email_text);
				$mail->send($_POST['user_email_fix']);
	
				// Passwort updaten
				dbquery("UPDATE 
					users 
				SET 
					user_password='".saltPasswort($pw)."' 
				WHERE 
					user_nick='".$_POST['user_nick']."' 
					AND user_email_fix='".$_POST['user_email_fix']."'
				;");
			
				// Log hinzufügen
				add_log(3,"Der Benutzer ".$_POST['user_nick']." hat ein neues Passwort per E-Mail angefordert!",time());
	
				echo "Deine Passwort-Anfrage war erfolgreich. Du solltest in einigen Minuten eine E-Mail mit dem neuen Passwort erhalten!<br/><br/>";
			}
			else
			{
				echo "Es wurde kein entsprechender Datensatz gefunden!<br/><br/><input type=\"button\" onclick=\"javascript:history.back();\" value=\"Zur&uuml;ck\" /> &nbsp;";
			}
		}
		else
		{
			echo "Du hast keinen Benutzernamen oder keine E-Mail-Adresse eingegeben oder ein unerlaubtes Zeichen verwendet! <br/><br/><input type=\"button\" onclick=\"javascript:history.back();\" value=\"Zur&uuml;ck\" /> &nbsp;";
		}
	}
	else
	{
		echo '<form action="?index=pwforgot" method="post"><div style="width:400px;margin:0px auto;">';
		$cstr = checker_init();
		echo "Hier kannst du ein neues Passwort per E-Mail anfordern, wenn du deines vergessen hast:<br/><br/>";
		tableStart();
		echo "<tr><th class=\"tbltitle\">Benutzername:</th><td class=\"tbldata\"><input type=\"text\" name=\"user_nick\" size=\"30\" maxlength=\"255\" /></td></tr>";
		echo "<tr><th class=\"tbltitle\">Deine E-Mail:</th><td class=\"tbldata\"><input type=\"text\" name=\"user_email_fix\" size=\"30\" maxlength=\"255\" /></td></tr>";
		tableEnd();
		echo "<input type=\"submit\" name=\"submit_pwforgot\" value=\"Passwort anfordern\" class=\"button\" /></div></form>";
	}
?>

