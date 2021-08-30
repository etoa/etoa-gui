<?PHP

/** @var \EtoA\Alliance\AllianceWithMemberCount $alliance */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

use EtoA\Alliance\AllianceRights;

if ($userAlliancePermission->checkHasRights(AllianceRights::LIQUIDATE, $page)) {
    echo "<h2>Allianz aufl&ouml;sen</h2>";

    // Prüft, ob noch Mitglieder vorhanden sind (keine Bewerbungen!)
    if ($alliance->memberCount > 1) {
        error_msg("Allianz kann nicht aufgel&ouml;st werden, da sie noch Mitglieder hat. L&ouml;sche zuerst die Mitglieder!");
        echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
    } else {
        echo "<form action=\"?page=$page\" method=\"post\">";
        echo "<input name=\"id_control\" type=\"hidden\" value=\"" . $cu->allianceId . "\" />";
        checker_init();
        echo "Willst du die Allianz wirklich aufl&ouml;sen?<br/><br/>
            <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Nein\" />&nbsp;&nbsp;&nbsp;
            <input type=\"submit\" name=\"liquidatesubmit\" value=\"Ja\" />";
        echo "</form>";
    }
}
