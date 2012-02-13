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
	// als Maturaarbeit '04 am Gymnasium Oberaargau	//
	// www.etoa.ch | mail@etoa.ch								 		//
	//////////////////////////////////////////////////
	//
	// $Author$
	// $Date$
	// $Rev$
	//

	iBoxStart("Logins");
	echo "Solltest du Probleme mit dem Passwort haben schreibe 
	ein ".ticketLink("Ticket",17)." oder ".popUp("kontaktiere","page=contact")." einen Admin.";
	iBoxEnd();
			
	// Änderungen speichern
	if (isset($_POST['password_submit']) && checker_verify())
	{
		$rtnMsg = "";
		if ($cu->setPassword($_POST['user_password'],$_POST['user_password1'],$_POST['user_password2'],$rtnMsg))
		{
			success_msg("Das Passwort wurde ge&auml;ndert!");
		}
		else
		{
			error_msg($rtnMsg);
		}
	}

	// Formular anzeigen
	$cstr = checker_init();
	echo "<form action=\"?page=$page&mode=password\" method=\"post\">";
	echo $cstr;
	tableStart("Passwort &auml;ndern");
	echo "<tr><th>Altes Passwort:</th><td><input type=\"password\" name=\"user_password\" maxlength=\"255\" size=\"20\"></td></tr>";
	echo "<tr><th>Neues Passwort (mind. ".PASSWORD_MINLENGHT." Zeichen):</th><td><input type=\"password\" name=\"user_password1\" maxlength=\"255\" size=\"20\"></td></tr>";
	echo "<tr><th>Neues Passwort wiederholen:</th><td><input type=\"password\" name=\"user_password2\" maxlength=\"255\" size=\"20\"></td></tr>";
	tableEnd();
	echo "Beachte dass Passw&ouml;rter eine L&auml;nge von mindestens ".PASSWORD_MINLENGHT." Zeichen haben m&uuml;ssen!<br/><br/>";
	echo "<input type=\"submit\" name=\"password_submit\" value=\"Passwort &auml;ndern\"></form><br/><br/>";

?>