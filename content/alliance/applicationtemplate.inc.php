<?PHP

if (Alliance::checkActionRights('applicationtemplate'))
{

	echo "<h2>Bewerbungsvorlage bearbeiten</h2>";
	echo "<form action=\"?page=$page\" method=\"post\">";
	checker_init();
	tableStart("Bewerbungsvorlage");
	echo "<tr><td class=\"tbltitle\">Text:</td>
	<td class=\"tbldata\"><textarea rows=\"15\" cols=\"60\" name=\"alliance_application_template\">".stripslashes($arr['alliance_application_template'])."</textarea></td></tr>";
	echo "<tr><td class=\"tbltitle\">Beispiel:</td><td class=\"tbldata\">";
	echo nl2br('Dein Name:
Dein Alter:
Dein Sektor:
Deine Rasse:
Deine Punktezahl:
Deine Erfahrung:
Was du von uns erwartest:
Was kannst du für uns tun:
Der Grund deiner Bewerbung:');
	echo "</td></tr>";
	tableEnd();
	echo "<input type=\"submit\" name=\"applicationtemplatesubmit\" value=\"Speichern\" /> &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
}
?>