<?PHP
//
// Script zum Anzeigen/Verbergen von Texten
//

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceDiplomacySearch;
use EtoA\Alliance\AllianceNewsRepository;

/** @var AllianceNewsRepository $allianceNewsRepository */
$allianceNewsRepository = $app[AllianceNewsRepository::class];
/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];
?>
<script type="text/javascript">
    function toggleText(elemId, switchId) {
        if (document.getElementById(switchId).innerHTML == "Anzeigen") {
            document.getElementById(elemId).style.display = '';
            document.getElementById(switchId).innerHTML = "Verbergen";
        } else {
            document.getElementById(elemId).style.display = 'none';
            document.getElementById(switchId).innerHTML = "Anzeigen";
        }
    }
</script>
<?PHP

echo "<h1>Rathaus</h1>";
echo "Im Rathaus k&ouml;nnen Allianzen Nachrichten an ihre Mitglieder oder an
    andere Allianzen ver&ouml;ffentlichen; diese Nachrichten k&ouml;nnen &ouml;ffentlich
    gemacht oder nur f&uuml;r die Empf&auml;nger lesbar publiziert werden.
    Zum Verfassen einer Nachricht benutze die entsprechende Option auf der Allianzseite.<br><br><br>";

//
// Neuste Nachrichten
//
$publicNews = $allianceNewsRepository->getNewsEntries(0, 10);
if (count($publicNews) > 0) {
    tableStart("Die neusten 10 Nachrichten");
    echo "<tr>
                        <th style=\"width:50%;\">Titel</th>
                        <th style=\"width:20%;\">Datum</th>
                        <th style=\"width:20%;\">Absender</th>
                        <th style=\"width:10%;\">Text</th>
                </tr>";
    foreach ($publicNews as $news) {
        $id = "th" . $news->id;
        $sid = "sth" . $news->id;

        echo "<tr><td>" . text2html($news->title) . "</td>";
        echo "<td>" . df($news->date) . "</td>";
        if ($news->authorAllianceName != "" && $news->authorAllianceTag != "") {
            echo "<td " . tm($news->authorAllianceTag, text2html($news->authorAllianceName)) . ">
                                <a href=\"?page=alliance&amp;info_id=" . $news->authorAllianceId . "\">" . $news->authorAllianceTag . "</a>
                            </td>";
        } else {
            echo "<td>(gel&ouml;scht)</td>";
        }
        echo "<td>
            [<a href=\"javascript:;\" onclick=\"toggleText('" . $id . "','" . $sid . "');\" id=\"" . $sid . "\">Anzeigen</a>]
            </td></tr>";
        echo "<tr id=\"" . $id . "\" style=\"display:none;\">
                <td colspan=\"5\">" . text2html(stripslashes($news->text)) . "
                <br/><br/>-------------------------------------<br/>";
        if ($news->authorUserId > 0) {
            echo "Geschrieben von <b><a href=\"?page=userinfo&amp;id=" . $news->authorUserId . "\">" . $news->authorUserNick . "</a></b>";
        } else {
            echo "<i>Unbekannter Verfasser</i>";
        }
        echo "</td>
            </tr>";
    }
    tableEnd();
} else {
    iBoxStart("Die neuesten 10 Nachrichten");
    echo "Es sind momentan keine Nachrichten vorhanden!";
    iBoxEnd();
}


//
// Internal messages
//
$internalNews = $allianceNewsRepository->getNewsEntries($cu->allianceId());
if (count($internalNews) > 0) {
    tableStart("Allianzinterne Nachrichten");
    echo "<tr>
                        <th style=\"width:50%;\">Titel</th>
                        <th style=\"width:20%;\">Datum</th>
                        <th style=\"width:20%;\">Absender</th>
                        <th style=\"width:10%;\">Text</th>
                </tr>";
    foreach ($internalNews as $news) {
        $id = "th" . $news->id;
        $sid = "sth" . $news->id;

        echo "<tr><td>" . text2html($news->title) . "</td>";
        echo "<td>" . df($news->date) . "</td>";
        if ($news->authorAllianceName != "" && $news->authorAllianceTag != "") {
            echo "<td " . tm($news->authorAllianceTag, text2html($news->authorAllianceName)) . ">
                    <a href=\"?page=alliance&amp;info_id=" . $news->authorAllianceId . "\">" . $news->authorAllianceTag . "</a>

                </td>";
        } else {
            echo "<td>(gel&ouml;scht)</td>";
        }
        echo "<td>
            [<a href=\"javascript:;\" onclick=\"toggleText('" . $id . "','" . $sid . "');\" id=\"" . $sid . "\">Anzeigen</a>]
            </td></tr>";
        echo "<tr id=\"" . $id . "\" style=\"display:none;\">
                <td colspan=\"5\">" . text2html(stripslashes($news->text)) . "
                <br/><br/>-------------------------------------<br/>";
        if ($news->authorUserId > 0) {
            echo "Geschrieben von <b><a href=\"?page=userinfo&amp;id=" . $news->authorUserId . "\">" . $news->authorUserNick . "</a></b>";
        } else {
            echo "<i>Unbekannter Verfasser</i>";
        }
        echo "</td>
            </tr>";
    }
    tableEnd();
} else {
    iBoxStart("Allianzinterne Nachrichten");
    echo "Es sind momentan keine Nachrichten vorhanden!";
    iBoxEnd();
}


//
// Bündnisse
//
$bnds = $allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::BND_CONFIRMED), 15);
if (count($bnds) > 0) {
    tableStart("Neuste Bündnisse");
    echo "
        <tr>
            <th style=\"width:25%;\">Allianz 1</th>
            <th style=\"width:25%;\">Allianz 2</th>
            <th style=\"width:20%;\">Bündnisname</th>
            <th style=\"width:20%;\">Datum</th>
            <th style=\"width:10%;\">Erklärung</th>
        </tr>";
    foreach ($bnds as $diplomacy) {
        $id = "bnd" . $diplomacy->id;
        $sid = "sbnd" . $diplomacy->id;
        echo "<tr>
                <td><a href=\"?page=alliance&amp;info_id=" . $diplomacy->alliance1Id . "\" " . tm($diplomacy->alliance1Tag, text2html($diplomacy->alliance1Name)) . ">" . text2html($diplomacy->alliance1Name) . "</td>
                <td><a href=\"?page=alliance&amp;info_id=" . $diplomacy->alliance2Id . "\" " . tm($diplomacy->alliance2Tag, text2html($diplomacy->alliance2Name)) . ">" . text2html($diplomacy->alliance2Name) . "</td>
                <td>" . stripslashes($diplomacy->name) . "</td>
                <td>" . df($diplomacy->date) . "</td>
                <td>";
        if ($diplomacy->publicText != "") {
            echo "[<a href=\"javascript:;\" onclick=\"toggleText('" . $id . "','" . $sid . "');\" id=\"" . $sid . "\">Anzeigen</a>]";
        } else {
            echo "-";
        }
        echo "</td>
            </tr>";
        echo "<tr id=\"" . $id . "\" style=\"display:none;\">
                <td colspan=\"5\">" . text2html(stripslashes($diplomacy->publicText)) . "</td>
            </tr>";
    }
    tableEnd();
} else {
    iBoxStart("Neuste Bündnisse");
    echo "Es sind momentan keine Nachrichten vorhanden!";
    iBoxEnd();
}


