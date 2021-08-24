<?php

use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Race\RaceDataRepository;
use EtoA\Ranking\UserBannerService;
use EtoA\Specialist\SpecialistDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserCommentRepository;
use EtoA\User\UserHolidayService;
use EtoA\User\UserLoginFailureRepository;
use EtoA\User\UserMultiRepository;
use EtoA\User\UserPropertiesRepository;
use EtoA\User\UserRatingRepository;
use EtoA\User\UserRatingSearch;
use EtoA\User\UserRepository;
use EtoA\User\UserSearch;
use EtoA\User\UserService;
use EtoA\User\UserSittingRepository;
use EtoA\User\UserWarningRepository;
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

$user = $userRepository->getUser((int) $id);
$adminUserNicks = $adminUserRepo->findAllAsList();

// Geänderte Daten speichern
if (isset($_POST['save'])) {
    $logUser = new User($id);
    if ($user->nick !== $_POST['user_nick']) {
        $userService->addToUserLog($id, "settings", "{nick} hat seinen Namen zu " . $_POST['user_nick'] . " geändert.", true);
    }

    // Speichert Usertdaten in der Tabelle "users"
    $user->name = $request->request->get('user_name');
    $user->npc = $request->request->getInt('npc');
    $user->nick = $request->request->get('user_nick');
    $user->email = $request->request->get('user_email');
    $user->passwordTemp = $request->request->get('user_password_temp');
    $user->emailFix = $request->request->get('user_email_fix');
    $user->dualName = $request->request->get('dual_name');
    $user->dualEmail = $request->request->get('dual_email');
    $user->raceId = $request->request->getInt('user_race_id');
    $user->allianceId = $request->request->getInt('user_alliance_id');
    $user->profileText = $request->request->get('user_profile_text');
    $user->signature = $request->request->get('user_signature');
    $user->multiDelets = $request->request->getInt('user_multi_delets');
    $user->sittingDays = $request->request->getInt('user_sitting_days');
    $user->chatAdmin = $request->request->getInt('user_chatadmin');
    $user->admin = $request->request->getInt('admin');
    $user->ghost = $request->request->getBoolean('user_ghost');
    $user->userChangedMainPlanet = $request->request->getBoolean('user_changed_main_planet');
    $user->profileBoardUrl = $request->request->get('user_profile_board_url');
    $user->allianceShipPoints = $request->request->getInt('user_alliace_shippoints');
    $user->allianceShipPointsUsed = $request->request->getInt('user_alliace_shippoints_used');

    if (isset($_POST['user_alliance_rank_id'])) {
        $user->allianceRankId = $request->request->getInt('user_alliance_rank_id');
    }
    if (isset($_POST['user_profile_img_check'])) {
        $user->profileImageCheck = false;
    }

    //new Multi
    if (($_POST['new_multi'] != "") && (($_POST['multi_reason'] != ""))) {
        $newMultiUserId = $userRepository->getUserIdByNick($_POST['new_multi']);
        if ($newMultiUserId === null) {
            error_msg("Dieser User exisitert nicht!");
        }
        //ist der eigene nick eingetragen
        elseif ($newMultiUserId == $_GET['id']) {
            error_msg("Man kann nicht den selben Nick im Sitting eintragen!");
        } else {
            $userMultiRepository->addOrUpdateEntry((int) $_GET['id'], $newMultiUserId, $_POST['multi_reason']);
            success_msg("Neuer User angelegt!");
        }
    }

    // Handle specialist decision
    if ($_POST['user_specialist_id'] > 0 && $_POST['user_specialist_time_h'] > 0) {
        $user->specialistTime = mktime($_POST['user_specialist_time_h'], $_POST['user_specialist_time_i'], 0, $_POST['user_specialist_time_m'], $_POST['user_specialist_time_d'], $_POST['user_specialist_time_y']);
        $user->specialistId = $request->request->getInt('user_specialist_id');
    } else {
        $user->specialistTime = 0;
        $user->specialistId = 0;
    }

    // Handle  image
    if (isset($_POST['profile_img_del']) && $_POST['profile_img_del'] == 1) {
        if (file_exists(PROFILE_IMG_DIR . "/" . $user->profileImage)) {
            unlink(PROFILE_IMG_DIR . "/" . $user->profileImage);
        }

        $user->profileImage = '';
    }

    // Handle avatar
    if (isset($_POST['avatar_img_del']) && $_POST['avatar_img_del'] == 1) {
        if (file_exists(BOARD_AVATAR_DIR . "/" . $user->avatar)) {
            unlink(BOARD_AVATAR_DIR . "/" . $user->avatar);
        }
        $user->avatar = '';
    }

    // Handle password
    if (isset($_POST['user_password']) && $_POST['user_password'] != "") {
        $user->password = saltPasswort($_POST['user_password']);
        echo "Das Passwort wurde ge&auml;ndert!<br>";
        /** @var LogRepository $logRepository */
        $logRepository = $app[LogRepository::class];
        $logRepository->add(LogFacility::ADMIN, LogSeverity::INFO, $cu->nick . " ändert das Passwort von " . $_POST['user_nick'] . "");
    }

    // Handle ban
    if ($_POST['ban_enable'] == 1) {
        $ban_from = parseDatePicker('user_blocked_from', $_POST);
        $ban_to = parseDatePicker('user_blocked_to', $_POST);

        $user->blockedFrom = $ban_from;
        $user->blockedTo = $ban_to;
        $user->banAdminId = $request->request->getInt('user_ban_admin_id');
        $user->banReason = $request->request->get('user_ban_reason');

        $adminUserNick = $adminUserNicks[$_POST['user_ban_admin_id']] ?? '';
        $userService->addToUserLog($id, "account", "{nick} wird von [b]" . date("d.m.Y H:i", $ban_from) . "[/b] bis [b]" . date("d.m.Y H:i", $ban_to) . "[/b] gesperrt.\n[b]Grund:[/b] " . addslashes($_POST['user_ban_reason']) . "\n[b]Verantwortlich: [/b] " . $adminUserNick, true);
    } else {
        $user->blockedFrom = 0;
        $user->blockedTo = 0;
        $user->banAdminId = 0;
        $user->banReason = '';
    }

    // Handle holiday mode
    if ($_POST['umod_enable'] == 1) {
        $userHolidayService->activateHolidayMode($logUser->getId(), true);
        $user->hmodFrom = parseDatePicker('user_hmode_from', $_POST);
        $user->hmodTo = parseDatePicker('user_hmode_to', $_POST);
    } else {
        $userHolidayService->deactivateHolidayMode($user, true);
        $user->hmodFrom = 0;
        $user->hmodTo = 0;
    }

    // Perform query
    $userRepository->save($user);

    //
    // Speichert Usereinstellungen
    //

    $properties = $userPropertiesRepository->getOrCreateProperties((int) $id);
    $properties->cssStyle = filled($_POST['css_style']) ? $_POST['css_style'] : null;
    $properties->planetCircleWidth = $_POST['planet_circle_width'];
    $properties->itemShow = $_POST['item_show'];
    $properties->imageFilter = $_POST['image_filter'] == 1;
    $properties->msgSignature = filled($_POST['msgsignature']) ? $_POST['msgsignature'] : null;
    $properties->msgCreationPreview = $_POST['msgcreation_preview'] == 1;
    $properties->msgPreview = $_POST['msg_preview'] == 1;
    $properties->helpBox = $_POST['helpbox'] == 1;
    $properties->noteBox = $_POST['notebox'] == 1;
    $properties->msgCopy = $_POST['msg_copy'] == 1;
    $properties->msgBlink = $_POST['msg_blink'] == 1;
    $properties->spyShipId = $_POST['spyship_id'];
    $properties->spyShipCount = $_POST['spyship_count'];
    $properties->analyzeShipId = $_POST['analyzeship_id'];
    $properties->analyzeShipCount = $_POST['analyzeship_count'];
    $properties->havenShipsButtons = $_POST['havenships_buttons'] == 1;
    $properties->showAdds = $_POST['show_adds'] == 1;
    $properties->fleetRtnMsg = $_POST['fleet_rtn_msg'] == 1;

    $userPropertiesRepository->storeProperties((int) $id, $properties);

    if (isset($_POST['del_multi'])) {
        //Multi löschen
        foreach ($_POST['del_multi'] as $m_id => $data) {
            $m_id = intval($m_id);

            if ($_POST['del_multi'][$m_id] == 1) {
                $userMultiRepository->deactivate((int) $_GET['id'], (int) $_POST['multi_nick'][$m_id]);
                $userRepository->increaseMultiDeletes((int) $_GET['id']);

                success_msg("Eintrag gelöscht!");
            }
        }
    }

    //Sitting löschen
    if (isset($_POST['del_sitting'])) {
        foreach ($_POST['del_sitting'] as $s_id => $data) {
            $s_id = intval($s_id);

            if ($_POST['del_sitting'][$s_id] == 1) {
                $userSittingRepository->cancelEntry($s_id);

                success_msg("Eintrag gelöscht!");
            }
        }
    }

    //new sitting
    if ($_POST['sitter_nick'] != "") {
        if ($_POST['sitter_password1'] == $_POST['sitter_password2'] && $_POST['sitter_password1'] != "") {
            $sitting_from = parseDatePicker('sitting_time_from', $_POST);
            $sitting_to = parseDatePicker('sitting_time_to', $_POST);
            $diff = ceil(($sitting_to - $sitting_from) / 86400);
            $pw = saltPasswort($_POST['sitter_password1']);
            $sitterId = $userRepository->getUserIdByNick($_POST['sitter_nick']);

            if ($diff > 0) {
                if ($sitterId !== null) {
                    if ($diff <= $_POST['user_sitting_days']) {
                        $userSittingRepository->addEntry((int) $_GET['id'], $sitterId, $pw, $sitting_from, $sitting_to);
                    } else {
                        error_msg("So viele Tage sind nicht mehr vorhanden!!");
                    }
                } else {
                    error_msg("Dieser Sitternick exisitert nicht!");
                }
            } else {
                error_msg("Enddatum muss größer als Startdatum sein!");
            }
        }
    }
    echo MessageBox::ok("", "&Auml;nderungen wurden &uuml;bernommen!", "submitresult");
}

