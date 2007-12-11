<?PHP
if (Alliance::checkActionRights('massmail'))
{
	echo "<h2>Rundmail</h2>";
	// Nachricht senden
	if ($_POST['submit']!="" && checker_verify())
	{
		$ures = dbquery("SELECT user_id FROM users WHERE user_alliance_id=".$s['user']['alliance_id']." AND user_id!=".$s['user']['id']." AND user_alliance_application='';");
		if (mysql_num_rows($ures)>0)
		{
			while ($uarr=mysql_fetch_array($ures))
			{
				$subject=addslashes($_POST['message_subject'])."";
				dbquery("INSERT INTO ".$db_table['messages']." (
				message_user_from,
				message_user_to,
				message_timestamp,
				message_cat_id,
				message_subject,
				message_text,
				message_massmail
				) VALUES (
				'".$s['user']['id']."',
				'".$uarr['user_id']."',
				".time().",
				".MSG_ALLYMAIL_CAT.",
				'".$subject."',
				'".addslashes($_POST['message_text'])."',
				1
				);");
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
		infobox_start("Nachricht verfassen",1);
		echo "<tr><td class=\"tbltitle\" style=\"width:50px;\">Betreff:</td><td class=\"tbldata\"><input type=\"text\" name=\"message_subject\" value=\"".$_GET['message_subject']."\" size=\"30\" maxlength=\"255\"></td></tr>";
		echo "<tr><td class=\"tbltitle\">Text:</td><td class=\"tbldata\"><textarea name=\"message_text\" rows=\"5\" cols=\"50\"></textarea></td></tr>";
		infobox_end(1);
		echo "<input type=\"submit\" name=\"submit\" value=\"Senden\" /> &nbsp;<input type=\"button\" value=\"Zur&uuml;ck\" onclick=\"document.location='?page=$page'\" />";
		echo "</form>";
	}
}
?>