<?PHP

use EtoA\User\UserMultiRepository;
use EtoA\User\UserRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\User\UserSittingRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var UserRepository */
$userRepository = $app[UserRepository::class];
/** @var UserMultiRepository $userMultiRepository */
$userMultiRepository = $app[UserMultiRepository::class];
/** @var ConfigurationService */
$config = $app[ConfigurationService::class];

$request = Request::createFromGlobals();

if (!$s->sittingActive || $s->falseSitter) {
    //
    // Neuen user anlegen, der am gleichen PC sitzt (multi)
    //

    if (isset($_POST['new_multi']) != "" && checker_verify()) {
        $userMultiRepository->addEmptyEntry($cu->getId());
        success_msg("Neuer User angelegt!");
    }


    //
    // Daten Speichern (multi)
    //

    if (isset($_POST['data_submit_multi']) && checker_verify()) {

        $user = array_unique($_POST['multi_nick']); //löscht alle users die mehrfach eingetragen wurden
        $change = false;
        foreach ($user as $id => $data) {
            if ($user[$id] != "" && ($request->request->get('del_multi', [])[$id] ?? 0) != 1) {
                //Ist dieser User existent
                $userIdForNick = $userRepository->getUserIdByNick($user[$id]);
                if ($userIdForNick === null) {
                    error_msg("Dieser User exisitert nicht!");
                }
                //ist der eigene nick eingetragen
                elseif ($userIdForNick == $cu->id) {
                    error_msg("Du kannst nicht dich selber eintragen!");
                } else {
                    $userMultiRepository->updateEntry((int) $id, $cu->getId(), $userIdForNick, $_POST['connection'][$id]);
                    $change = true;
                }
            }
        }
        if ($change) success_msg("&Auml;nderungen &uuml;bernommen!");

        //Löscht alle angekreuzten user
        if ($request->request->has('del_multi')) {
            // User löschen
            foreach ($_POST['del_multi'] as $id => $data) {
                $id = intval($id);
                $entry = $userMultiRepository->getUserEntry($cu->getId(), $id);
                if ($entry !== null) {
                    if ($entry->reason !== '0' && $entry->multiUserId !== 0) {
                        $userMultiRepository->deleteEntry($entry->id);
                    } else {
                        $userMultiRepository->deactivateEntry($entry->id);
                        // Speichert jeden gelöschten multi (soll vor missbrauch schützen -> mutli erstellen -> löschen -> erstellen -> löschen etc.)
                        dbquery("
                                UPDATE
                                    users
                                SET
                                    user_multi_delets=user_multi_delets+1
                                WHERE
                                    user_id='" . $cu->id . "';");
                    }
                    success_msg("Eintrag gelöscht!");
                }
            }
        }
    }







    //
    // Plan new sitting session
    //
    if (isset($_GET['action']) && $_GET['action'] == "new_sitting") {
        echo "<form action=\"?page=$page&amp;mode=$mode&amp;action=new_sitting\" method=\"post\">";

        /** @var UserSittingRepository $userSittingRepository */
        $userSittingRepository = $app[UserSittingRepository::class];
        $prof_rest_days = max(0, $userSittingRepository->getUsedSittingTime($cu->getId()));
        $form = true;
        if ($prof_rest_days > 0) {

            // Save
            if (isset($_POST['sitting_add'])) {
                if ($_POST['sitter_nick'] != "") {
                    $res = dbquery("SELECT user_id,user_registered FROM users WHERE user_id!=" . $cu->id . " AND user_nick='" . mysql_real_escape_string($_POST['sitter_nick']) . "' LIMIT 1;");
                    if (mysql_num_rows($res) > 0) {
                        $arr = mysql_fetch_row($res);
                        $sitterId = $arr[0];
                        $sitterRegistered = $arr[1];
                        if ($_POST['sitter_password1'] == $_POST['sitter_password2'] && $_POST['sitter_password1'] != "" && strlen($_POST['sitter_password1']) >= $config->getInt('password_minlength')) {
                            $pw = saltPasswort($_POST['sitter_password1']);

                            $res = dbquery("SELECT user_id FROM users WHERE user_password='" . $pw . "' AND user_id=" . $cu->id . " LIMIT 1;");
                            if (mysql_num_rows($res) == 0) {
                                $tm_from = mktime($_POST['date_from_h'], $_POST['date_from_i'], 0, $_POST['date_from_m'], $_POST['date_from_d'], $_POST['date_from_y']);
                                $tm_to = $tm_from + $_POST['date_to_days'] * 86400;

                                /** @var UserSittingRepository $userSittingRepository */
                                $userSittingRepository = $app[UserSittingRepository::class];
                                if ($tm_from > time() - 600  && $tm_from < $tm_to && $_POST['date_to_days'] <= $prof_rest_days) {
                                    if (!$userSittingRepository->hasSittingEntryForTimeSpan($cu->getId(), $tm_from, $tm_to)) {
                                        $userSittingRepository->addEntry($cu->getId(), $sitterId, $pw, $tm_from, $tm_to);
                                        success_msg("Sitting eingerichtet!");
                                        echo "<p>" . button("Weiter", "?page=$page&amp;mode=$mode&amp;") . "</p>";
                                        $form = false;
                                    } else {
                                        error_msg("In diesem Zeitraum existiert bereits ein Sittingeintrag!");
                                    }
                                } else {
                                    error_msg("Ungültiger Zeitraum!");
                                }
                            } else {
                                unset($pw);
                                error_msg("Das Passwort darf nicht dasselbe wie das normale Accountpasswort sein!");
                            }
                        } else {
                            error_msg("Passwörter sind nicht gleich oder zu kurz (mind. " . $config->getInt('password_minlength') . " Zeichen)");
                        }
                    } else {
                        error_msg("Benutzername ist ungültig!");
                    }
                } else {
                    error_msg("Kein Name angegeben!");
                }
            }

            if ($form) {
                tableStart("Neues Sitting einrichten");

                //Sitter Nick
                echo "<tr>
                        <th width=\"35%\">Sitter Nick:</th>
                        <td width=\"65%\" colspan=\"2\" " . tm("Sitter Nick", "Gib hier den Nick des Users an, welcher dein Account Sitten soll.") . ">";
                echo "<input type=\"text\" name=\"sitter_nick\" maxlength=\"20\" size=\"20\" value=\"" . (isset($_POST['sitter_nick']) ? $_POST['sitter_nick'] : '') . "\" id=\"user_nick_sitting\" autocomplete=\"off\" />";
                echo "</td>";
                echo "</tr>";

                //Sitter Passwort
                echo "<tr>
                        <th width=\"35%\">Sitter Passwort:</th>
                        <td width=\"65%\" colspan=\"2\" " . tm("Sitter Passwort", "Definiere hier das Passwort, mit dem sich dein Sitter einlogen kann.") . ">
                            <input type=\"password\" name=\"sitter_password1\" maxlength=\"20\" size=\"20\" value=\"" . (isset($pw) ? $pw : '') . "\" autocomplete=\"off\" />
                        </td>
                     </tr>";

                //Sitter Passwort (wiederholen)
                echo "<tr>
                        <th width=\"35%\">Sitter Passwort (wiederholen):</th>
                        <td width=\"65%\" colspan=\"2\" " . tm("Sitter Passwort (wiederholen)", "Zur SIcherheit, musst du hier das Passwort noch einmal hinschreiben.") . ">
                            <input type=\"password\" name=\"sitter_password2\" maxlength=\"20\" size=\"20\" value=\"" . (isset($pw) ? $pw : '') . "\" autocomplete=\"off\" />
                        </td>
                     </tr>";


                echo "<tr><th>Zeitraun:</th><td>Von ";
                show_timebox("date_from", isset($tm_from) ? $tm_from : time());
                echo " Dauer (Tage) ";

                // erstellt ein Optionsfeld mit den anzahl sitting tagen die der user noch zur verfügung hat

                echo "<select name=\"date_to_days\">";
                for ($x = 1; $prof_rest_days >= $x; $x++) {
                    echo "<option value=\"" . $x . "\" " . (isset($_POST['date_to_days']) && $_POST['date_to_days'] == $x ? ' selected="selected"' : '') . ">" . $x . "</option>";
                }
                echo "</select></td></tr>";
                tableEnd();
                echo "<p>Mit dem Speichern dieses Eintrags werden die entsprechenden Sittingtage von deinem Account abgezogen.<br/><br/><input type=\"submit\" name=\"sitting_add\" value=\"Speichern\" /> ";
            }
        } else {
            error_msg("Alle Sitting-Tage sind aufgebraucht!");
            echo "<p>";
        }
        echo "" . button("Abbrechen", "?page=$page&amp;mode=$mode") . "</p>";
        echo "</form>";
    }

    //
    // Show past and planned sitting sessions
    //
    else {


        //
        // Multierkennung
        //
        echo "<form action=\"?page=$page&mode=sitting\" method=\"post\">";
        $cstr = checker_init();

        $multiEntries = $userMultiRepository->getUserEntries($cu->getId(), true);
        $user_res = dbquery("
            SELECT
                user_sitting_days
            FROM
                users
            WHERE
                user_id='" . $cu->id . "';");
        $user_arr = mysql_fetch_array($user_res);

        tableStart("Multierkennung [<a href=\"?page=help&site=multi_sitting\">Info</a>]");

        echo "<tr>
                    <th width=\"35%\">User</th>
                    <th width=\"55%\">Beziehung</th>
                    <th width=\"10%\">L&ouml;schen</th>
                    ";

        $unused_multi = 0;
        foreach ($multiEntries as $multi) {


            echo "<tr><td>";

            if ($multi->multiUserId !== 0) {
                echo "<input type=\"text\" name=\"multi_nick[" . $multi->id . "]\" maxlength=\"20\" size=\"20\" value=\"" . stripslashes($multi->multiUserNick) . "\" readonly=\"readonly\">";
            } else {
                echo "<input type=\"text\" name=\"multi_nick[" . $multi->id . "]\" id=\"user_nick_multi\"  maxlength=\"20\" size=\"20\" autocomplete=\"off\" value=\"Usernick\" onkeyup=\"xajax_searchUser(this.value,'user_nick_multi','citybox_multi');\"><br/>
                                            <div class=\"citybox\" id=\"citybox_multi\">&nbsp;</div>";
                $unused_multi++;
            }

            echo "</td>";
            echo "<td>";

            if ($multi->reason != '0') {
                echo "<input type=\"text\" name=\"connection[" . $multi->id . "]\" maxlength=\"50\" size=\"50\" value=\"" . stripslashes($multi->reason) . "\" readonly=\"readonly\">";
            } else {
                echo "<input type=\"text\" name=\"connection[" . $multi->id . "]\" maxlength=\"50\" size=\"50\" value=\"\">";
            }

            echo "</td>";
            echo "<td style=\"text-align:center;\">";
            echo "<input type=\"checkbox\" name=\"del_multi[" . $multi->id . "]\" value=\"1\" />";
            echo "</td></tr>";
        }
        // Todo: fix sitting
        if ($unused_multi < 1) {
            echo "<tr><td style=\"text-align:center;\" colspan=\"3\"><input type=\"submit\" name=\"new_multi\" value=\"User hinzuf&uuml;gen\"/></td></tr>";
        }

        tableEnd();

        echo "<input type=\"submit\" name=\"data_submit_multi\" value=\"&Uuml;bernehmen\"/></form><br/><br/><br>";








        //
        // Sitting
        //

        /** @var UserSittingRepository $userSittingRepository */
        $userSittingRepository = $app[UserSittingRepository::class];
        if (isset($_GET['remove_sitting']) && intval($_GET['remove_sitting']) > 0) {
            $success = $userSittingRepository->deleteFutureUserEntry((int) $_GET['remove_sitting'], $cu->getId());
            if ($success)
                success_msg("Sitting entfernt!");
        }
        if (isset($_GET['cancel_sitting']) && intval($_GET['cancel_sitting']) > 0) {
            $success = $userSittingRepository->cancelUserEntry((int) $_GET['cancel_sitting'], $cu->getId());
            if ($success)
                success_msg("Sitting abgebrochen!");
        }


        tableStart("Sitter Einstellungen [<a href=\"?page=help&site=multi_sitting\">Info</a>]");
        $entries = $userSittingRepository->getWhereUser($cu->getId());
        $days = $cu->sittingDays;
        if (count($entries) > 0) {
            echo "<tr><th>Sitter</th><th>Von</th><th>Bis</th><th>Tage</th><th>Aktionen</th></tr>";
            foreach ($entries as $entry) {
                $tdays = ceil(($entry->dateTo - $entry->dateFrom) / 86400);
                $days -= $tdays;
                echo "<tr>
                <td>" . $entry->sitterNick . "</td>
                <td>" . df($entry->dateFrom) . "</td>
                <td>" . df($entry->dateTo) . "</td>
                <td>" . $tdays . "</td>
                <td>";
                if ($entry->dateFrom > time()) {
                    echo "<a href=\"?page=$page&amp;mode=$mode&amp;remove_sitting=" . $entry->id . "\" onclick=\"return confirm('Sittereinstellung löschen?');\">Löschen</a>";
                } elseif ($entry->dateFrom < time() && $entry->dateTo > time()) {
                    echo "AKTIV <a href=\"?page=$page&amp;mode=$mode&amp;cancel_sitting=" . $entry->id . "\" onclick=\"return confirm('Sitting abbrechen?');\">Abbrechen</a>";
                } else {
                    echo "-";
                }
                echo "</td>
                </tr>";
            }
        } else {
            echo "<tr><td>Keine Sitting-Daten vorhanden!</td></tr>";
        }
        tableEnd();
        echo "<p>Noch $days Sittingtage verfügbar.</p>";
        if ($days > 0)
            echo "<p>" . button("Sitting einrichten/hinzufügen", "?page=$page&amp;mode=$mode&amp;action=new_sitting") . "</p>";
    }
}
