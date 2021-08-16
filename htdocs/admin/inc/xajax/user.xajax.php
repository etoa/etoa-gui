<?PHP

use EtoA\Admin\AdminUserRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Building\BuildingDataRepository;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Help\TicketSystem\TicketRepository;
use EtoA\Log\GameLogFacility;
use EtoA\Log\GameLogRepository;
use EtoA\Log\GameLogSearch;
use EtoA\Log\LogSeverity;
use EtoA\Message\MessageRepository;
use EtoA\Ship\ShipDataRepository;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserCommentRepository;
use EtoA\User\UserLogRepository;
use EtoA\User\UserPointsRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserService;

$xajax->register(XAJAX_FUNCTION, "showTimeBox");
$xajax->register(XAJAX_FUNCTION, "allianceRankSelector");
$xajax->register(XAJAX_FUNCTION, "userPointsTable");
$xajax->register(XAJAX_FUNCTION, "addUserComment");
$xajax->register(XAJAX_FUNCTION, "delUserComment");
$xajax->register(XAJAX_FUNCTION, "userLogs");
$xajax->register(XAJAX_FUNCTION, "addUserLog");
$xajax->register(XAJAX_FUNCTION, "userTickets");
$xajax->register(XAJAX_FUNCTION, "userComments");
$xajax->register(XAJAX_FUNCTION, "sendUrgendMsg");
$xajax->register(XAJAX_FUNCTION, "showLast5Messages");
$xajax->register(XAJAX_FUNCTION, "loadEconomy");

function showTimeBox($parent, $name, $value, $show = 1)
{
    $or = new xajaxResponse();
    ob_start();
    if ($show > 0) {
        show_timebox($name, intval($value), 1);
    } else {
        echo "-";
    }
    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($parent, "innerHTML", $out);
    return $or;
}

function allianceRankSelector($parent, $name, $value = 0, $aid = 0)
{
    global $app;

    /** @var AllianceRankRepository $allianceRankRepository */
    $allianceRankRepository = $app[AllianceRankRepository::class];

    $or = new xajaxResponse();
    ob_start();
    if ($aid != 0) {
        $ranks = $allianceRankRepository->getRanks($aid);
        if (count($ranks) > 0) {
            echo "<select name=\"" . $name . "\"><option value=\"0\">(Kein Rang)</option>";
            foreach ($ranks as $rank) {
                echo "<option value=\"" . $rank->id . "\"";
                if ($value == $rank->id) {
                    echo " selected=\"selected\"";
                }
                echo ">" . $rank->name . "</option>";
            }
            echo "</select>";
        } else {
            echo "-";
        }
    } else {
        echo "-";
    }
    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($parent, "innerHTML", $out);
    return $or;
}

