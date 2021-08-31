<?PHP

use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

if (isset($_GET['id']) && intval($_GET['id']) > 0)
    $id = intval($_GET['id']);
else
    $id = intval($_GET['info_id']);

$infoAlliance = $allianceRepository->getAlliance($id);
if ($infoAlliance !== null) {
    if ($cu->allianceId() !== $infoAlliance->id) {
        $allianceRepository->addVisit($infoAlliance->id, true);
    }

    tableStart($infoAlliance->nameWithTag);
    if ($infoAlliance->getImageUrl() !== null && is_file($infoAlliance->getImageUrl())) {
        $ims = getimagesize($infoAlliance->getImageUrl());
        echo "<tr>
                        <td colspan=\"3\" style=\"text-align:center;background:#000\">
                            <img src=\"" . $infoAlliance->getImageUrl() . "\" alt=\"Allianz-Logo\" style=\"width:" . $ims[0] . "px;height:" . $ims[1] . "\" />
                        </td>
                    </tr>";
    }
    if ($config->getBoolean('allow_wings') && $infoAlliance->motherId !== 0) {
        $motherAlliance = $allianceRepository->getAlliance($infoAlliance->motherId);
        echo "<tr>
                        <th colspan=\"2\" style=\"text-align:center;\">
                            Diese Allianz ist ein Wing von <b><a href=\"?page=$page&amp;action=info&amp;id=" . $motherAlliance->id . "\">" . $motherAlliance->nameWithTag . "</a></b>
                        </th>
                    </tr>";
    }
    if ($infoAlliance->text != "") {
        echo "<tr>
                        <td colspan=\"2\" style=\"text-align:center;\">
                            " . BBCodeUtils::toHTML($infoAlliance->text) . "
                        </td>
                    </tr>";
    }

    // Kriege
    $wars = $allianceDiplomacyRepository->getDiplomacies($infoAlliance->id, AllianceDiplomacyLevel::WAR);
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
            $opAlliance = $allianceRepository->getAlliance($diplomacy->otherAllianceId);
            echo "<tr>
                                    <td>
                                        <a href=\"?page=$page&amp;id=" . $opAlliance->id . "\">" . $opAlliance->nameWithTag . "</a>
                                    </td>
                                    <td>" . StringUtils::formatNumber($opAlliance->points) . " / " . StringUtils::formatNumber($opAlliance->averagePoints) . "</td>
                                    <td>" . StringUtils::formatDate($diplomacy->date, false) . " bis " . StringUtils::formatDate($diplomacy->date + WAR_DURATION, false) . "</td>
                                </tr>";
        }
        echo "</table>
                        </td>
                    </tr>";
    }


    // Friedensabkommen
    $peace = $allianceDiplomacyRepository->getDiplomacies($infoAlliance->id, AllianceDiplomacyLevel::PEACE);
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
            $opAlliance = $allianceRepository->getAlliance($diplomacy->otherAllianceId);
            echo "<tr>
                                    <td>
                                        <a href=\"?page=$page&amp;id=" . $opAlliance->id . "\">" . $opAlliance->nameWithTag . "</a>
                                    </td>
                                    <td>" . StringUtils::formatNumber($opAlliance->points) . " / " . StringUtils::formatNumber($opAlliance->averagePoints) . "</td>
                                    <td>" . StringUtils::formatDate($diplomacy->date, false) . " bis " . StringUtils::formatDate($diplomacy->date + PEACE_DURATION, false) . "</td>
                                </tr>";
        }
        echo "</table>
                        </td>
                    </tr>";
    }

    // Bündnisse
    $bnds = $allianceDiplomacyRepository->getDiplomacies($infoAlliance->id, AllianceDiplomacyLevel::BND_CONFIRMED);
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
            $opAlliance = $allianceRepository->getAlliance($diplomacy->otherAllianceId);
            echo "<tr>
                                    <td>" . stripslashes($diplomacy->name) . "</td>
                                    <td><a href=\"?page=$page&amp;id=" . $opAlliance->id . "\">" . $opAlliance->nameWithTag . "</a></td>
                                    <td>" . StringUtils::formatNumber($opAlliance->points) . " / " . StringUtils::formatNumber($opAlliance->averagePoints) . "</td>
                                    <td>" . StringUtils::formatDate($diplomacy->date) . "</td>
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
    echo $infoAlliance->memberCount;
    if ($infoAlliance->publicMemberList)
        echo " [<a href=\"javascript:;\" onclick=\"xajax_showAllianceMembers('" . intval($id) . "','members')\" >Anzeigen</a>]";
    echo "</td>
                </tr>";

    // Punkte
    echo "<tr>
                    <th>Punkte / Durchschnitt:</th>
                    <td>";
    echo StringUtils::formatNumber($infoAlliance->points) . " / " . StringUtils::formatNumber($infoAlliance->averagePoints) . "";
    echo "</td>
                </tr>";

    // Gründer
    $founderNick = $userRepository->getNick($infoAlliance->founderId);
    echo "<tr>
                    <th>Gr&uuml;nder:</th>
                    <td>
                        <a href=\"?page=userinfo&amp;id=" . $infoAlliance->founderId . "\">" . $founderNick . "</a>
                    </td>
                </tr>";

    // Gründung
    echo "<tr>
                    <th>Gründungsdatum:</th>
                    <td>
                        " . StringUtils::formatDate($infoAlliance->foundationTimestamp) . " (vor " . StringUtils::formatTimespan(time() - $infoAlliance->foundationTimestamp) . ")
                    </td>
                </tr>";

    // Url
    if ($infoAlliance->url != "") {
        echo "<tr>
                        <th>Website/Forum:</th>
                        <td><b>" . StringUtils::formatLink($infoAlliance->url) . "</b></td>
                    </tr>";
    }

    // Diverses
    echo "<tr>
                    <th>Akzeptiert Bewerbungen:</th>
                    <td>
                        " . ($infoAlliance->acceptApplications ? "Ja" : "Nein") . "
                    </td>
                </tr>";
    echo "<tr>
                    <th>Akzeptiert Bündnissanfragen:</th>
                    <td>
                        " . ($infoAlliance->acceptBnd ? "Ja" : "Nein") . "
                    </td>
                </tr>";

    echo "</table>";
} else {
    error_msg("Diese Allianz existiert nicht!");
}

echo "<br/><br/><input type=\"button\" onclick=\"history.back();;\" value=\"Zur&uuml;ck\" />";
