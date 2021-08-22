<?PHP

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\StringUtils;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

if (isset($_GET['id']) && intval($_GET['id']) > 0)
    $id = intval($_GET['id']);
else
    $id = intval($_GET['info_id']);

$infoAlly = new Alliance($id);
if ($infoAlly->valid) {
    if ($cu->allianceId != $infoAlly->id)
        $infoAlly->visitsExt++;

    tableStart($infoAlly);
    if ($infoAlly->image != "" && is_file($infoAlly->imageUrl)) {
        $ims = getimagesize($infoAlly->imageUrl);
        echo "<tr>
                        <td colspan=\"3\" style=\"text-align:center;background:#000\">
                            <img src=\"" . $infoAlly->imageUrl . "\" alt=\"Allianz-Logo\" style=\"width:" . $ims[0] . "px;height:" . $ims[1] . "\" />
                        </td>
                    </tr>";
    }
    if ($config->getBoolean('allow_wings') && $infoAlly->motherId != 0) {
        echo "<tr>
                        <th colspan=\"2\" style=\"text-align:center;\">
                            Diese Allianz ist ein Wing von <b><a href=\"?page=$page&amp;action=info&amp;id=" . $infoAlly->motherId . "\">" . $infoAlly->mother . "</a></b>
                        </th>
                    </tr>";
    }
    if ($infoAlly->text != "") {
        echo "<tr>
                        <td colspan=\"2\" style=\"text-align:center;\">
                            " . text2html($infoAlly->text) . "
                        </td>
                    </tr>";
    }

    // Kriege
    $wars = $allianceDiplomacyRepository->getDiplomacies($infoAlly->id, AllianceDiplomacyLevel::WAR);
    if (count($wars) > 0) {

        echo "<tr>
                        <th>Kriege:</th>
                        <td>
                            <table class=\"tbl\">
                                <tr>
                                    <th>Allianz</th>
                                    <th>Punkte</th>
                                    <th>Zeitraum</th>
                                </tr>";
        foreach ($wars as $diplomacy) {
            $opAlly = new Alliance($diplomacy->otherAllianceId);
            echo "<tr>
                                    <td>
                                        <a href=\"?page=$page&amp;id=" . $opAlly->id . "\">" . $opAlly . "</a>
                                    </td>
                                    <td>" . nf($opAlly->points) . " / " . nf($opAlly->avgPoints) . "</td>
                                    <td>" . df($diplomacy->date, 0) . " bis " . df($diplomacy->date + WAR_DURATION, 0) . "</td>
                                </tr>";
        }
        echo "</table>
                        </td>
                    </tr>";
    }


    // Friedensabkommen
    $peace = $allianceDiplomacyRepository->getDiplomacies($infoAlly->id, AllianceDiplomacyLevel::PEACE);
    if (count($peace) > 0) {
        echo "<tr>
                        <th>Friedensabkommen:</th>
                        <td>
                            <table class=\"tbl\">
                                <tr>
                                    <th>Allianz</th>
                                    <th>Punkte</th>
                                    <th>Zeitraum</th>
                                </tr>";
        foreach ($peace as $diplomacy) {
            $opAlly = new Alliance($diplomacy->otherAllianceId);
            echo "<tr>
                                    <td>
                                        <a href=\"?page=$page&amp;id=" . $opAlly->id . "\">" . $opAlly . "</a>
                                    </td>
                                    <td>" . nf($opAlly->points) . " / " . nf($opAlly->avgPoints) . "</td>
                                    <td>" . df($diplomacy->date, 0) . " bis " . df($diplomacy->date + PEACE_DURATION, 0) . "</td>
                                </tr>";
        }
        echo "</table>
                        </td>
                    </tr>";
    }

    // Bündnisse
    $bnds = $allianceDiplomacyRepository->getDiplomacies($infoAlly->id, AllianceDiplomacyLevel::BND_CONFIRMED);
    if (count($bnds) > 0) {
        echo "<tr>
                        <th>Bündnisse:</th>
                        <td>
                            <table class=\"tbl\">
                                <tr>
                                    <th>Bündnisname</th>
                                    <th>Allianz</th>
                                    <th>Punkte</th>
                                    <th>Seit</th>
                                </tr>";

        foreach ($bnds as $diplomacy) {
            $opAlly = new Alliance($diplomacy->otherAllianceId);
            echo "<tr>
                                    <td>" . stripslashes($diplomacy->name) . "</td>
                                    <td><a href=\"?page=$page&amp;id=" . $opAlly->id . "\">" . $opAlly . "</a></td>
                                    <td>" . nf($opAlly->points) . " / " . nf($opAlly->avgPoints) . "</td>
                                    <td>" . df($diplomacy->date) . "</td>
                                </tr>";
        }
        echo "</table>
                        </td>
                    </tr>";
    }

    // Mitglieder
    echo "<tr>
                    <th style=\"width:250px;\">Mitglieder:</th>
                    <td id=\"members\">";
    echo $infoAlly->memberCount;
    if ($infoAlly->publicMemberList)
        echo " [<a href=\"javascript:;\" onclick=\"xajax_showAllianceMembers('" . intval($id) . "','members')\" >Anzeigen</a>]";
    echo "</td>
                </tr>";

    // Punkte
    echo "<tr>
                    <th>Punkte / Durchschnitt:</th>
                    <td>";
    echo nf($infoAlly->points) . " / " . nf($infoAlly->avgPoints) . "";
    echo "</td>
                </tr>";

    // Gründer
    echo "<tr>
                    <th>Gr&uuml;nder:</th>
                    <td>
                        <a href=\"?page=userinfo&amp;id=" . $infoAlly->founderId . "\">" . $infoAlly->founder . "</a>
                    </td>
                </tr>";

    // Gründung
    echo "<tr>
                    <th>Gründungsdatum:</th>
                    <td>
                        " . df($infoAlly->foundationDate) . " (vor " . tf(time() - $infoAlly->foundationDate) . ")
                    </td>
                </tr>";

    // Url
    if ($infoAlly->url != "") {
        echo "<tr>
                        <th>Website/Forum:</th>
                        <td><b>" . StringUtils::formatLink($infoAlly->url) . "</b></td>
                    </tr>";
    }

    // Diverses
    echo "<tr>
                    <th>Akzeptiert Bewerbungen:</th>
                    <td>
                        " . ($infoAlly->acceptApplications ? "Ja" : "Nein") . "
                    </td>
                </tr>";
    echo "<tr>
                    <th>Akzeptiert Bündnissanfragen:</th>
                    <td>
                        " . ($infoAlly->acceptPact ? "Ja" : "Nein") . "
                    </td>
                </tr>";

    echo "</table>";
} else {
    error_msg("Diese Allianz existiert nicht!");
}

echo "<br/><br/><input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";
