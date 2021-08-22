<?PHP

use EtoA\Alliance\AllianceNewsRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\TownhallService;
use EtoA\Support\StringUtils;
use EtoA\User\UserRatingService;

if (Alliance::checkActionRights(AllianceRights::ALLIANCE_NEWS)) {
    /** @var AllianceNewsRepository $allianceNewsRepository */
    $allianceNewsRepository = $app[AllianceNewsRepository::class];

    echo "<h2>Allianznews</h2>";
    if ((isset($_POST['newssubmit']) || isset($_POST['newssubmitsend'])) && checker_verify()) {
        if (StringUtils::checkIllegalSigns($_POST['news_title']) != "") {
            error_msg("Ungültige Zeichen (" . StringUtils::checkIllegalSigns($_POST['news_title']) . ") im Newstitel!!");
            $_SESSION['alliance']['news']['news_title'] = $_POST['news_title'];
            $_SESSION['alliance']['news']['news_text'] = $_POST['news_text'];
            $_SESSION['alliance']['news']['alliance_id'] = $_POST['alliance_id'];
        } elseif (isset($_POST['newssubmitsend']) && isset($_POST['news_text']) && $_POST['news_text'] != "") {
            $_SESSION['alliance'] = array();

            $newsId = $allianceNewsRepository->add($cu->getId(), $cu->allianceId(), $_POST['news_title'], $_POST['news_text'], (int) $_POST['alliance_id']);

            success_msg("News wurde gesendet!");

            // Gebe nur Punkte falls Nachricht öffentlich oder an andere Allianz
            if ($cu->allianceId != $_POST['alliance_id']) {
                /** @var UserRatingService $userRatingService */
                $userRatingService = $app[UserRatingService::class];

                // 2nd param is only for logging, Log::add() escapes string properly
                $userRatingService->addDiplomacyRating(
                    $cu->id,
                    DIPLOMACY_POINTS_PER_NEWS,
                    "Rathausnews verfasst (ID:" . $newsId . ", " . $_POST['news_text'] . ")"
                );
            }


            /** @var TownhallService $townhallService */
            $townhallService = $app[TownhallService::class];

            // Update rss file
            $townhallService->genRss();

        } elseif (isset($_POST['news_title']) && isset($_POST['news_text']) && $_POST['news_title'] != "" && $_POST['news_text'] != "") {
            $_SESSION['alliance'] = array();
            $_SESSION['alliance']['news'] = array();
            $_SESSION['alliance']['news']['news_title'] = $_POST['news_title'];
            $_SESSION['alliance']['news']['news_text'] = $_POST['news_text'];
            $_SESSION['alliance']['news']['alliance_id'] = $_POST['alliance_id'];
            $_SESSION['alliance']['news']['preview'] = TRUE;
            iBoxStart("Vorschau - " . $_POST['news_title']);
            echo text2html($_POST['news_text']);
            iBoxEnd();
        } else {
            $_SESSION['alliance'] = array();
            $_SESSION['alliance']['news'] = array();
            $_SESSION['alliance']['news']['news_title'] = $_POST['news_title'];
            $_SESSION['alliance']['news']['news_text'] = $_POST['news_text'];
            $_SESSION['alliance']['news']['alliance_id'] = $_POST['alliance_id'];
            error_msg("Nicht alle Felder ausgefüllt!");
        }
    }

    echo "<form action=\"?page=$page&action=" . $_GET['action'] . "\" method=\"post\">";
    checker_init();
    if (isset($_GET['message_subject']) && $_GET['message_subject'] != "") {
        $_SESSION['alliance']['news']['news_title'] = $_GET['message_subject'];
    }

    tableStart("Neue Allianzenews");
    if (isset($_SESSION['alliance']['news']['alliance_id']) && $_SESSION['alliance']['news']['alliance_id'] != 0) {
        $aid = intval($_SESSION['alliance']['news']['alliance_id']);
    } else {
        $aid = $cu->allianceId;
    }

    if (isset($_SESSION['alliance']['news']['news_title'])) {
        $news_title = $_SESSION['alliance']['news']['news_title'];
    } else {
        $news_title = "";
    }

    if (isset($_SESSION['alliance']['news']['news_text'])) {
        $news_text = $_SESSION['alliance']['news']['news_text'];
    } else {
        $news_text = "";
    }
    if (isset($_SESSION['alliance']['news']['preview']) && $_SESSION['alliance']['news']['preview']) {
        $send = "<input type=\"submit\" name=\"newssubmitsend\" value=\"Senden\"> &nbsp; ";
    } else {
        $send = "";
    }

    $aid = (isset($_POST['alliance_id'])) ? intval($_POST['alliance_id']) : $cu->allianceId();

    echo "<tr><th colspan=\"3\">Sende diese Nachricht nur ab, wenn du dir bezüglich der Rathausreglen sicher bist! Eine Missachtung kann zur Sperrung des Accounts führen!</th></tr>";
    echo "<tr>
        <th width=\"170\">Betreff:</td>
        <td colspan=\"2\"><input type=\"text\" name=\"news_title\" value=\"" . StringUtils::encodeDBStringToPlaintext($news_title) . "\" size=\"62\" maxlength=\"255\"></td></tr>";
    echo '<tr>
        <th width="170">Text:</td>
        <td colspan="2"><textarea name="news_text" rows="18" cols="60">' . StringUtils::encodeDBStringForTextarea($news_text) . '</textarea>
        <br/>' . helpLink('textformat', 'Hilfe zur Formatierung') . '</td></tr>';
    echo "<tr>
        <th width=\"170\">Ziel:</td>
        <td colspan=2>
            <select name=\"alliance_id\">";


    $selected = '';
    if ($aid == 0) {
        $selected = 'selected="selected" ';
    }
    echo '<option ' . $selected . ' value="0" style="font-weight:bold;color:#0f0;">Öffentliches Rathaus</option>';

    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];
    $allianceNamesWithTags = $allianceRepository->getAllianceNamesWithTags();

    foreach ($allianceNamesWithTags as $allianceId => $allianceNamesWithTag) {
        $selected = ($aid == $allianceId) ? 'selected="selected" ' : "";

        echo '<option value="' . $allianceId . '" ' . $selected . '>' . $allianceNamesWithTag . "</option>";
    }
    echo "</select></td>
    </tr>";
    tableEnd();

    echo $send;

    echo "<input type=\"submit\" name=\"newssubmit\" value=\"Vorschau\">";
    echo " &nbsp; <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
    echo "</form>";
}
