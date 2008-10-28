<?PHP
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
	infobox_start("Passwort &auml;ndern",1);
	echo "<tr><th class=\"tbldata\" width=\"35%\">Altes Passwort:</th><td class=\"tbldata\" width=\"65%\"><input type=\"password\" name=\"user_password\" maxlength=\"255\" size=\"20\"></td></tr>";
	echo "<tr><th class=\"tbldata\" width=\"35%\">Neues Passwort (mind. ".PASSWORD_MINLENGHT." Zeichen):</th><td class=\"tbldata\" width=\"65%\"><input type=\"password\" name=\"user_password1\" maxlength=\"255\" size=\"20\"></td></tr>";
	echo "<tr><th class=\"tbldata\" width=\"35%\">Neues Passwort wiederholen:</th><td class=\"tbldata\" width=\"65%\"><input type=\"password\" name=\"user_password2\" maxlength=\"255\" size=\"20\"></td></tr>";
	infobox_end(1);
	echo "Beachte dass Passw&ouml;rter eine L&auml;nge von mindestens ".PASSWORD_MINLENGHT." Zeichen haben m&uuml;ssen!<br/><br/>";
	echo "<input type=\"submit\" name=\"password_submit\" value=\"Passwort &auml;ndern\"></form><br/><br/>";

?>