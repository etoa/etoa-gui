<?PHP
if (Alliance::checkActionRights('massmail'))
{
	echo "<h2>Rundmail</h2>";
	
	// Nachricht senden
	if (isset($_POST['submit']) && checker_verify())
	{
		$ures = dbquery("SELECT 
			user_id 
		FROM 
			users 
		WHERE 
			user_alliance_id=".$arr['alliance_id']." 
			AND user_id!=".$cu->id." 
		;");
		if (mysql_num_rows($ures)>0)
		{
			while ($uarr=mysql_fetch_array($ures))
			{
				$subject=addslashes($_POST['message_subject'])."";
				
				Message::sendFromUserToUser($cu->id,$uarr['user_id'],$_POST['message_subject'],$_POST['message_subject'],MSG_ALLYMAIL_CAT);
			}
			echo "Nachricht wurde gesendet!<br/><br/>";
			echo "<input type=\"button\" value=\"Neue Nachricht schreiben\" onclick=\"document.location='?page=$page&action=massmail'\" /> &nbsp; ";
			echo "<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
		}
		else
		{
			echo "<b>Fehler:</b> Nachricht wurde nicht gesendet, keine Mitglieder vorhanden!<br/><br/>";
			echo "<input type=\"button\" value=\"Zur&uuml;ck\" onClick=\"document.location='?page=$page'\" />";
		}
	}
	else
	{
		echo "<form action=\"?page=$page&action=massmail\" method=\"POST\">";
		checker_init();
		if(isset($_GET['message_subject']))
		{
			$subject = $_GET['message_subject'];
		}
		else
		{
			$subject = "";
		}
		tableStart("Nachricht verfassen");
		echo "<tr><td class=\"tbltitle\" style=\"width:50px;\">Betreff:</td><td class=\"tbldata\"><input type=\"text\" name=\"message_subject\" value=\"".$subject."\" size=\"30\" maxlength=\"255\"></td></tr>";
		echo "<tr><td class=\"tbltitle\">Text:</td><td class=\"tbldata\"><textarea name=\"message_text\" rows=\"5\" cols=\"50\"></textarea></td></tr>";
		tableEnd();
		echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" /> &nbsp;<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
		echo "</form>";
	}
}
?>