function userPointsTable($uid, $target, $length = 100, $start = -1, $end = -1)
{
    global $app;

    /** @var UserPointsRepository $userPointsRepository */
    $userPointsRepository = $app[UserPointsRepository::class];

    $t = time();
    if ($start == -1) {
        $start = $t - 172800;
    }
    if ($end == -1) {
        $end = $t;
    }

    $or = new xajaxResponse();
    ob_start();
    $limitarr = array(10, 20, 30, 50, 100, 200);

    echo "<div id=\"pointGraphDetail\" style=\"text-align:center;margin-bottom:6px;\">
    <img src=\"../misc/stats.image.php?user=" . $uid . "&amp;limit=" . $length . "&amp;start=" . $start . "&amp;end=" . $end . "\" alt=\"Diagramm\" />
    <br/>";
    echo "Zeige maximal <select id=\"pointsLimit\" onchange=\"xajax_userPointsTable($uid,'$target',
    document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
    document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
    document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
    );\">";
    foreach ($limitarr as $x) {
        echo "<option value=\"$x\"";
        if ($x == $length) echo " selected=\"selected\"";
        echo ">$x</option>";
    }
    echo "</select> Datensätze von <select id=\"pointsTimeStart\" onchange=\"xajax_userPointsTable($uid,'$target',
    document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
    document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
    document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
    );\">";
    for ($x = $t - 86400; $x > $t - (14 * 86400); $x -= 86400) {
        echo "<option value=\"$x\"";
        if ($x <= $start + 300 && $x >= $start - 300) echo " selected=\"selected\"";
        echo ">" . df($x) . "</option>";
    }
    echo "</select> bis <select id=\"pointsTimeEnd\" onchange=\"xajax_userPointsTable($uid,'$target',
    document.getElementById('pointsLimit').options[document.getElementById('pointsLimit').selectedIndex].value,
    document.getElementById('pointsTimeStart').options[document.getElementById('pointsTimeStart').selectedIndex].value,
    document.getElementById('pointsTimeEnd').options[document.getElementById('pointsTimeEnd').selectedIndex].value
    );\">";
    for ($x = $t; $x > $t - (13 * 86400); $x -= 86400) {
        echo "<option value=\"$x\"";
        if ($x <= $end + 300 && $x >= $end - 300) echo " selected=\"selected\"";
        echo ">" . df($x) . "</option>";
    }
    echo "</select>

    <br/></div>";
    echo "<table class=\"tb\">";
    $userPoints = $userPointsRepository->getPoints($uid, $length, $start, $end);
    if (count($userPoints) > 0) {
        echo "<tr>
            <th>Datum</th>
            <th>Zeit</th>
            <th>Punkte</th>
            <th>Gebäude</th>
            <th>Forschung</th>
            <th>Flotte</th>
        </tr>";
        foreach ($userPoints as $points) {
            echo "<tr>
                <td class=\"tbldata\">" . date("d.m.Y", $points->timestamp) . "</td>
                <td class=\"tbldata\">" . date("H:i", $points->timestamp) . "</td>
                <td class=\"tbldata\">" . nf($points->points) . "</td>
                <td class=\"tbldata\">" . nf($points->buildingPoints) . "</td>
                <td class=\"tbldata\">" . nf($points->techPoints) . "</td>
                <td class=\"tbldata\">" . nf($points->shipPoints) . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine fehlgeschlagenen Logins</td></tr>";
    }
    echo "</table>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

function userTickets($uid, $target)
{
    global $app;

    /** @var AdminUserRepository */
    $adminUserRepo = $app[AdminUserRepository::class];

    /** @var TicketRepository */
    $ticketRepo = $app[TicketRepository::class];

    $or = new xajaxResponse();
    ob_start();
    echo "<table class=\"tb\">";

    $tickets = $ticketRepo->findBy(["user_id" => $uid]);
    if (count($tickets) > 0) {
        echo "<tr>
            <th>ID</th>
            <th>Datum</th>
            <th>Kategorie</th>
            <th>Status</th>
            <th>Admin</th>
        </tr>";
        foreach ($tickets as $ticket) {
            $adminNick = $adminUserRepo->getNick($ticket->adminId);
            echo "<tr>
                <td><a href=\"?page=tickets&id=" . $ticket->id . "\">" . $ticket->getIdString() . "</a></td>
                <td class=\"tbldata\">" . df($ticket->timestamp) . "</td>
                <td class=\"tbldata\">" . $ticketRepo->getCategoryName($ticket->catId) . "</td>
                <td class=\"tbldata\">" . $ticket->getStatusName() . "</td>
                <td class=\"tbldata\">" . $adminNick . "</td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine Tickets</td></tr>";
    }
    echo "</table>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}


function sendUrgendMsg($uid, $subject, $text)
{
    global $app;

    $or = new xajaxResponse();
    if ($text != "" && $subject != "") {
        /** @var \EtoA\Message\MessageRepository $messageRepository */
        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
        $messageRepository->createSystemMessage((int) $uid, USER_MSG_CAT_ID, $subject, $text);

        $or->alert("Nachricht gesendet!");
        $or->assign('urgendmsgsubject', "value", "");
        $or->assign('urgentmsg', "value", "");
        $or->script("showLoader('lastmsgbox');xajax_showLast5Messages(" . $uid . ",'lastmsgbox');");
    } else {
        $or->alert("Titel oder Text fehlt!");
    }
    return $or;
}

function showLast5Messages($uid, $target, $limit = 5)
{
    $or = new xajaxResponse();

    // TODO
    global $app;

    /** @var MessageRepository */
    $messageRepository = $app[MessageRepository::class];

    /** @var UserRepository */
    $userRepo = $app[UserRepository::class];

    ob_start();
    echo "<table class=\"tb\">";

    $messages = $messageRepository->findBy([
        'user_to_id' => $uid,
    ], $limit);
    if (count($messages) > 0) {
        echo "<tr>
            <th>Datum</th>
            <th>Sender</th>
            <th>Titel</th>
            <th>Text</th>
            <th>Gelesen</th>
            <th>Optionen</th>
        </tr>";
        foreach ($messages as $message) {
            echo "<tr>
                <td class=\"tbldata\">" . df($message->timestamp) . "</td>
                <td class=\"tbldata\">";
            if ($message->userFrom > 0) {
                echo "<a href=\"?page=user&sub=edit&user_id=" . $message->userFrom . "\">" . $userRepo->getNick($message->userFrom) . "</a>";
            } else {
                echo "System";
            }
            echo "</td>
                <td class=\"tbldata\">" . $message->subject . "</td>
                <td class=\"tbldata\">" . text2html($message->text) . "</td>
                <td class=\"tbldata\">" . ($message->read ? "Ja" : "Nein") . "</td>
                <td class=\"tbldata\">[<a href=\"?page=messages&sub=edit&message_id=" . $message->id . "\">Details</a>]</td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine Nachrichten vorhanden!</td></tr>";
    }
    echo "</table>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

function userComments($uid, $target)
{
    global $app;

    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];

    $or = new xajaxResponse();
    ob_start();
    echo "<h2>Neuer Kommentar:</h2><textarea rows=\"4\" cols=\"70\" id=\"new_comment_text\"></textarea><br/><br/>";
    echo "<input type=\"button\" onclick=\"xajax_addUserComment('$uid','$target',document.getElementById('new_comment_text').value);\" value=\"Speichern\" />";
    echo "<h2>Gespeicherte Kommentare</h2><table class=\"tb\">";

    $comments = $userCommentRepository->getComments($uid);
    if (count($comments) > 0) {
        echo "<tr>
            <th>Text</th>
            <th>Verfasst</th>
            <th>Aktionen</th>
        </tr>";
        foreach ($comments as $comment) {
            echo "<tr>
                <td class=\"tbldata\" >" . text2html($comment->text) . "</td>
                <td class=\"tbldata\" style=\"width:200px;\">" . df($comment->timestamp) . " von " . $comment->adminNick . "</td>
                <td class=\"tbldata\" style=\"width:50px;\"><a href=\"javascript:;\" onclick=\"if (confirm('Wirklich löschen?')) {xajax_delUserComment('" . $uid . "','" . $target . "'," . $comment->id . ")}\">Löschen</a></td>
            </tr>";
        }
    } else {
        echo "<tr><td class=\"tbldata\">Keine Kommentare</td></tr>";
    }
    echo "</table></div>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

function addUserComment($uid, $target, $text)
{
    global $app;

    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];

    $or = new xajaxResponse();
    if ($text != "") {
        $or->script("showLoader('$target');");
        $userCommentRepository->addComment($uid, $_SESSION['user_id'], $text);
        $or->script("xajax_userComments('$uid','$target')");
    } else {
        $or->alert("Fehler! Kein Text!");
    }
    return $or;
}

function delUserComment($uid, $target, $id)
{
    global $app;

    /** @var UserCommentRepository $userCommentRepository */
    $userCommentRepository = $app[UserCommentRepository::class];

    $or = new xajaxResponse();
    if ($id > 0) {
        $or->script("showLoader('$target');");
        $userCommentRepository->deleteComment($id);
        $or->script("xajax_userComments('$uid','$target')");
    } else {
        $or->alert("Fehler! Falsche ID!");
    }
    return $or;
}


function userLogs($uid, $target)
{
    global $app;

    /** @var UserLogRepository $userLogRepository */
    $userLogRepository = $app[UserLogRepository::class];

    $or = new xajaxResponse();
    ob_start();
    tableStart("", '100%');
    echo "<tr><th>Nachricht</th><th>Datum</th><th>IP</th></tr>";
    $logs = $userLogRepository->getUserLogs($uid, 100);
    foreach ($logs as $log) {
        echo "<tr><td>" . text2html($log->message) . "</td>
                        <td>" . df($log->timestamp) . "</td>
                        <td><a href=\"?page=user&amp;sub=ipsearch&amp;ip=" . $log->host . "\">" . $log->host . "</a></td></tr>";
    }
    tableEnd();

    echo "<h2>Neuer Log:</h2><textarea rows=\"4\" cols=\"70\" id=\"new_log\"></textarea><br/><br/>";

    echo "<input type=\"button\" onclick=\"xajax_addUserLog('$uid','$target',document.getElementById('new_log').value);\" value=\"Speichern\" />";


    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}

function addUserLog($uid, $target, $text)
{
    // TODO
    global $app;

    /** @var UserService */
    $userService = $app[UserService::class];

    $or = new xajaxResponse();
    if ($text != "") {
        $or->script("showLoader('$target');");
        $userService->addToUserLog($uid, "settings", $text, true);
        $or->script("xajax_userLogs('$uid','$target')");
    } else {
        $or->alert("Fehler! Kein Text!");
    }
    return $or;
}

function loadEconomy($uid, $target)
{
    global $app;

    /** @var PlanetRepository $planetRepository */
    $planetRepository = $app[PlanetRepository::class];
    /** @var GameLogRepository $gameLogRepository */
    $gameLogRepository = $app[GameLogRepository::class];

    $or = new xajaxResponse();
    ob_start();

    // Stopt Ladedauer
    $tmr = timerStart();

    //
    // Rohstoff- und Produktionsübersicht
    //

    echo "<fieldset><legend>Rohstoff- und Produktionsübersicht</legend>";

    // Sucht alle Planet IDs des Users
    $userPlanets = $planetRepository->getUserPlanetsWithCoordinates($uid);
    if (count($userPlanets) > 0) {
        $cnt_res = 0;
        $max_res = array(0, 0, 0, 0, 0, 0);
        $min_res = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
        $tot_res = array(0, 0, 0, 0, 0, 0);

        $cnt_prod = 0;
        $max_prod = array(0, 0, 0, 0, 0, 0);
        $min_prod = array(9999999999, 9999999999, 9999999999, 9999999999, 9999999999, 9999999999);
        $tot_prod = array(0, 0, 0, 0, 0, 0);
        $val_res = [];
        $val_prod = [];
        $val_store = [];
        $val_time = [];
        foreach ($userPlanets as $planet) {
            //Speichert die aktuellen Rohstoffe in ein Array
            $val_res[$planet->id][0] = floor($planet->resMetal);
            $val_res[$planet->id][1] = floor($planet->resCrystal);
            $val_res[$planet->id][2] = floor($planet->resPlastic);
            $val_res[$planet->id][3] = floor($planet->resFuel);
            $val_res[$planet->id][4] = floor($planet->resFood);
            $val_res[$planet->id][5] = floor($planet->people);

            for ($x = 0; $x < 6; $x++) {
                $max_res[$x] = max($max_res[$x], $val_res[$planet->id][$x]);
                $min_res[$x] = min($min_res[$x], $val_res[$planet->id][$x]);
                $tot_res[$x] += $val_res[$planet->id][$x];
            }

            //Speichert die aktuellen Rohstoffproduktionen in ein Array
            $val_prod[$planet->id][0] = floor($planet->prodMetal);
            $val_prod[$planet->id][1] = floor($planet->prodCrystal);
            $val_prod[$planet->id][2] = floor($planet->prodPlastic);
            $val_prod[$planet->id][3] = floor($planet->prodFuel);
            $val_prod[$planet->id][4] = floor($planet->prodFood);
            $val_prod[$planet->id][5] = floor($planet->prodPeople);

            for ($x = 0; $x < 6; $x++) {
                $max_prod[$x] = max($max_prod[$x], $val_prod[$planet->id][$x]);
                $min_prod[$x] = min($min_prod[$x], $val_prod[$planet->id][$x]);
                $tot_prod[$x] += $val_prod[$planet->id][$x];
            }

            //Speichert die aktuellen Speicher in ein Array
            $val_store[$planet->id][0] = floor($planet->storeMetal);
            $val_store[$planet->id][1] = floor($planet->storeCrystal);
            $val_store[$planet->id][2] = floor($planet->storePlastic);
            $val_store[$planet->id][3] = floor($planet->storeFuel);
            $val_store[$planet->id][4] = floor($planet->storeFood);
            $val_store[$planet->id][5] = floor($planet->peoplePlace);

            //Berechnet die dauer bis die Speicher voll sind (zuerst prüfen ob Division By Zero!)

            //Titan
            if ($planet->prodMetal > 0) {
                if ($planet->storeMetal - $planet->resMetal > 0) {
                    $val_time[$planet->id][0] = ceil(($planet->storeMetal - $planet->resMetal) / $planet->prodMetal * 3600);
                } else {
                    $val_time[$planet->id][0] = 0;
                }
            } else {
                $val_time[$planet->id][0] = 0;
            }

            //Silizium
            if ($planet->prodCrystal > 0) {
                if ($planet->storeCrystal - $planet->resCrystal > 0) {
                    $val_time[$planet->id][1] = ceil(($planet->storeCrystal - $planet->resCrystal) / $planet->prodCrystal * 3600);
                } else {
                    $val_time[$planet->id][1] = 0;
                }
            } else {
                $val_time[$planet->id][1] = 0;
            }

            //PVC
            if ($planet->prodPlastic > 0) {
                if ($planet->storePlastic - $planet->resPlastic > 0) {
                    $val_time[$planet->id][2] = ceil(($planet->storePlastic - $planet->resPlastic) / $planet->prodPlastic * 3600);
                } else {
                    $val_time[$planet->id][2] = 0;
                }
            } else {
                $val_time[$planet->id][2] = 0;
            }

            //Tritium
            if ($planet->prodFuel > 0) {
                if ($planet->storeFuel - $planet->resFuel > 0) {
                    $val_time[$planet->id][3] = ceil(($planet->storeFuel - $planet->resFuel) / $planet->prodFuel * 3600);
                } else {
                    $val_time[$planet->id][3] = 0;
                }
            } else {
                $val_time[$planet->id][3] = 0;
            }

            //Nahrung
            if ($planet->prodFood > 0) {
                if ($planet->storeFood - $planet->resFood > 0) {
                    $val_time[$planet->id][4] = ceil(($planet->storeFood - $planet->resFood) / $planet->prodFood * 3600);
                } else {
                    $val_time[$planet->id][4] = 0;
                }
            } else {
                $val_time[$planet->id][4] = 0;
            }

            //Bewohner
            if ($planet->prodPeople > 0) {
                if ($planet->peoplePlace - $planet->people > 0) {
                    $val_time[$planet->id][5] = ceil(($planet->peoplePlace - $planet->people) / $planet->prodPeople * 3600);
                } else {
                    $val_time[$planet->id][5] = 0;
                }
            } else {
                $val_time[$planet->id][5] = 0;
            }
        }


        //
        // Rohstoffe/Bewohner und Speicher
        //

        echo "<h2>Rohstoffe und Bewohner</h2>";
        echo "<table class=\"tbl\">";
        echo "<tr>
                                    <td class=\"tbltitle\">Name:</td>
                                    <td class=\"tbltitle\">" . RES_METAL . "</td>
                                    <td class=\"tbltitle\">" . RES_CRYSTAL . "</td>
                                    <td class=\"tbltitle\">" . RES_PLASTIC . "</td>
                                    <td class=\"tbltitle\">" . RES_FUEL . "</td>
                                    <td class=\"tbltitle\">" . RES_FOOD . "</td>
                                    <td class=\"tbltitle\">Bewohner</td>
                                </tr>";
        foreach ($userPlanets as $planet) {
            echo "<tr>
                                        <td class=\"tbldata\">
                                            <a href=\"?page=galaxy&sub=edit&id=" . $planet->id . "\">" . $planet->toString() . "</a>
                                        </td>";
            for ($x = 0; $x < 6; $x++) {
                echo "<td";
                if ($max_res[$x] == $val_res[$planet->id][$x]) {
                    echo " class=\"tbldata3\"";
                } elseif ($min_res[$x] == $val_res[$planet->id][$x]) {
                    echo " class=\"tbldata2\"";
                } else {
                    echo " class=\"tbldata\"";
                }


                //Der Speicher ist noch nicht gefüllt
                if ($val_res[$planet->id][$x] < $val_store[$planet->id][$x] && $val_time[$planet->id][$x] != 0) {
                    echo " " . tm("Speicher", "Speicher voll in " . tf($val_time[$planet->id][$x]) . "") . " ";
                    if ($val_time[$planet->id][$x] < 43200) {
                        echo " style=\"font-style:italic;\" ";
                    }
                    echo ">" . nf($val_res[$planet->id][$x]) . "</td>";
                }
                //Speicher Gefüllt
                else {
                    echo " " . tm("Speicher", "Speicher voll!") . "";
                    echo " style=\"\" ";
                    echo "><b>" . nf($val_res[$planet->id][$x]) . "</b></td>";
                }
            }
            echo "</tr>";
            $cnt_res++;
        }
        echo "<tr>
                                    <td colspan=\"6\"></td>
                                </tr>
                                <tr>
                                    <td class=\"tbltitle\">Total</td>";
        for ($x = 0; $x < 6; $x++) {
            echo "<td class=\"tbltitle\">" . nf($tot_res[$x]) . "</td>";
        }
        echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
        for ($x = 0; $x < 6; $x++) {
            echo "<td class=\"tbltitle\">" . nf($tot_res[$x] / $cnt_res) . "</td>";
        }
        echo "</tr>";
        echo "</table>";



        //
        // Rohstoffproduktion inkl. Energie
        //

        // Ersetzt Bewohnerwerte durch Energiewerte
        $max_prod[5] = 0;
        $min_prod[5] = 9999999999;
        $tot_prod[5] = 0;
        foreach ($userPlanets as $planet) {
            //Speichert die aktuellen Energieproduktionen in ein Array (Bewohnerproduktion [5] wird überschrieben)
            $val_prod[$planet->id][5] = floor($planet->prodPower);

            // Gibt Min. / Max. aus
            $max_prod[5] = max($max_prod[5], $val_prod[$planet->id][5]);
            $min_prod[5] = min($min_prod[5], $val_prod[$planet->id][5]);
            $tot_prod[5] += $val_prod[$planet->id][5];
        }

        echo "<p>Legende: Minimum, Maximum,
                        <span style=\"font-style:italic\">Speicher bald voll</span>,
                        <span class=\"tbldata\" style=\"font-weight:bold\">Speicher voll</span>
                    </p>";


        echo "<h2>Produktion</h2>";
        echo "<table class=\"tbl\">";
        echo "<tr><th class=\"tbltitle\">Name:</th>
                    <th class=\"tbltitle\">" . RES_METAL . "</th>
                    <th class=\"tbltitle\">" . RES_CRYSTAL . "</th>
                    <th class=\"tbltitle\">" . RES_PLASTIC . "</th>
                    <th class=\"tbltitle\">" . RES_FUEL . "</th>
                    <th class=\"tbltitle\">" . RES_FOOD . "</th>
                    <th class=\"tbltitle\">Energie</th></tr>";
        foreach ($userPlanets as $planet) {
            echo "<tr><td class=\"tbldata\"><a href=\"?page=galaxy&amp;sub=edit&amp;id=" . $planet->id . "\">" . $planet->toString() . "</a></td>";
            for ($x = 0; $x < 6; $x++) {
                /*
                            // Erstellt TM-Box für jeden Rohstoff
                            // Titan
                            if($x == 0)
                            {
                                $tm_header = "Titan-Bonis";
                                $tm = "".$arr['race_name'].": ".$arr['race_f_metal']."<br\>".$p->type->name.": ".$p->type->metal."<br\>".$p->sol_type_name.": ".$p->sol->type->metal."";
                            }
                            elseif($x == 1)
                            {
                                $tm_header = "Silizium-Bonis";
                                $tm = "".$arr['race_name'].": ".$arr['race_f_crystal']."<br\>".$p->type->name.": ".$p->type->crystal."<br\>".$p->sol_type_name.": ".$p->sol->type->crystal."";
                            }
                            elseif($x == 2)
                            {
                                $tm_header = "PVC-Bonis";
                                $tm = "".$arr['race_name'].": ".$arr['race_f_plastic']."<br\>".$p->type->name.": ".$p->type->plastic."<br\>".$p->sol_type_name.": ".$p->sol->type->plastic."";
                            }
                            elseif($x == 3)
                            {
                                $tm_header = "Tritium-Bonis";
                                $tm = "".$arr['race_name'].": ".$arr['race_f_fuel']."<br\>".$p->type->name.": ".$p->type->fuel."<br\>".$p->sol_type_name.": ".$p->sol->type->fuel."";
                            }
                            elseif($x == 4)
                            {
                                $tm_header = "Nahrungs-Bonis";
                                $tm = "".$arr['race_name'].": ".$arr['race_f_food']."<br\>".$p->type->name.": ".$p->type->food."<br\>".$p->sol_type_name.": ".$p->sol->type->food."";
                            }
                            elseif($x == 5)
                            {
                                $tm_header = "Energie-Bonis";
                                $tm = "".$arr['race_name'].": ".$arr['race_f_power']."<br\>".$p->type->name.": ".$p->type->power."<br\>".$p->sol_type_name.": ".$p->sol->type->power."";
                            }
                            else
                            {
                                $tm_header = "";
                                $tm = "";
                            }
                            */
                $tm_header = "";
                $tm = "";

                echo "<td";
                if ($max_prod[$x] == $val_prod[$planet->id][$x]) {
                    echo " class=\"tbldata3\"";
                } elseif ($min_prod[$x] == $val_prod[$planet->id][$x]) {
                    echo " class=\"tbldata2\"";
                } else {
                    echo " class=\"tbldata\"";
                }
                echo " " . tm($tm_header, $tm) . ">" . nf($val_prod[$planet->id][$x]) . "</td>";
            }
            echo "</tr>";
            $cnt_prod++;
        }
        echo "<tr><td colspan=\"6\"></td></tr>";
        echo "<tr><th class=\"tbltitle\">Total</th>";
        for ($x = 0; $x < 6; $x++)
            echo "<td class=\"tbltitle\">" . nf($tot_prod[$x]) . "</td>";
        echo "</tr><tr><th class=\"tbltitle\">Durchschnitt</th>";
        for ($x = 0; $x < 6; $x++)
            echo "<td class=\"tbltitle\">" . nf($tot_prod[$x] / $cnt_prod) . "</td>";
        echo "</tr>";
        echo "</table>";
    } else {
        echo "Der User hat noch keinen Planeten!";
    }
    echo "</fieldset>";


    echo "<fieldset><legend>Die fünf letzten Aufträge</legend>";


    //
    // 5 letzte Bauaufträge
    //

    echo "<h2>Bauaufträge</h2>";

    $logs = $gameLogRepository->searchLogs(GameLogSearch::create()->userId($uid)->facility(GameLogFacility::BUILD), 5);
    if (count($logs) > 0) {
        tableStart();
        echo "<tr>
                        <th style=\"width:140px;\">Datum</th>
                        <th style=\"\">Schweregrad</th>
                        <th>Raumobjekt</th>
                        <th>Einheit</th>
                        <th>Status</th>
                        <th>Optionen</th>
                    </tr>";

        /** @var BuildingDataRepository $buildingRepository */
        $buildingRepository = $app[BuildingDataRepository::class];
        $buildingNames = $buildingRepository->getBuildingNames(true);

        foreach ($logs as $log) {
            $te = ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-";
            $ob = $buildingNames[$log->objectId] . " " . ($log->level > 0 ? $log->level : '');
            switch ($log->status) {
                case 1:
                    $obStatus = "Ausbau abgebrochen";
                    break;
                case 2:
                    $obStatus = "Abriss abgebrochen";
                    break;
                case 3:
                    $obStatus = "Ausbau";
                    break;
                case 4:
                    $obStatus = "Abriss";
                    break;
                default:
                    $obStatus = '-';
            }

            echo "<tr>
                        <td>" . df($log->timestamp) . "</td>
                        <td>" . LogSeverity::SEVERITIES[$log->severity] . "</td>
                        <td>" . $te . "</td>
                        <td>" . $ob . "</td>
                        <td>" . $obStatus . "</td>
                        <td><a href=\"javascript:;\" onclick=\"toggleBox('details" . $log->id . "')\">Details</a></td>
                        </tr>";
            echo "<tr id=\"details" . $log->id . "\" style=\"display:none;\"><td colspan=\"9\">" . text2html($log->message) . "
                        <br/><br/>IP: " . $log->ip . "</td></tr>";
        }

        tableEnd();
    } else {
        echo "Es sind keine Logs vorhanden!";
    }


    //
    // 5 letzte Forschungsaufträge
    //

    echo "<h2>Forschungsaufträge</h2>";

    $logs = $gameLogRepository->searchLogs(GameLogSearch::create()->userId($uid)->facility(GameLogFacility::TECH), 5);
    if (count($logs) > 0) {
        tableStart();
        echo "<tr>
                        <th style=\"width:140px;\">Datum</th>
                        <th style=\"\">Schweregrad</th>
                        <th>Raumobjekt</th>
                        <th>Einheit</th>
                        <th>Status</th>
                        <th>Optionen</th>
                    </tr>";

        /** @var TechnologyDataRepository $techRepository */
        $techRepository = $app[TechnologyDataRepository::class];
        $technologyNames = $techRepository->getTechnologyNames(true);
        foreach ($logs as $log) {
            $te = ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-";
            $ob = $technologyNames[$log->objectId] . " " . ($log->level > 0 ? $log->level : '');
            switch ($log->status) {
                case 3:
                    $obStatus = "Erforschung";
                    break;
                case 0:
                    $obStatus = "Erforschung abgebrochen";
                    break;
                default:
                    $obStatus = '-';
            }

            echo "<tr>
                        <td>" . df($log->timestamp) . "</td>
                        <td>" . LogSeverity::SEVERITIES[$log->severity] . "</td>
                        <td>" . $te . "</td>
                        <td>" . $ob . "</td>
                        <td>" . $obStatus . "</td>
                        <td><a href=\"javascript:;\" onclick=\"toggleBox('details" . $log->id . "')\">Details</a></td>
                        </tr>";
            echo "<tr id=\"details" . $log->id . "\" style=\"display:none;\"><td colspan=\"9\">" . text2html($log->message) . "
                        <br/><br/>IP: " . $log->ip . "</td></tr>";
        }

        tableEnd();
    } else {
        echo "Es sind keine Logs vorhanden!";
    }


    //
    // 5 letzte Schiffsaufträge
    //

    echo "<h2>Schiffsaufträge</h2>";
    $logs = $gameLogRepository->searchLogs(GameLogSearch::create()->userId($uid)->facility(GameLogFacility::SHIP), 5);
    if (count($logs) > 0) {
        tableStart();
        echo "<tr>
                        <th style=\"width:140px;\">Datum</th>
                        <th style=\"\">Schweregrad</th>
                        <th>Raumobjekt</th>
                        <th>Einheit</th>
                        <th>Status</th>
                        <th>Optionen</th>
                    </tr>";

        /** @var ShipDataRepository $shipRepository */
        $shipRepository = $app[ShipDataRepository::class];
        $shipNames = $shipRepository->getShipNames(true);

        foreach ($logs as $log) {
            $te = ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-";
            $ob = $log->objectId > 0 ? $shipNames[$log->objectId] . ' ' . ($log->level > 0 ? $log->level . 'x' : '') : '-';
            switch ($log->status) {
                case 1:
                    $obStatus = "Bau";
                    break;
                case 0:
                    $obStatus = "Bau abgebrochen";
                    break;
                default:
                    $obStatus = '-';
            }

            echo "<tr>
                        <td>" . df($log->timestamp) . "</td>
                        <td>" . LogSeverity::SEVERITIES[$log->severity] . "</td>
                        <td>" . $te . "</td>
                        <td>" . $ob . "</td>
                        <td>" . $obStatus . "</td>
                        <td><a href=\"javascript:;\" onclick=\"toggleBox('details" . $log->id . "')\">Details</a></td>
                        </tr>";
            echo "<tr id=\"details" . $log->id . "\" style=\"display:none;\"><td colspan=\"9\">" . text2html($log->message) . "
                        <br/><br/>IP: " . $log->ip . "</td></tr>";
        }

        tableEnd();
    } else {
        echo "Es sind keine Logs vorhanden!";
    }



    //
    // 5 letzte Verteidigungsaufträge
    //

    echo "<h2>Verteidigungsaufträge</h2>";

    $logs = $gameLogRepository->searchLogs(GameLogSearch::create()->userId($uid)->facility(GameLogFacility::DEF), 5);
    if (count($logs) > 0) {
        tableStart();
        echo "<tr>
                        <th style=\"width:140px;\">Datum</th>
                        <th style=\"\">Schweregrad</th>
                        <th>Raumobjekt</th>
                        <th>Einheit</th>
                        <th>Status</th>
                        <th>Optionen</th>
                    </tr>";

        /** @var DefenseDataRepository $defenseRepository */
        $defenseRepository = $app[DefenseDataRepository::class];
        $defenseNames = $defenseRepository->getDefenseNames(true);

        foreach ($logs as $log) {
            $te = ($log->entityId > 0) ? Entity::createFactoryById($log->entityId) : "-";
            $ob = $log->objectId > 0 ? $defenseNames[$log->objectId] . ' ' . ($log->level > 0 ? $log->level . 'x' : '') : '-';
            switch ($log->status) {
                case 1:
                    $obStatus = "Bau";
                    break;
                case 0:
                    $obStatus = "Bau abgebrochen";
                    break;
                default:
                    $obStatus = '-';
            }

            echo "<tr>
                        <td>" . df($log->timestamp) . "</td>
                        <td>" . LogSeverity::SEVERITIES[$log->severity] . "</td>
                        <td>" . $te . "</td>
                        <td>" . $ob . "</td>
                        <td>" . $obStatus . "</td>
                        <td><a href=\"javascript:;\" onclick=\"toggleBox('details" . $log->id . "')\">Details</a></td>
                        </tr>";
            echo "<tr id=\"details" . $log->id . "\" style=\"display:none;\"><td colspan=\"9\">" . text2html($log->message) . "
                        <br/><br/>IP: " . $log->ip . "</td></tr>";
        }

        tableEnd();
    } else {
        echo "Es sind keine Logs vorhanden!";
    }
    echo "</fieldset>";

    echo "<p>Wirtschaftsseite geladen in " . timerStop($tmr) . " sec <input type=\"button\" value=\"Wirtschaftsdaten neu laden\" onclick=\"showLoader('tabEconomy');xajax_loadEconomy(" . $uid . ",'tabEconomy');\" /></p>";

    $out = ob_get_contents();
    ob_end_clean();
    $or->assign($target, "innerHTML", $out);
    return $or;
}
