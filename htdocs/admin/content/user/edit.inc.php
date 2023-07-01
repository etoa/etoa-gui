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

/** @var */
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


    '<div id="tabs-11">';

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