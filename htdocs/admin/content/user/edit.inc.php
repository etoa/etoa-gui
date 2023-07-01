<?php

use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\Race\RaceDataRepository;
use EtoA\Ranking\UserBannerService;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\ExternalUrl;
use EtoA\Support\StringUtils;
use EtoA\User\UserCommentRepository;
use EtoA\User\UserHolidayService;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSittingRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var TicketRepository $ticketRepo */
$ticketRepo = $app[TicketRepository::class];

/** @var AdminUserRepository $adminUserRepo */
$adminUserRepo = $app[AdminUserRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var UserService $userService */
$userService = $app[UserService::class];

/** @var \EtoA\Ship\ShipDataRepository $shipDateRepository */
$shipDateRepository = $app[\EtoA\Ship\ShipDataRepository::class];

/** @var UserLoginFailureRepository $userLoginFailureRepository */
$userLoginFailureRepository = $app[UserLoginFailureRepository::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserSittingRepository $userSittingRepository */
$userSittingRepository = $app[UserSittingRepository::class];

/** @var UserMultiRepository $userMultiRepository */
$userMultiRepository = $app[UserMultiRepository::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

/** @var NetworkNameService $networkNameService */
$networkNameService = $app[NetworkNameService::class];
/** @var UserHolidayService $userHolidayService */
$userHolidayService = $app[UserHolidayService::class];

/** @var UserBannerService $userBannerService */
$userBannerService = $app[UserBannerService::class];

$request = Request::createFromGlobals();

if (isset($_GET['id']))
    $id = $_GET['id'];
elseif (isset($_GET['user_id']))
    $id = $_GET['user_id'];
else
    $id = 0;

$user = $userRepository->getUser((int)$id);
$adminUserNicks = $adminUserRepo->findAllAsList();

// Geänderte Daten speichern
if (isset($_POST['save'])) {
    // TODO
}

// User löschen
if (isset($_POST['delete_user'])) {
// TODO
}

// Löschantrag speichern
if (isset($_POST['requestdelete'])) {
// TODO
}

// Löschantrag aufheben
if (isset($_POST['canceldelete'])) {
// TODO
}

// Fetch all data
$user = $userRepository->getUserAdminView($id);
if ($user !== null) {


    echo '<div id="tabs-3">';


    /**
     * Game-Daten
     */

    /** @var RaceDataRepository $raceRepository */
    $raceRepository = $app[RaceDataRepository::class];

    $raceNames = $raceRepository->getRaceNames();

    /** @var SpecialistDataRepository $specialistRepository */
    $specialistRepository = $app[SpecialistDataRepository::class];

    $specialistNames = $specialistRepository->getSpecialistNames();

    echo "<table class=\"tbl\">";
    echo "<tr>
                    <td class=\"tbltitle\">Rasse:</td>
                    <td class=\"tbldata\">
                        <select name=\"user_race_id\">
                        <option value=\"0\">(Keine)</option>";
    foreach ($raceNames as $raceId => $raceName) {
        echo "<option value=\"" . $raceId . "\"";
        if ($user->raceId === $raceId) echo " selected=\"selected\"";
        echo ">" . $raceName . "</option>\n";
    }
    echo "</select>
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" class=\"tbltitle\">Spezialist:</td>
                    <td class=\"tbldata\">
                        <select name=\"user_specialist_id\" id=\"user_specialist_id\" onchange=\"loadSpecialist(" . $st . ");\">
                        <option value=\"0\">(Keiner)</option>";
    foreach ($specialistNames as $specialistId => $specialistName) {
        echo '<option value="' . $specialistId . '"';
        if ($user->specialistId === $specialistId) {
            echo ' selected="selected"';
        }
        echo '>' . $specialistName . '</option>';
    }
    echo "</select> &nbsp; Bis:&nbsp; <span id=\"spt\">-</span>
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Allianz:</td>
                    <td class=\"tbldata\">
                        <select id=\"user_alliance_id\" name=\"user_alliance_id\" onchange=\"loadAllianceRanks(" . $user->allianceRankId . ");\">";
    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];
    $allianceNamesWithTags = $allianceRepository->getAllianceNamesWithTags();
    echo "<option value=\"0\">(Keine)</option>";
    foreach ($allianceNamesWithTags as $allianceId => $allianceNamesWithTag) {
        echo "<option value=\"$allianceId\"";
        if ($allianceId === $user->allianceId) echo " selected=\"selected\"";
        echo ">" . $allianceNamesWithTag . "</option>";
    }
    echo "</select> Rang: <span id=\"ars\">-</span></td>
                </tr>";
    echo "<tr>
            <td class=\"tbltitle\">Spionagesonden für Direktscan:</td>
            <td class=\"tbldata\">
            <input type=\"text\" name=\"spyship_count\" maxlength=\"5\" size=\"5\" value=\"" . $properties->spyShipCount . "\"> &nbsp; ";
    $shipNames = $shipDateRepository->getShipNamesWithAction('spy');
    if (count($shipNames) > 0) {
        echo '<select name="spyship_id"><option value="0">(keines)</option>';
        foreach ($shipNames as $shipId => $shipName) {
            echo '<option value="' . $shipId . '"';
            if ($properties->spyShipId == $shipId)
                echo ' selected="selected"';
            echo '>' . $shipName . '</option>';
        }
    } else {
        echo "Momentan steht kein Schiff zur Auswahl!";
    }
    echo "</td>
                </tr>";
    echo "<tr>
            <td class=\"tbltitle\">Analysatoren für Quickanalyse:</td>
            <td class=\"tbldata\">
            <input type=\"text\" name=\"analyzeship_count\" maxlength=\"5\" size=\"5\" value=\"" . $properties->analyzeShipCount . "\"> &nbsp; ";
    $shipNames = $shipDateRepository->getShipNamesWithAction('analyze');
    if (count($shipNames) > 0) {
        echo '<select name="analyzeship_id"><option value="0">(keines)</option>';
        foreach ($shipNames as $shipId => $shipName) {
            echo '<option value="' . $shipId . '"';
            if ($properties->analyzeShipId == $shipId)
                echo ' selected="selected"';
            echo '>' . $shipName . '</option>';
        }
    } else {
        echo "Momentan steht kein Schiff zur Auswahl!";
    }
    echo "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" valign=\"top\">Verfügbare Allianzschiffteile</td>
                    <td class=\"tbldata\">
                        <input type=\"text\" name=\"user_alliace_shippoints\" value=\"" . $user->allianceShipPoints . "\" size=\"10\" maxlength=\"10\" />
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" valign=\"top\">Verbaute Allianzschiffteile</td>
                    <td class=\"tbldata\">
                        <input type=\"text\" name=\"user_alliace_shippoints_used\" value=\"" . $user->allianceShipPointsUsed . "\" size=\"10\" maxlength=\"10\" />
                    </td>
                </tr>";

    // Multis & Sitting
    echo "<tr>
            <td class=\"tbltitle\" valign=\"top\">Gel&ouml;schte Multis</td>
            <td class=\"tbldata\">
                <input type=\"text\" name=\"user_multi_delets\" value=\"" . $user->multiDelets . "\" size=\"3\" maxlength=\"3\" />
            </td>
            </tr>
            <tr>
            <td class=\"tbltitle\" valign=\"top\">Sittertage</td>
            <td class=\"tbldata\">
                <input type=\"text\" name=\"user_sitting_days\" value=\"" . $user->sittingDays . "\" size=\"3\" maxlength=\"3\" />
            </td>
            </tr>";

    // Hauptplanet geändert
    echo "</td>
            </tr>
            <tr>
                <td class=\"tbltitle\" valign=\"top\">Hauptplanet geändert</td>
                <td class=\"tbldata\">
                    <label><input type=\"radio\" name=\"user_changed_main_planet\" value=\"1\" " . ($user->userChangedMainPlanet ? "checked" : "") . " />&nbsp;Ja</label>&nbsp;
                    <label><input type=\"radio\" name=\"user_changed_main_planet\" value=\"0\" " . (!$user->userChangedMainPlanet ? "checked" : "") . " />&nbsp;Nein</label>
                </td>
            </tr>";

    // Tabelle Ende

    echo "</table>";
    echo "
        <script type=\"text/javascript\">
        loadSpecialist(" . $st . ");loadAllianceRanks(" . $user->allianceRankId . ");
        </script>";

    echo '</div><div id="tabs-4">';

    /**
     * Sitting & Multi
     */

    $multiEntries = $userMultiRepository->getUserEntries($user->id, true);
    echo '<table class="tb">
            <tr>
                <th rowspan="' . (count($multiEntries) + 1) . '" valign="top">Eingetragene Multis</th>
                <th>Name</th>
                <th>Begründung</th>
                <th>Eingetragen</th>
                <th>Löschen</th>
            </tr>';
    foreach ($multiEntries as $multi) {
        echo '<tr>
                <td>
                    <a href="?page=user&sub=edit&user_id=' . $multi->multiUserId . '" name="multi_nick"".">' . $multi->multiUserNick . '</a>
                    <input type="hidden" name="multi_nick[' . $multi->multiUserId . ']" value="' . $multi->multiUserId . '" readonly="readonly">
                </td>
                <td>
                    ' . $multi->reason . '
                </td>
                <td>
                    ' . ($multi->timestamp > 0 ? StringUtils::formatDate($multi->timestamp) : '-') . '
                </td>
                <td>
                    <input type="checkbox" name="del_multi[' . $multi->multiUserId . ']" value="1">
                </td>
            </tr>';
    }

    $deletedMultiEntries = $userMultiRepository->getUserEntries($user->id, false);
    echo '<tr>
                <th rowspan="' . (count($deletedMultiEntries) + 1) . '" valign="top">Gelöschte Multis</th>
                <th>Name</th>
                <th>Begründung</th>
                <th>Gelöscht</th>
                <th></th>
            </tr>';
    foreach ($deletedMultiEntries as $multi) {
        echo '<tr>
                <td>
                    <a href="?page=user&sub=edit&user_id=' . $multi->multiUserId . '">' . $multi->multiUserNick . '</a>
                </td>
                <td>
                    ' . $multi->reason . '
                </td>
                <td>
                    ' . ($multi->timestamp > 0 ? StringUtils::formatDate($multi->timestamp) : '-') . '
                </td>
            </tr>';
    }
    echo '</table>';

    $sittedEntries = $userSittingRepository->getWhereUser($user->id);
    $sittingEntries = $userSittingRepository->getWhereSitter($user->id);
    echo '<table class="tb">
            <tr>
                <th rowspan="' . (count($sittedEntries) + 1) . '" valign="top">Wurde gesittet</th>
                <th>Sitter</th>
                <th>Start</th>
                <th>Ende</th>
                <th>Abbrechen</th>
            </tr>';
    $used_days = 0;
    foreach ($sittedEntries as $sittedEntry) {
        $used_days += (($sittedEntry->dateTo - $sittedEntry->dateFrom) / 86400);

        $time = time();
        echo '<tr>
                <td>
                    <a href="?page=user&sub=edit&user_id=' . $sittedEntry->sitterId . '">' . $sittedEntry->sitterNick . '</a>
                </td>
                <td>
                    ' . StringUtils::formatDate($sittedEntry->dateFrom) . '
                </td>
                <td>
                    ' . StringUtils::formatDate($sittedEntry->dateTo) . '
                </td>';
        if ($sittedEntry->dateTo > $time) {
            echo '<td>
                            <input type="checkbox" name="del_sitting[' . $sittedEntry->id . ']" value="1">
                        </td>';
        } else {
            echo '<td/>';
        }
        echo '</tr>';
    }

    echo '<tr>
            <th rowspan="' . (count($sittingEntries) + 1) . '" valign="top">Hat gesittet</th>
            <th>Gesitteter User</th>
            <th>Start</th><br>
            <th>Ende</th>
        </tr>';
    foreach ($sittingEntries as $sittingEntry) {
        echo '<tr>
                <td>
                    <a href="?page=user&sub=edit&user_id=' . $sittingEntry->userId . '">' . $sittingEntry->userNick . '</a>
                </td>
                <td>
                    ' . StringUtils::formatDate($sittingEntry->dateFrom) . '
                </td>
                <td>
                    ' . StringUtils::formatDate($sittingEntry->dateTo) . '
                </td>
            </tr>';
    }
    echo '</table>

    <h2>Multi einrichten</h2>

    <table class="tb">
        <tr>
            <td>User</td>
            <td>
                <input type="text" name="new_multi" maxlength="20" size="20"  autocomplete="off" placeholder="Usernick"">
            </td>
        </tr>
        <tr>
            <td>Beziehung</td>
            <td>
                <input type="text" name="multi_reason" maxlength="20" size="20"">
            </td>
        </tr>
    </table>

    <h2>Sitting einrichten</h2>

    <table class="tb">
        <tr>
            <td>Übrige Tage</td>
            <td>
                ' . floor($user->sittingDays - $used_days) . '
            </td>
        </tr>
        <tr>
            <td>Sitter</td>
            <td>
                <input type="text" maxlength="20" size="20" name="sitter_nick">
            </td>
        </tr>
        <tr>
            <td>Passwort</td>
            <td>
                <input type="text" maxlength="20" size="20" name="sitter_password1">
            </td>
        </tr>
        <tr>
            <td>Passwort wiederholen</td>
            <td>
                <input type="text" maxlength="20" size="20" name="sitter_password2">
            </td>
        </tr>
        <tr>
            <td>Von</td>
            <td>';
    showDatepicker("sitting_time_from", time(), true);
    echo '</td>
        </tr>
        <tr>
            <td>Bis</td>
            <td>';
    showDatepicker("sitting_time_to", time(), true);
    echo '</td>
            </tr>
    </table>

    </div><div id="tabs-5">';

    /**
     * Profil
     */

    echo "<table class=\"tb\">";
    echo "<tr>
                    <th>Profil-Text:</th>
                    <td class=\"tbldata\">
                        <textarea name=\"user_profile_text\" cols=\"60\" rows=\"8\">" . stripslashes($user->profileText) . "</textarea>
                    </td>
                </tr>
                <tr>
                    <th>Profil-Bild:</th>
                    <td class=\"tbldata\">";
    if ($user->profileImage != "") {
        if ($user->profileImageCheck)
            echo "<input type=\"checkbox\" value=\"0\" name=\"user_profile_img_check\"> Bild-Verifikation bestätigen<br/>";
        echo '<img src="' . $user->getProfileImageUrl() . '" alt="Profil" /><br/>';
        echo "<input type=\"checkbox\" value=\"1\" name=\"profile_img_del\"> Bild l&ouml;schen<br/>";
    } else {
        echo "<i>Keines</i>";
    }
    echo "</td>
                </tr>
                <tr>
                    <th>Board-Signatur:</th>
                    <td class=\"tbldata\">
                        <textarea name=\"user_signature\" cols=\"60\" rows=\"8\">" . $user->signature . "</textarea>
                    </td>
                </tr>
                <tr>
                    <th>Avatarpfad:</th>
                    <td class=\"tbldata\">";
    if ($user->avatar != "") {
        echo '<img src="' . $user->getAvatarUrl() . '" alt="Profil" /><br/>';
        echo "<input type=\"checkbox\" value=\"1\" name=\"avatar_img_del\"> Bild l&ouml;schen<br/>";
    } else {
        echo "<i>Keines</i>";
    }
    echo "</td>
                </tr>
                <tr>
            <th>Öffentliches Foren-Profil:</th>
            <td class=\"tbldata\">
                <input type=\"text\" name=\"user_profile_board_url\" maxlength=\"200\" size=\"50\" value=\"" . $user->profileBoardUrl . "\">
            </td>
            </tr>";

    echo '<tr><th>Banner:</th><td>';
    $name = $userBannerService->getUserBannerPath($id);
    if (file_exists($name)) {
        echo '
                <img src="' . $name . '" alt="Banner"><br>
                Generiert: ' . StringUtils::formatDate(filemtime($name)) . '<br/>
                <textarea readonly="readonly" rows="2" cols="65">&lt;a href="' . ExternalUrl::USERBANNER_LINK . '"&gt;&lt;img src="' . $config->get('roundurl') . '/' . $name . '" width="468" height="60" alt="EtoA Online-Game" border="0" /&gt;&lt;/a&gt;</textarea>
                <textarea readonly="readonly" rows="2" cols="65">[url=' . ExternalUrl::USERBANNER_LINK . '][img]' . $config->get('roundurl') . '/' . $name . '[/img][/url]</textarea>';
    }
    echo '</td></tr>';
    echo "</table>";

    echo '</div><div id="tabs-6">';

    /**
     * Design
     */

    $designs = get_designs();

    echo "<table class=\"tbl\" style=\"width:1000px\">";
    echo "<tr>
                    <td class=\"tbltitle\">Design:</td>
                    <td class=\"tbldata\">
                        <input type=\"text\" name=\"css_style\" id=\"css_style\" size=\"45\" maxlength=\"250\" value=\"" . $properties->cssStyle . "\">
                        &nbsp; <input type=\"button\" onclick=\"document.getElementById('css_style').value = document.getElementById('designSelector').options[document.getElementById('designSelector').selectedIndex].value\" value=\"&lt;&lt;\" /> &nbsp; ";
    echo "<select id=\"designSelector\">
                <option value=\"\">(Bitte wählen)</option>";
    foreach ($designs as $k => $v) {
        echo "<option value=\"$k\"";
        if ($properties->cssStyle == $k) echo " selected=\"selected\"";
        echo ">" . $v['name'] . "</option>";
    }
    echo "</select>
        </td>
    </tr>
    <tr>
    <td class=\"tbltitle\">Planetkreisgr&ouml;sse:</td>
    <td class=\"tbldata\" width=\"64%\" colspan=\"4\">
        <select name=\"planet_circle_width\">";
    for ($x = 450; $x <= 700; $x += 50) {
        echo "<option value=\"$x\"";
        if ($properties->planetCircleWidth == $x) echo " selected=\"selected\"";
        echo ">" . $x . "</option>";
    }
    echo "</select>
    </td>
    </tr>
    <tr>
        <td class=\"tbltitle\">Schiff/Def Ansicht:</td>
        <td class=\"tbldata\">
            <input type=\"radio\" name=\"item_show\" value=\"full\"";
    if ($properties->itemShow == 'full' || $properties->itemShow == '') echo " checked=\"checked\"";
    echo " /> Volle Ansicht  &nbsp;
            <input type=\"radio\" name=\"item_show\" value=\"small\"";
    if ($properties->itemShow == 'small') echo " checked=\"checked\"";
    echo " /> Einfache Ansicht
        </td>
    </tr>
    <tr>
        <td class=\"tbltitle\">Bildfilter:</td>
        <td class=\"tbldata\">
            <input type=\"radio\" name=\"image_filter\" value=\"1\"";
    if ($properties->imageFilter) echo " checked=\"checked\"";
    echo "/> An   &nbsp;
            <input type=\"radio\" name=\"image_filter\" value=\"0\"";
    if (!$properties->imageFilter) echo " checked=\"checked\"";
    echo "/> Aus
        </td>
    </tr>
    <tr>
        <td class=\"tbltitle\">Vertausche Buttons in Hafen-Schiffauswahl:</td>
        <td class=\"tbldata\">
        <input type=\"radio\" name=\"havenships_buttons\" value=\"1\" ";
    if ($properties->havenShipsButtons) echo " checked=\"checked\"";
    echo "/> Aktiviert &nbsp;
        <input type=\"radio\" name=\"havenships_buttons\" value=\"0\" ";
    if (!$properties->havenShipsButtons) echo " checked=\"checked\"";
    echo "/> Deaktiviert
        </td>
    </tr>
    <tr>
        <td class=\"tbltitle\">Werbung anzeigen:</td>
        <td class=\"tbldata\">
        <input type=\"radio\" name=\"show_adds\" value=\"1\" ";
    if ($properties->showAdds) echo " checked=\"checked\"";
    echo "/> Aktiviert &nbsp;
        <input type=\"radio\" name=\"show_adds\" value=\"0\" ";
    if (!$properties->showAdds) echo " checked=\"checked\"";
    echo "/> Deaktiviert
        </td>
    </tr>";
    echo "</table>";

    echo '</div><div id="tabs-7">';

    /**
     * Messages
     */

    echo "<table class=\"tbl\">";
    echo "<tr>
                    <td class=\"tbltitle\">Nachrichten-Signatur:</td>
                    <td class=\"tbldata\">
                        <textarea name=\"msgsignature\" cols=\"60\" rows=\"8\">" . $properties->msgSignature . "</textarea>
                    </td>
                </tr>
                <tr>
                <td class=\"tbltitle\">Nachrichtenvorschau (Neue/Archiv):</td>
                    <td class=\"tbldata\">
            <input type=\"radio\" name=\"msg_preview\" value=\"1\" ";
    if ($properties->msgPreview) echo " checked=\"checked\"";
    echo "/> Aktiviert
            <input type=\"radio\" name=\"msg_preview\" value=\"0\" ";
    if (!$properties->msgPreview) echo " checked=\"checked\"";
    echo "/> Deaktiviert
            </td>
        </tr>
        <tr>
        <td class=\"tbltitle\">Nachrichtenvorschau (Erstellen):</td>
        <td class=\"tbldata\">
        <input type=\"radio\" name=\"msgcreation_preview\" value=\"1\" ";
    if ($properties->msgCreationPreview) echo " checked=\"checked\"";
    echo "/> Aktiviert
        <input type=\"radio\" name=\"msgcreation_preview\" value=\"0\" ";
    if (!$properties->msgCreationPreview) echo " checked=\"checked\"";
    echo "/> Deaktiviert
    </td>
    </tr>
    <tr>
    <td class=\"tbltitle\">Blinkendes Nachrichtensymbol:</td>
        <td class=\"tbldata\">
        <input type=\"radio\" name=\"msg_blink\" value=\"1\" ";
    if ($properties->msgBlink) echo " checked=\"checked\"";
    echo "/> Aktiviert
        <input type=\"radio\" name=\"msg_blink\" value=\"0\" ";
    if (!$properties->msgBlink) echo " checked=\"checked\"";
    echo "/> Deaktiviert
    </td>
    </tr>
    <tr>
    <td class=\"tbltitle\">Text bei Antwort/Weiterleiten kopieren:</td>
        <td class=\"tbldata\">
        <input type=\"radio\" name=\"msg_copy\" value=\"1\" ";
    if ($properties->msgCopy) echo " checked=\"checked\"";
    echo "/> Aktiviert
        <input type=\"radio\" name=\"msg_copy\" value=\"0\" ";
    if (!$properties->msgCopy) echo " checked=\"checked\"";
    echo "/> Deaktiviert
        </td>
    </tr>

                <tr>
            <td class=\"tbltitle\">Nachricht bei Transport-/Spionagerückkehr:</td>
            <td class=\"tbldata\">
            <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"1\" ";
    if ($properties->fleetRtnMsg) {
        echo " checked=\"checked\"";
    }
    echo "/> Aktiviert &nbsp;

            <input type=\"radio\" name=\"fleet_rtn_msg\" value=\"0\" ";
    if (!$properties->fleetRtnMsg) {
        echo " checked=\"checked\"";
    }
    echo "/> Deaktiviert
            </td>
        </tr>
    <tr>
        <td colspan=\"2\" class=\"tabSeparator\"></td>
    </tr>
    <tr>
        <td class=\"tbltitle\">Nachricht senden:</td>
            <td class=\"tbldata\">
                Titel: <input type=\"text\" id=\"urgendmsgsubject\" maxlength=\"200\" size=\"50\" />
                <input type=\"button\" onclick=\"xajax_sendUrgendMsg(" . $user->id . ",document.getElementById('urgendmsgsubject').value,document.getElementById('urgentmsg').value)\" value=\"Senden\" /><br/>
                        Text: <textarea id=\"urgentmsg\" cols=\"60\" rows=\"4\"></textarea>
            </td>
        </tr>";
    echo "</table><br/>";

    echo "<h2>Letzte 5 Nachrichten</h2>";
    echo "<input type=\"button\" onclick=\"showLoader('lastmsgbox');xajax_showLast5Messages(" . $user->id . ",'lastmsgbox');\" value=\"Neu laden\" /><br><br>";
    echo "<div id=\"lastmsgbox\">Lade...</div>";

    echo '</div><div id="tabs-8">';

    /**
     * Loginfailures
     */

    echo "<table class=\"tbl\">";
    $failures = $userLoginFailureRepository->getUserLoginFailures($user->id);
    if (count($failures) > 0) {
        echo "<tr>
                        <th class=\"tbltitle\">Zeit</th>
                        <th class=\"tbltitle\">IP-Adresse</th>
                        <th class=\"tbltitle\">Hostname</th>
                        <th class=\"tbltitle\">Client</th>
                    </tr>";
        foreach ($failures as $failure) {
            echo "<tr>
                                            <td class=\"tbldata\">" . StringUtils::formatDate($failure->time) . "</td>
                                            <td class=\"tbldata\">
                                                <a href=\"?page=$page&amp;sub=ipsearch&amp;ip=" . $failure->ip . "\">" . $failure->ip . "</a>
                                            </td>
                                            <td class=\"tbldata\">
                                                <a href=\"?page=$page&amp;sub=ipsearch&amp;host=" . $networkNameService->getHost($failure->ip) . "\">" . $networkNameService->getHost($failure->ip) . "</a>
                                            </td>
                                            <td class=\"tbldata\">" . $failure->client . "</td>
                                        </tr>";
        }
    } else {
        echo "<tr>
                        <td class=\"tbldata\">Keine fehlgeschlagenen Logins</td>
                    </tr>";
    }
    echo "</table>";

    echo '</div><div id="tabs-9">';

    /**
     * Points
     */

    tableStart("Bewertung");

    /** @var UserRatingRepository $userRatingRepository */
    $userRatingRepository = $app[UserRatingRepository::class];

    $ratingSearch = UserRatingSearch::create()->id($id);

    $battleRating = $userRatingRepository->getBattleRating($ratingSearch)[0] ?? null;
    if ($battleRating !== null) {
        echo "<tr>
                <td>Kampfpunkte</td>
                <td>" . $battleRating->rating . "</td>
            </tr>";
        echo "<tr>
                <td>Kämpfe gewonnen/verloren/total</td>
                <td>" . $battleRating->battlesWon . "/" . $battleRating->battlesLost . "/" . $battleRating->battlesFought . "</td>
            </tr>";
    }

    $tradeRating = $userRatingRepository->getTradeRating($ratingSearch)[0] ?? null;
    if ($tradeRating !== null) {
        echo "<tr>
                <td>Handelspunkte</td>
                <td>" . $tradeRating->rating . "</td>
            </tr>";
        echo "<tr>
                <td>Handel Einkauf/Verkauf</td>
                <td>" . $tradeRating->tradesBuy . "/" . $tradeRating->tradesSell . "</td>
            </tr>";
    }

    $diplomacyRating = $userRatingRepository->getDiplomacyRating($ratingSearch)[0] ?? null;
    if ($diplomacyRating !== null) {
        echo "<tr>
                <td>Diplomatiepunkte</td>
                <td>" . $diplomacyRating->rating . "</td>
            </tr>";
    }

    tableEnd();

    echo '</div><div id="tabs-10">';

    /**
     * Tickets
     */

    echo "<div id=\"ticketsBox\">";

    $tickets = $ticketRepo->findBy(['user_id' => $id]);
    if (count($tickets) > 0) {
        tableStart('Tickets', '100%');
        echo "<tr>
            <th>ID</th>
            <th>Status</th>
            <th>Kategorie</th>
            <th>Zugeteilter Admin</th>
            <th>Letzte Änderung</th>
        </tr>";
        foreach ($tickets as $ticket) {
            echo "<tr>
                <td><a href=\"?page=tickets&id=" . $ticket->id . "\">" . $ticket->getIdString() . "</a></td>
                <td>" . $ticket->getStatusName() . "</td>
                <td>" . $ticketRepo->getCategoryName($ticket->catId) . "</td>
                <td>" . ($ticket->adminId > 0 ? $adminUserRepo->getNick($ticket->adminId) : '-') . "</td>
                <td>" . StringUtils::formatDate($ticket->timestamp) . "</td>
            </tr>";
        }
        tableEnd();
    } else {
        echo '<p>Dieser User hat keine Tickets</p>';
    }

    echo "</div>";

    echo '</div><div id="tabs-11">';

    /**
     * Kommentare
     */

    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];

    echo "<div id=\"commentsBox\"><h2>Neuer Kommentar:</h2><textarea rows=\"4\" cols=\"70\" id=\"new_comment_text\"></textarea><br/><br/>";
    echo "<input type=\"button\" onclick=\"xajax_addUserComment('$id','commentsBox',document.getElementById('new_comment_text').value);\" value=\"Speichern\" />";
    echo "<h2>Gespeicherte Kommentare</h2><table class=\"tb\">";

    $comments = $userCommentRepository->getComments($id);
    if (count($comments) > 0) {
        echo "<tr>
            <th>Text</th>
            <th>Verfasst</th>
            <th>Aktionen</th>
        </tr>";
        foreach ($comments as $comment) {
            echo "<tr>
                <td class=\"tbldata\" >" . BBCodeUtils::toHTML($comment->text) . "</td>
                <td class=\"tbldata\" style=\"width:200px;\">" . StringUtils::formatDate($comment->timestamp) . " von " . $comment->adminNick . "</td>
                <td class=\"tbldata\" style=\"width:50px;\"><a href=\"javascript:;\" onclick=\"if (confirm('Wirklich löschen?')) {xajax_delUserComment('" . $id . "','commentsBox'," . $comment->id . ")}\">Löschen</a></td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine Kommentare</td></tr>";
    }
    echo "</table></div>";

    echo '</div><div id="tabs-12">';

    /**
     * Log
     */
    echo "<div id=\"logsBox\">
        <div style=\"text-align:center;\"></div>
    </div>";


    echo '</div><div id="tabs-13">';

    /**
     * Wirtschaft
     */

    echo "
    <div id=\"tabEconomy\">
    Das Laden aller Wirtschaftsdaten kann einige Sekunden dauern!<br/><br/>
    <input type=\"button\" value=\"Wirtschaftsdaten laden\" onclick=\"showLoader('tabEconomy');xajax_loadEconomy(" . $user->id . ",'tabEconomy');\" />
    </div>";

    echo '
    </div>
</div>';


    // Buttons
    echo "<p>";
    echo "<input type=\"submit\" name=\"save\" value=\"&Auml;nderungen &uuml;bernehmen\" class=\"positive\" /> &nbsp;";
    if ($user->deleted !== 0) {
        echo "<input type=\"submit\" name=\"canceldelete\" value=\"Löschantrag aufheben\" class=\"userDeletedColor\" /> &nbsp;";
    } else {
        echo "<input type=\"submit\" name=\"requestdelete\" value=\"Löschantrag erteilen\" class=\"userDeletedColor\" /> &nbsp;";
    }
    echo "<input type=\"submit\" name=\"delete_user\" value=\"User l&ouml;schen\" class=\"remove\" onclick=\"return confirm('Soll dieser User entg&uuml;ltig gel&ouml;scht werden?');\"></p>";

    echo "<hr/><p>";
    echo button("Planeten", "?page=galaxy&sq=" . searchQueryUrl("user_id:=:" . $user->id)) . " &nbsp;";
    echo button("Gebäude", "?page=buildings&sq=" . searchQueryUrl("user_nick:=:" . $user->nick)) . " &nbsp;";
    echo "<input type=\"button\" value=\"Forschungen\" onclick=\"document.location='?page=techs&action=search&query=" . searchQuery(array("user_id" => $user->id)) . "'\" /> &nbsp;";
    echo button("Schiffe", "?page=ships&sq=" . searchQueryUrl("user_nick:=:" . $user->nick)) . " &nbsp;";
    echo button("Verteidigung", "?page=def&sq=" . searchQueryUrl("user_nick:=:" . $user->nick)) . " &nbsp;";
    echo button("Raketen", "?page=missiles&sq=" . searchQueryUrl("user_nick:=:" . $user->nick)) . " &nbsp;";
    echo "<input type=\"button\" value=\"IP-Adressen &amp; Hosts\" onclick=\"document.location='?page=user&amp;sub=ipsearch&amp;user=" . $user->id . "'\" /></p>";


    echo "<hr/>";
    echo "<p><input type=\"button\" value=\"Spielerdaten neu laden\" onclick=\"document.location='?page=$page&sub=edit&amp;user_id=" . $user->id . "'\" /> &nbsp;";
    echo "<input type=\"button\" value=\"Zur&uuml;ck zu den Suchergebnissen\" onclick=\"document.location='?page=$page&action=search'\" /> &nbsp;";
    echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /></p>";


    echo "</form>";

    if ($user->blockedFrom === 0)
        echo "<script>$(function() { $('#ban_options').hide(); });</script>";
    if ($user->blockedFrom == 0)
        echo "<script>$(function() { $('#umod_options').hide(); });</script>";

    echo '<script>
        $(function() {
            $( "#user_edit_tabs" ).bind( "tabsshow", function(event, ui) {
                if (ui.index == 6) {
                    xajax_showLast5Messages(' . $id . ',"lastmsgbox");
                } else if (ui.index == 8) {
                    xajax_userPointsTable(' . $id . ',"pointsBox");
                } else if (ui.index == 9) {
                    xajax_userTickets(' . $id . ',"ticketsBox");
                } else if (ui.index == 10) {
                    xajax_userComments(' . $id . ',"commentsBox");
                } else if (ui.index == 11) {
                    xajax_userLogs(' . $id . ',"logsBox");
                }
            });
        });
    </script>';
}