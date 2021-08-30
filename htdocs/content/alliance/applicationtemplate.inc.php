<?PHP

use EtoA\Alliance\AllianceRights;

/** @var \EtoA\Alliance\Alliance $alliance */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

if ($userAlliancePermission->checkHasRights(AllianceRights::APPLICATION_TEMPLATE, $page)) {

    echo "<h2>Bewerbungsvorlage bearbeiten</h2>";
    echo "<form action=\"?page=$page\" method=\"post\">";
    checker_init();
    tableStart("Bewerbungsvorlage");
    echo "<tr><th>Text:</td>
    <td><textarea rows=\"15\" cols=\"60\" name=\"alliance_application_template\">" . stripslashes($alliance->applicationTemplate) . "</textarea><br/>" . helpLink('textformat', 'Hilfe zur Formatierung') . "</td></tr>";
    echo "<tr><th>Beispiel:</td><td>";
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