//
// Kriege
//
$wars = $allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::WAR));
if (count($wars) > 0) {
    tableStart("Aktuelle Kriege (Dauer " . round(WAR_DURATION / 3600) . "h)");
    echo "<tr>
                        <th width=\"25%\">Allianz 1</th>
                        <th width=\"25%\">Allianz 2</th>
                        <th width=\"20%\">Start</th>
                        <th width=\"20%\">Ende</th>
                        <th width=\"10%\">Erklärung</th>
                    </tr>";
    foreach ($wars as $diplomacy) {
        $id = "war" . $diplomacy->id;
        $sid = "swar" . $diplomacy->id;
        echo "<tr>
                <td><a href=\"?page=alliance&amp;info_id=" . $diplomacy->alliance1Id . "\" " . tm($diplomacy->alliance1Tag, text2html($diplomacy->alliance1Name)) . ">" . text2html($diplomacy->alliance1Name) . "</td>
                <td><a href=\"?page=alliance&amp;info_id=" . $diplomacy->alliance2Id . "\" " . tm($diplomacy->alliance2Tag, text2html($diplomacy->alliance2Name)) . ">" . text2html($diplomacy->alliance2Name) . "</td>
                <td>" . df($diplomacy->date) . "</td>
                <td>" . df($diplomacy->date + WAR_DURATION) . "</td>
                <td>";
        if ($diplomacy->publicText != "") {
            echo "[<a href=\"javascript:;\" onclick=\"toggleText('" . $id . "','" . $sid . "');\" id=\"" . $sid . "\">Anzeigen</a>]";
        } else {
            echo "-";
        }
        echo "</td>
            </tr>";
        echo "<tr id=\"" . $id . "\" style=\"display:none;\">
                <td colspan=\"5\">" . text2html(stripslashes($diplomacy->publicText)) . "</td>
            </tr>";
    }
    tableEnd();
} else {
    iBoxStart("Aktuelle Kriege (Dauer " . round(WAR_DURATION / 3600) . "h)");
    echo "Es sind momentan keine Nachrichten vorhanden!";
    iBoxEnd();
}


//
// Friedensabkommen
//
$peace = $allianceDiplomacyRepository->search(AllianceDiplomacySearch::create()->level(AllianceDiplomacyLevel::PEACE));
if (count($peace) > 0) {
    tableStart("Aktuelle Friedensabkommen (Dauer " . round(PEACE_DURATION / 3600) . "h)");
    echo "<tr>
            <th width=\"30%\">Allianz 1</th>
            <th width=\"30%\">Allianz 2</th>
            <th width=\"20%\">Start</th>
            <th width=\"20%\">Ende</th>
        </tr>";
    foreach ($peace as $diplomacy) {
        echo "<tr>
                <td><a href=\"?page=alliance&amp;info_id=" . $diplomacy->alliance1Id . "\" " . tm($diplomacy->alliance1Tag, text2html($diplomacy->alliance1Name)) . ">" . text2html($diplomacy->alliance1Name) . "</td>
                <td><a href=\"?page=alliance&amp;info_id=" . $diplomacy->alliance2Id . "\" " . tm($diplomacy->alliance2Tag, text2html($diplomacy->alliance2Name)) . ">" . text2html($diplomacy->alliance2Name) . "</td>
                <td>" . df($diplomacy->date) . "</td>
                <td>" . df($diplomacy->date + PEACE_DURATION) . "</td>
            </tr>";
    }
    tableEnd();
} else {
    iBoxStart("Aktuelle Friedensabkommen (Dauer " . round(PEACE_DURATION / 3600) . "h)");
    echo "Es sind momentan keine Nachrichten vorhanden!";
    iBoxEnd();
}
