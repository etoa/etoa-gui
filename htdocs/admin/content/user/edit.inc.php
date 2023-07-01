<?php

use EtoA\Admin\AdminUserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\HostCache\NetworkNameService;
use EtoA\Ranking\UserBannerService;
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


    '<div id="tabs-7">';

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

}