// User löschen
if (isset($_POST['delete_user'])) {
    try {
        $userService->delete((int) $id, false, $cu->nick);
        success_msg("L&ouml;schung erfolgreich!");
    } catch (Exception $ex) {
        error_msg($ex->getMessage());
    }
}

// Löschantrag speichern
if (isset($_POST['requestdelete'])) {
    $t = time() + ($config->getInt('user_delete_days') * 3600 * 24);
    $userRepository->markDeleted($id, $t);
    success_msg("Löschantrag gespeichert!");
}

// Löschantrag aufheben
if (isset($_POST['canceldelete'])) {
    $userRepository->markDeleted($id, 0);
    success_msg("Löschantrag aufgehoben!");
}

if (isset($_GET['setverified'])) {
    $userRepository->setVerified(intval($id), true);
    success_msg("Account freigeschaltet!");
}

// Fetch all data
$user = $userRepository->getUserAdminView($id);
if ($user !== null) {
    // Load data
    $properties = $userPropertiesRepository->getOrCreateProperties($user->id);

    // Some preparations
    $st = $user->specialistTime > 0 ? $user->specialistTime : time();

    $browserParser = new \WhichBrowser\Parser($user->userAgent);
    $agent = $browserParser->toString();

    // Javascript
    echo "<script type=\"text/javascript\">

    function loadSpecialist(st)
    {
        var elem = document.getElementById('user_specialist_id');
        xajax_showTimeBox('spt','user_specialist_time',st,elem.options[elem.selectedIndex].value);
    }

    function loadAllianceRanks(val)
    {
        var elem = document.getElementById('user_alliance_id');
        xajax_allianceRankSelector('ars','user_alliance_rank_id',val,elem.options[elem.selectedIndex].value);
    }


    function toggleText(elemId,switchId)
    {
        if (document.getElementById(switchId).innerHTML=='Anzeigen')
        {
            document.getElementById(elemId).style.display='';
            document.getElementById(switchId).innerHTML='Verbergen';
        }
        else
        {
            document.getElementById(elemId).style.display='none';
            document.getElementById(switchId).innerHTML='Anzeigen';
        }
    }

    </script>";

    $twig->addGlobal('subtitle', "User bearbeiten: " . $user->nick);

    echo "<form action=\"?page=$page&amp;sub=edit&amp;id=" . $user->id . "\" method=\"post\">
    <input type=\"hidden\" id=\"tabactive\" name=\"tabactive\" value=\"\" />";

    echo '<div class="tabs" id="user_edit_tabs">
<ul>
<li><a href="#tabs-1">Info</a></li>
<li><a href="#tabs-2">Account</a></li>
<li><a href="#tabs-3">Daten</a></li>
<li><a href="#tabs-4">Sitting</a></li>
<li><a href="#tabs-5">Profil</a></li>
<li><a href="#tabs-6">Design</a></li>
<li><a href="#tabs-7">Nachrichten</a></li>
<li><a href="#tabs-8">Loginfehler</a></li>
<li><a href="#tabs-9">Punkte</a></li>
<li><a href="#tabs-10">Tickets</a></li>
<li><a href="#tabs-11">Kommentare</a></li>
<li><a href="#tabs-12">Log</a></li>
<li><a href="#tabs-13">Wirtschaft</a></li>
</ul>
<div id="tabs-1">';


    /**
     * Allgemeines
     */

    echo "<table class=\"tbl\">";
    echo "<tr>
                    <td class=\"tbltitle\" style=\"width:180px;\">ID:</td>
                    <td class=\"tbldata\">" . $user->ip . "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Registrierdatum:</td>
                    <td class=\"tbldata\">" . StringUtils::formatDate($user->registered) . "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Zulezt online:</td>";
    if ($user->timeAction !== null)
        echo "<td class=\"tbldata\" style=\"color:#0f0;\">online</td>";
    elseif ($user->timeLog > 0)
        echo "<td class=\"tbldata\">" . date("d.m.Y H:i", $user->timeLog) . "</td>";
    else
        echo "<td class=\"tbldata\">Noch nicht eingeloggt!</td>";
    echo        "</tr>
                <tr>
                    <td class=\"tbltitle\">IP/Host:</td>
                    <td class=\"tbldata\"><a href=\"?page=user&amp;sub=ipsearch&amp;ip=" . $user->ipAddr . "\">" . $user->ipAddr . "</a>,
                        <a href=\"?page=user&amp;sub=ipsearch&amp;host=" . $networkNameService->getHost($user->ipAddr) . "\">" . $networkNameService->getHost($user->ipAddr) . "</a></td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Agent:</td>
                    <td class=\"tbldata\">" . $agent . "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Punkte:</td>
                    <td class=\"tbldata\">
                        " . StringUtils::formatNumber($user->points) . "
                        [<a href=\"javascript:;\" onclick=\"toggleBox('pointGraph')\">Verlauf anzeigen</a>]
                        <div id=\"pointGraph\" style=\"display:none;\"><img src=\"../misc/stats.image.php?user=" . $user->id . "\" alt=\"Diagramm\" /></div>
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Rang:</td>
                    <td class=\"tbldata\">" . StringUtils::formatNumber($user->rank) . " (aktuell), " . StringUtils::formatNumber($user->rankHighest) . " (max)</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Rohstoffe von...</td>
                    <td class=\"tbldata\">
                        Raids: " . StringUtils::formatNumber($user->resFromRaid) . " t<br/>
                        Asteroiden: " . StringUtils::formatNumber($user->resFromAsteroid) . " t<br/>
                        Nebelfelder: " . StringUtils::formatNumber($user->resFromNebula) . " t<br/>
        Trümmerfelder: " . StringUtils::formatNumber($user->resFromTf) . " t
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Infos:</td>
                    <td class=\"tbldata\">";


    if ($user->observe != "") {
        echo "<div>Benutzer steht unter <b>Beobachtung</b>: " . $user->observe . " &nbsp; [<a href=\"?page=user&sub=observed&text=" . $user->id . "\">Ändern</a>]</div>";
    }
    if ($user->deleted !== 0) {
        echo "<div class=\"userDeletedColor\">Dieser Account ist zur Löschung am " . StringUtils::formatDate($user->deleted) . " vorgemerkt</div>";
    }
    if ($user->hmodFrom > 0) {
        echo "<div class=\"userHolidayColor\">Dieser Account ist im Urlaubsmodus seit " . StringUtils::formatDate($user->hmodFrom) . " bis mindestens " . StringUtils::formatDate($user->hmodTo) . "</div>";
    }
    if ($user->blockedFrom > 0 && $user->blockedTo > time()) {
        echo "<div class=\"userLockedColor\">Dieser Account ist im gesperrt von " . StringUtils::formatDate($user->blockedFrom) . " bis " . StringUtils::formatDate($user->blockedTo);
        if ($user->banReason != "") {
            echo ". Grund: " . stripslashes($user->banReason);
        }
        echo "</div>";
    }
    if ($user->admin != 0) {
        echo "<div class=\"adminColor\">Dies ist ein Admin-Account!</div>";
    }
    if ($user->ghost) {
        echo "<div class=\"userGhostColor\">Dies ist ein Geist-Account. Er wird nicht in der Statistik angezeigt!</div>";
    }
    if ($user->chatAdmin != 0) {
        echo "<div>Dieser User ist ein Chat-Admin.</div>";
    }
    if ($user->verificationKey != '') {
        echo "<div>Die E-Mail Adresse ist noch nicht bestätigt [<a href=\"?page=$page&sub=$sub&id=$id&setverified\">Freischalten</a>].</div>";
    }

    // Kommentare
    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];
    $commentData = $userCommentRepository->getCommentInformation($user->id);

    if ($commentData['count'] > 0) {
        echo "<div><b>" . $commentData['count'] . " Kommentare</b> vorhanden, neuster Kommentar von " . StringUtils::formatDate($commentData['latest']) . "
                            [<a href=\"javascript:;\" onclick=\"$('.tabs').tabs('select', 10);\">Zeigen</a>]
                            </div>";
    }

    // Tickets
    $newTickets = $ticketRepo->findBy([
        "user_id" => $user->id,
        "status" => "new",
    ]);
    $numberOfNewTickets = count($newTickets);
    $assignedTickets = $ticketRepo->findBy([
        "user_id" => $user->id,
        "status" => "assigned",
    ]);
    $numberOfAssignedTickets = count($assignedTickets);
    if ($numberOfNewTickets + $numberOfAssignedTickets > 0) {
        echo "<div><b>" . $numberOfNewTickets . " neue Tickets</b> und <b>" . $numberOfAssignedTickets . " zugewiesene Tickets</b> vorhanden
                            [<a href=\"javascript:;\" onclick=\"$('.tabs').tabs('select', 9);\">Zeigen</a>]
                            </div>";
    }

    // Verwarnungen
    /** @var UserWarningRepository $userWarningRepository */
    $userWarningRepository = $app[UserWarningRepository::class];
    $warning = $userWarningRepository->getCountAndLatestWarning($user->id);
    if ($warning['count'] > 0) {
        echo "<div><b>" . $warning['count'] . " Verwarnungen</b> vorhanden, neuste  von " . StringUtils::formatDate($warning['max']) . "
                            [<a href=\"?page=user&amp;sub=warnings&amp;user=" . $id . "\">Zeigen</a>]
                            </div>";
    }


    echo "</td>
                </tr>";

    echo "</table>";

    echo '</div><div id="tabs-2">';

    /**
     * Account
     */

    echo "<table class=\"tbl\">";
    echo "<tr>
                <td class=\"tbltitle\">Nick:</td>
                <td class=\"tbldata\">
                    <input type=\"text\" name=\"user_nick\" value=\"" . $user->nick . "\" size=\"35\" maxlength=\"250\" />
                </td>
                <td>Eine Nickänderung ist grundsätzlich nicht erlaubt</td>
            </tr>
            <tr>
                <td class=\"tbltitle\">E-Mail:</td>
                <td class=\"tbldata\">
                    <input type=\"text\" name=\"user_email\" value=\"" . $user->email . "\" size=\"35\" maxlength=\"250\" />
                </td>
                <td>Rundmails gehen an diese Adresse</td>
            </tr>
            <tr>
                <td class=\"tbltitle\">Name:</td>
                <td class=\"tbldata\">
                    <input type=\"text\" name=\"user_name\" value=\"" . $user->name . "\" size=\"35\" maxlength=\"250\" />
                </td>
                <td>Bei Accountübergabe anpassen</td>
            </tr>
            <tr>
                <td class=\"tbltitle\">E-Mail fix:</td>
                <td class=\"tbldata\">
                    <input type=\"text\" name=\"user_email_fix\" value=\"" . $user->emailFix . "\" size=\"35\" maxlength=\"250\" />
                </td>
                <td>Bei Accountübergabe anpassen</td>
            </tr>
        </tr>
                    <td class=\"tbltitle\">Name Dual:</td>
                    <td class=\"tbldata\">
                        <input type=\"text\" name=\"dual_name\" value=\"" . $user->dualName . "\" size=\"35\" maxlength=\"250\" />
                    </td>
                    <td>Bei Dualänderung anpassen</td>
                </tr>
        <tr>
                    <td class=\"tbltitle\">E-Mail Dual:</td>
                    <td class=\"tbldata\">
                        <input type=\"text\" name=\"dual_email\" value=\"" . $user->dualEmail . "\" size=\"35\" maxlength=\"250\" />
                    </td>
                    <td>Bei Dualänderung anpassen</td>
                </tr>
        <tr>
                    <td class=\"tbltitle\">Passwort:</td>
                    <td class=\"tbldata\">
                        <input type=\"text\" name=\"user_password\" value=\"\" size=\"35\" maxlength=\"250\" />
                    </td>
                    <td>Leerlassen um altes Passwort beizubehalten</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Temporäres Passwort:</td>
                    <td class=\"tbldata\">
                        <input type=\"text\" name=\"user_password_temp\" value=\"" . $user->passwordTemp . "\" size=\"30\" maxlength=\"30\" />
                    </td>
                    <td>Nur dieses wird verwendet, falls ausgefüllt</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Geist:</td>
                    <td class=\"tbldata\">
                        <input type=\"radio\" name=\"user_ghost\" id=\"user_ghost1\" value=\"1\"";
    if ($user->ghost) {
        echo " checked=\"checked\" ";
    }
    echo " /><label for=\"user_ghost1\">Ja</label>
                        <input type=\"radio\" name=\"user_ghost\" id=\"user_ghost0\" value=\"0\" ";
    if (!$user->ghost) {
        echo " checked=\"checked\" ";
    }
    echo "/><label for=\"user_ghost0\">Nein</label>
                    </td>
                    <td>Legt fest ob der Spieler in der Rangliste ausgeblendet wird</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Chat-Admin:</td>
                    <td class=\"tbldata\">
                        <input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin1\" value=\"1\"";
    if ($user->chatAdmin === 1)
        echo " checked=\"checked\" ";
    echo " /><label for=\"user_chatadmin1\">Ja</label>
                        <input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin0\"value=\"0\" ";
    if ($user->chatAdmin === 0)
        echo " checked=\"checked\" ";
    echo "/><label for=\"user_chatadmin0\">Nein</label><br />
                        <input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin2\"value=\"2\" ";
    if ($user->chatAdmin === 2)
        echo " checked=\"checked\" ";
    echo "/><label for=\"user_chatadmin2\">Leiter Team Community</label><br />
                        <input type=\"radio\" name=\"user_chatadmin\" id=\"user_chatadmin3\"value=\"3\" ";
    if ($user->chatAdmin === 3)
        echo " checked=\"checked\" ";
    echo "/><label for=\"user_chatadmin3\">Entwickler mit Adminrechten</label>
                        </td>
                    <td>Der Spieler hat Adminrechte im Chat und einen silbernen Stern für Chatadmin,
                        einen grünen Stern für Leiter Team Community bzw. einen cyanfarbenen Stern für
                        Entwickler mit Adminrechten (Entwickler mit Adminrechten funktioniert nur, wenn unten 'Admin' auf 'Ja' gestellt wird).</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">Admin:</td>
                    <td class=\"tbldata\">
                        <input type=\"radio\" name=\"admin\" id=\"admin1\" value=\"1\"";
    if ($user->admin === 1)
        echo " checked=\"checked\" ";
    echo " /><label for=\"admin1\">Ja</label>
                        <input type=\"radio\" name=\"admin\" id=\"admin0\" value=\"0\" ";
    if ($user->admin === 0)
        echo " checked=\"checked\" ";
    echo "/><label for=\"admin0\">Nein</label>
                        <input type=\"radio\" name=\"admin\" id=\"admin2\" value=\"2\" ";
    if ($user->admin === 2)
        echo " checked=\"checked\" ";
    echo "/><label for=\"admin2\">Entwickler ohne Adminrechte</label>
                    </td>
                    <td>Admin: Der Spieler wird in der Raumkarte als Game-Admin markiert.<br/>Entwickler: Der Spieler bekommt einen nutzlosen roten Stern im Chat, keine Markierung</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\">NPC:</td>
                    <td class=\"tbldata\">
                        <input type=\"radio\" name=\"npc\" id=\"npc1\" value=\"1\"";
    if ($user->npc === 1)
        echo " checked=\"checked\" ";
    echo " /><label for=\"npc1\">Ja</label>
                        <input type=\"radio\" name=\"npc\" id=\"npc0\" value=\"0\" ";
    if ($user->npc === 0)
        echo " checked=\"checked\" ";
    echo "/><label for=\"npc0\">Nein</label>
                    </td>
                    <td>Spieler wird als NPC markiert</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" valign=\"top\">Sperren</td>
                    <td class=\"tbldata\">
                        <input type=\"radio\" name=\"ban_enable\" id=\"ban_enable0\" value=\"0\" onclick=\"$('#ban_options').hide();\"";
    if ($user->blockedFrom === 0) {
        echo " checked=\"checked\"";
    }
    echo " /><label for=\"ban_enable0\">Nein</label>
                        <input type=\"radio\" name=\"ban_enable\" id=\"ban_enable1\" value=\"1\" onclick=\"$('#ban_options').show();\" ";
    if ($user->blockedFrom > 0) {
        echo " checked=\"checked\"";
    }
    echo " /><label for=\"ban_enable1\">Ja</label>";
    if ($user->blockedFrom > 0 && $user->blockedTo < time()) {
        echo " <i><b>Diese Sperre ist abgelaufen!</b></i>";
    }
    echo "<table id=\"ban_options\">
                            <tr>
                    <td class=\"tbltitle\" valign=\"top\">Von </td>
                    <td class=\"tbldata\">";
    showDatepicker("user_blocked_from", $user->blockedFrom > 0 ? $user->blockedFrom : time(), true);
    echo "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" valign=\"top\">Bis</td>
                    <td class=\"tbldata\">";
    $userBlockedDefaultTime = 3600 * 24 * $config->get('user_ban_min_length');
    showDatepicker("user_blocked_to", $user->blockedFrom > 0 ? $user->blockedTo : time() + $userBlockedDefaultTime, true);
    echo "</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" valign=\"top\">Admin</td>
                    <td class=\"tbldata\">
                        <select name=\"user_ban_admin_id\" id=\"user_ban_admin_id\">
                        <option value=\"0\">(niemand)</option>";
    foreach ($adminUserNicks as $adminUserId => $adminUserNick) {
        echo "<option value=\"" . $adminUserId . "\"";
        if ($user->banAdminId === $adminUserId) echo " selected=\"selected\"";
        echo ">" . $adminUserNick . "</option>\n";
    }
    echo "</select>
                    </td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" valign=\"top\">Grund</td>
                    <td class=\"tbldata\">
                        <textarea name=\"user_ban_reason\" id=\"user_ban_reason\" cols=\"45\" rows=\"2\">" . stripslashes($user->banReason) . "</textarea>
                    </td>
                </tr>
                </table>";

    echo "</td>
                    <td>Der Benutzer kann sich nicht einloggen und erscheint auf dem Pranger</td>
                </tr>
                <tr>
                    <td class=\"tbltitle\" valign=\"top\">U-Mod</td>
                    <td class=\"tbldata\">
                        <input type=\"radio\" name=\"umod_enable\" id=\"umod_enable0\" value=\"0\" onclick=\"$('#umod_options').hide();\" checked=\"checked\" /><label for=\"umod_enable0\">Nein</label>
                        <input type=\"radio\" name=\"umod_enable\" id=\"umod_enable1\" value=\"1\" onclick=\"$('#umod_options').show();\" ";
    if ($user->hmodFrom > 0) {
        echo " checked=\"checked\"";
    }
    echo "/><label for=\"umod_enable1\">Ja</label> ";
    if ($user->hmodFrom > 0 && $user->hmodTo < time()) {
        echo "<i><b>Dieser Urlaubsmodus ist abgelaufen!</b></i>";
    }
    echo "<table id=\"umod_options\">
                        <tr>
                            <td class=\"tbltitle\" valign=\"top\">Von</td>
                            <td class=\"tbldata\">";
    showDatepicker("user_hmode_from", $user->hmodFrom > 0 ? $user->hmodFrom : time(), true);
    echo "</td>
                        </tr>
                        <tr>
                            <td class=\"tbltitle\" valign=\"top\">Bis</td>
                            <td class=\"tbldata\">";
    $userHolidayModeDefaultTime = 3600 * 24 * $config->get('user_umod_min_length');
    showDatepicker("user_hmode_to", $user->hmodTo > 0 ? $user->hmodTo : time() + $userHolidayModeDefaultTime, true);
    echo "</td>
                        </tr>
                        </table>
                    </td>
                    <td>Der Benutzer kann nichts mehr bauen, wird aber auch nicht angegriffen</td>
                </tr>";


    echo "</table>";

    echo '</div><div id="tabs-3">';


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
        echo '<img src="' . PROFILE_IMG_DIR . '/' . $user->profileImage . '" alt="Profil" /><br/>';
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
        echo '<img src="' . BOARD_AVATAR_DIR . '/' . $user->avatar . '" alt="Profil" /><br/>';
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
                <textarea readonly="readonly" rows="2" cols="65">&lt;a href="' . USERBANNER_LINK_URL . '"&gt;&lt;img src="' . $config->get('roundurl') . '/' . $name . '" width="468" height="60" alt="EtoA Online-Game" border="0" /&gt;&lt;/a&gt;</textarea>
                <textarea readonly="readonly" rows="2" cols="65">[url=' . USERBANNER_LINK_URL . '][img]' . $config->get('roundurl') . '/' . $name . '[/img][/url]</textarea>';
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
        <td class=\"tbltitle\">Separates Hilfefenster:</td>
        <td class=\"tbldata\">
        <input type=\"radio\" name=\"helpbox\" value=\"1\" ";
    if ($properties->helpBox) echo " checked=\"checked\"";
    echo "/> Aktiviert &nbsp;
        <input type=\"radio\" name=\"helpbox\" value=\"0\" ";
    if (!$properties->helpBox) echo " checked=\"checked\"";
    echo "/> Deaktiviert
        </td>
    </tr>
    <tr>
        <td class=\"tbltitle\">Separater Notizbox:</td>
        <td class=\"tbldata\">
        <input type=\"radio\" name=\"notebox\" value=\"1\" ";
    if ($properties->noteBox) echo " checked=\"checked\"";
    echo "/> Aktiviert &nbsp;
        <input type=\"radio\" name=\"notebox\" value=\"0\" ";
    if (!$properties->noteBox) echo " checked=\"checked\"";
    echo "/> Deaktiviert
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
} else {
    echo "<i>Datensatz nicht vorhanden!</i>";
}
