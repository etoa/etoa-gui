<?PHP

/** @var \EtoA\Alliance\Alliance $alliance */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

use EtoA\Alliance\AllianceImage;
use EtoA\Alliance\AllianceRights;
use EtoA\Support\StringUtils;

if ($userAlliancePermission->checkHasRights(AllianceRights::EDIT_DATA, $page)) {
    echo "<h2>Allianzdaten &auml;ndern</h2>";

    echo "<form action=\"?page=$page\" method=\"post\" enctype=\"multipart/form-data\">";
    checker_init();

    tableStart("Daten der Info-Seite");
    echo "<tr>
        <th>Allianz-Tag:</th>
        <td>
            <input type=\"text\" name=\"alliance_tag\" size=\"6\" maxlength=\"6\" value=\"" . stripslashes($alliance->tag) . "\" />
        </td>
    </tr>
    <tr>
        <th>Allianz-Name:</th>
        <td>
            <input type=\"text\" name=\"alliance_name\" size=\"25\" maxlength=\"25\" value=\"" . stripslashes($alliance->name) . "\" />
        </td>
    </tr>
    <tr>
        <th>Beschreibung:</th>
        <td>
            <textarea rows=\"25\" cols=\"70\" name=\"alliance_text\">" . stripslashes($alliance->text) . "</textarea>
            <br/>" . helpLink('textformat', 'Hilfe zur Formatierung') . "
        </td>
    </tr>
    <tr>
        <th>Website/Forum:</th>
        <td>
            <input type=\"text\" name=\"alliance_url\" size=\"40\" maxlength=\"255\" value=\"" . stripslashes($alliance->url) . "\" /> Inkl. http://www....
        </td>
    </tr>
    <tr>
        <th>Allianz-Bild:</th>
        <td>";
    if ($alliance->getImageUrl() !== null) {
        echo '<img src="' . $alliance->getImageUrl() . '" alt="Profil" /><br/>';
        echo "<input type=\"checkbox\" value=\"1\" name=\"alliance_img_del\"> Bild l&ouml;schen<br/>";
    }
    echo "Allianzbild heraufladen/&auml;ndern: <input type=\"file\" name=\"alliance_img_file\" /><br/><b>Regeln:</b> Max " . AllianceImage::IMAGE_MAX_WIDTH . "*" . AllianceImage::IMAGE_MAX_HEIGHT . " Pixel, Bilder grösser als " . AllianceImage::IMAGE_WIDTH . "*" . AllianceImage::IMAGE_HEIGHT . " werden automatisch verkleinert.<br/>Format: GIF, JPG oder PNG. Grösse: Max " . StringUtils::formatNumber(AllianceImage::IMAGE_MAX_SIZE) . " Byte</td>
    </tr>
    <tr>
        <th>Bewerbungen zulassen:</th>
        <td>
            <input type=\"radio\" name=\"alliance_accept_applications\" value=\"1\" " . ($alliance->acceptApplications ? " checked=\"checked\"" : "") . "/>
            <span " . tm("Bewerbungen zulassen", "Jeder User kann sich bei dieser Allianz bewerben.") . ">Ja</span>
            <input type=\"radio\" name=\"alliance_accept_applications\" value=\"0\" " . (!$alliance->acceptApplications ? " checked=\"checked\"" : "") . "/>
            <span " . tm("Bewerbungen zulassen", "Es können keine Bewerbungen an diese Allianz geschrieben werden.") . ">Nein</span>
        </td>
    </tr>
    <tr>
        <th>Bündnisanfragen zulassen:</th>
        <td>
            <input type=\"radio\" name=\"alliance_accept_bnd\" value=\"1\" " . ($alliance->acceptBnd ? " checked=\"checked\"" : "") . "/>
            <span " . tm("Bündnisanfragen zulassen", "Bündnisanfragen von jeder Allianz sind zugelassen.") . ">Ja</span>
            <input type=\"radio\" name=\"alliance_accept_bnd\" value=\"0\" " . (!$alliance->acceptBnd ? " checked=\"checked\"" : "") . "/>
            <span " . tm("Bewerbungen zulassen", "Es werden keine Bündnisanfragen angenommen.") . ">Nein</span>
        </td>
    </tr>
    <tr>
        <th>Öffentliche Mitgliederliste:</th>
        <td>
            <input type=\"radio\" name=\"alliance_public_memberlist\" value=\"1\" " . ($alliance->publicMemberList ? " checked=\"checked\"" : "") . " /> Ja
            <input type=\"radio\" name=\"alliance_public_memberlist\" value=\"0\" " . (!$alliance->publicMemberList ? " checked=\"checked\"" : "") . " /> Nein
            (In der Statistik und auf der Raumkarte ist der Allianzname immer sichtbar)
        </td>
    </tr>";


    tableEnd();

    echo "<input type=\"submit\" name=\"editsubmit\" value=\"Speichern\" /> &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
}
