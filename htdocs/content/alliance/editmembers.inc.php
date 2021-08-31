<?PHP

use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Alliance\AllianceSearch;
use EtoA\Alliance\AllianceService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];
/** @var AllianceRepository $allianceRepository */
$allianceRepository = $app[AllianceRepository::class];
/** @var AllianceHistoryRepository */
$allianceHistoryRepository = $app[AllianceHistoryRepository::class];
/** @var AllianceRankRepository $allianceRankRepository */
$allianceRankRepository = $app[AllianceRankRepository::class];
/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var LogRepository $logRepository */
$logRepository = $app[LogRepository::class];
/** @var AllianceService $allianceService */
$allianceService = $app[AllianceService::class];
/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

/** @var \EtoA\Alliance\Alliance $alliance */
/** @var bool $isFounder */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

if ($userAlliancePermission->checkHasRights(AllianceRights::EDIT_MEMBERS, $page)) {
    $currentAlliance = $allianceRepository->getAlliance($alliance->id);

    echo "<h2>Allianzmitglieder</h2>";
    // Ränge laden
    $rank = [];
    $ranks = $allianceRankRepository->getRanks($cu->allianceId());
    foreach ($ranks as $r) {
        $rank[$r->id] = $r->name;
    }
    echo "<form action=\"?page=$page&amp;action=editmembers\" method=\"post\">";


    // Mitgliederänderungen speichern
    if (isset($_POST['editmemberssubmit']) && checker_verify()) {
        if (isset($_POST['user_alliance_rank_id']) && count($_POST['user_alliance_rank_id']) > 0) {
            foreach ($_POST['user_alliance_rank_id'] as $uid => $rid) {
                $uid = intval($uid);
                $rid = intval($rid);

                if (!$userRepository->hasUserRankId($cu->allianceId(), $uid, $rid)) {
                    $userRepository->setAllianceId($uid, $cu->allianceId(), $rid);
                    $allianceHistoryRepository->addEntry($currentAlliance->id, "Der Spieler [b]" . get_user_nick($uid) . "[/b] erhält den Rang [b]" . $rank[$rid] . "[/b].");
                }
            }
            success_msg("&Auml;nderungen wurden übernommen!");
        }

        // Handle user move from wing to wing or main

        if ($config->getBoolean('allow_wings')) {
            $checked_arr = array();
            $wingAlliances = $allianceRepository->searchAlliances(AllianceSearch::create()->motherId($currentAlliance->id));
            if (count($wingAlliances) > 0) {
                if (isset($_POST['moveuser']) && count($_POST['moveuser']) > 0) {
                    foreach ($_POST['moveuser'] as $wf => $wd)    //wf = source alliance id
                    {                                            //wd = array with alliance members
                        $wf = intval($wf);
                        foreach ($wd as $uk => $wt)                //uk = user id
                        {                                        //wt = value (target alliance id)
                            $uk = intval($uk);
                            $wt = intval($wt);
                            if ($wt != 0) {
                                if ($wf != $wt && ($wf == $currentAlliance->id || isset($wingAlliances[$wf])) && ($wt == $currentAlliance->id || isset($wingAlliances[$wt]))) {
                                    $toBeRemovedUser = $userRepository->getUser($uk);
                                    if ($wf == $currentAlliance->id) {
                                        $allianceService->kickMember($currentAlliance, $toBeRemovedUser);
                                    } else {
                                        $allianceService->kickMember($wingAlliances[$wf], $toBeRemovedUser);;
                                    }

                                    $checked_arr[$uk] = $wt;
                                }
                            }
                        }
                    }
                }
            }
            // Bug-Workaround by river: First kick all, then reassign them to alliances
            // to prevent ressources penalty for temporary +1 user
            // TODO: A cool user-swap-function for alliances would be a better solution.
            if (count($checked_arr) > 0) {
                foreach ($checked_arr as $moving_user_id => $target_alliance) {
                    $toBeAddedUser = $userRepository->getUser($moving_user_id);
                    if ($target_alliance == $currentAlliance->id) {
                        if ($allianceService->addMember($currentAlliance, $toBeAddedUser)) {
                            success_msg($toBeAddedUser->nick . " wurde umgeteilt!");
                        } else {
                            error_msg("Umteilung nicht möglich, User ist bereits Mitglied oder die maximale Anzahl an Mitgliedern wurde erreicht!");
                        }
                    } else {
                        if ($allianceService->addMember($wingAlliances[$target_alliance], $toBeAddedUser)) {
                            success_msg($toBeAddedUser->nick . " wurde verschoben!");
                        } else {
                            error_msg("Verschiebung nicht möglich, User ist bereits Mitglied oder die maximale Anzahl an Mitgliedern wurde erreicht!");
                        }
                    }
                }
            }
        }
    }

    // Gründer wechseln
    if (isset($_GET['setfounder']) && intval($_GET['setfounder']) > 0 && $isFounder && $cu->id != intval($_GET['setfounder'])) {
        $fid = intval($_GET['setfounder']);

        $newFounder = $userRepository->getUser($fid);
        if ($newFounder !== null && $newFounder->allianceId === $currentAlliance->id) {
            $allianceService->changeFounder($currentAlliance, $newFounder);
            $logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Der Spieler [b]" . $newFounder->nick . "[/b] wird vom Spieler [b]" . $cu . "[/b] zum Gründer befördert.");
            success_msg("Gründer ge&auml;ndert!");
        } else
            error_msg("User nicht gefunden!");
    }

    // Mitglied kicken
    if (isset($_GET['kickuser']) && intval($_GET['kickuser']) > 0 && checker_verify() && !$allianceDiplomacyRepository->isAtWar($cu->allianceId())) {
        $kid = intval($_GET['kickuser']);

        $toBeKickedUser = $userRepository->getUser($kid);
        if ($toBeKickedUser !== null && $toBeKickedUser->allianceId === $currentAlliance->id) {
            if ($allianceService->kickMember($currentAlliance, $toBeKickedUser)) {
                $logRepository->add(LogFacility::ALLIANCE, LogSeverity::INFO, "Der Spieler [b]" . $toBeKickedUser->nick . "[/b] wurde von [b]" . $cu . "[/b] aus der Allianz [b]" . $currentAlliance->nameWithTag . "[/b] ausgeschlossen!");
                success_msg("Der Spieler [b]" . $toBeKickedUser->nick . "[/b] wurde aus der Allianz ausgeschlossen!");
                unset($tmpUser);
            } else {
                error_msg("Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er in einem Allianzangriff unterwegs ist!");
            }
        } else {
            error_msg("Der Spieler konnte nicht aus der Allianz ausgeschlossen werden, da er kein Mitglieder dieser Allianz ist!");
        }
    }

    $wings = [];
    if ($config->getBoolean('allow_wings')) {
        $wings = $allianceRepository->searchAlliances(AllianceSearch::create()->motherId($currentAlliance->id));
    }

    checker_init();
    tableStart();
    echo "<tr>
            <th>Nick:</th>
            <th>Punkte:</th>
            <th>Online:</th>
            <th>Rang:</th>";

    if (count($wings) > 0) {
        echo "<th>Umteilen</th>";
    }

    echo "<th>Aktionen</th>
        </tr>";
    $allianceMembers = $allianceRepository->getAllianceMembers($currentAlliance->id);
    foreach ($allianceMembers as $allianceMember) {
        echo "<tr>";
        // Nick, Planet, Punkte
        echo "<td>" . $allianceMember->nick . "</td>
            <td>" . StringUtils::formatNumber($allianceMember->points) . "</td>";
        // Zuletzt online
        if ((time() - $config->getInt('online_threshold') * 60) < $allianceMember->timeAction)
            echo "<td style=\"color:#0f0;\">online</td>";
        else
            echo "<td>" . date("d.m.Y H:i", $allianceMember->timeAction) . "</td>";

        // Rang
        if ($allianceMember->id === $currentAlliance->founderId) {
            echo "<td>Gründer</td>";
        } else {
            echo "<td><select name=\"user_alliance_rank_id[" . $allianceMember->id . "]\">";
            echo "<option value=\"0\">Rang w&auml;hlen...</option>";
            foreach ($rank as $id => $name) {
                echo "<option value=\"$id\"";
                if ($allianceMember->rankId == $id) echo " selected=\"selected\"";
                echo ">" . $name . "</option>";
            }
            echo "</select></td>";
        }

        if (count($wings) > 0) {
            echo "<td>";
            if ($currentAlliance->founderId !== $allianceMember->id && !$allianceDiplomacyRepository->isAtWar($currentAlliance->id)) {
                echo "<select name=\"moveuser[" . $currentAlliance->id . "][" . $allianceMember->id . "]\">
                    <option value=\"\">Keine Änderung</option>";
                foreach ($wings as $wing) {
                    echo "<option value=\"" . $wing->id . "\">Wing " . $wing->nameWithTag . "</option>";
                }
                echo "</select>";
            } elseif ($currentAlliance->founderId === $allianceMember->id) {
                echo "Gründer";
            } else {
                echo "";
            }
            echo "</td>";
        }

        // Aktionen
        echo "<td>";
        if ($cu->id != $allianceMember->id)
            echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=" . $allianceMember->id . "\">Nachricht</a><br/>";
        echo "<a href=\"?page=userinfo&amp;id=" . $allianceMember->id . "\">Profil</a><br/>";
        if ($isFounder && $cu->id != $allianceMember->id)
            echo "<a href=\"?page=alliance&amp;action=editmembers&amp;setfounder=" . $allianceMember->id . "\" onclick=\"return confirm('Soll der Spieler \'" . $allianceMember->nick . "\' wirklich zum Gründer bef&ouml;rdert werden? Dir werden dabei die Gründerrechte entzogen!');\">Gründer</a><br/>";

        if ($cu->id != $allianceMember->id && $allianceMember->id !== $currentAlliance->founderId && !$allianceDiplomacyRepository->isAtWar($cu->allianceId())) {
            echo "<a href=\"?page=$page&amp;action=editmembers&amp;kickuser=" . $allianceMember->id . checker_get_link_key() . "\" onclick=\"return confirm('Soll " . $allianceMember->nick . " wirklich aus der Allianz ausgeschlosen werden?');\">Kicken</a>";
        }
        echo "</td></tr>";
    }
    tableEnd();



    if (count($wings) > 0) {
        foreach ($wings as $wing) {
            tableStart("Mitglieder des Wings " . $wing->nameWithTag);
            echo "<tr>
                    <th>Name:</th>
                    <th>Punkte:</th>
                    <th>Online:</th>
                    <th>Umteilen:</th>
                    <th>Aktionen:</th>
                </tr>";
            $wingMembers = $allianceRepository->getAllianceMembers($wing->id);
            $wingIsAtWar = $allianceDiplomacyRepository->isAtWar($wing->id);
            foreach ($wingMembers as $wingMember) {
                echo "<tr>
                        <td>" . $wingMember->nick . "</td>
                        <td>" . StringUtils::formatNumber($wingMember->points) . "</td>";
                // Zuletzt online
                if ((time() - $config->getInt('online_threshold') * 60) < $wingMember->timeAction)
                    echo "<td style=\"color:#0f0;\">online</td>";
                else
                    echo "<td>" . date("d.m.Y H:i", $wingMember->timeAction) . "</td>";
                echo "<td>";
                if ($wing->founderId !== $wingMember->id && !$wingIsAtWar) {
                    echo "<select name=\"moveuser[" . $wing->id . "][" . $wingMember->id . "]\">
                            <option value=\"\">Keine Änderung</option>
                            <option value=\"" . $currentAlliance->id . "\">Hauptallianz " . $currentAlliance->nameWithTag . "</option>";
                    foreach ($wings as $wingOption) {
                        if ($wingOption->id !== $wing->id)
                            echo "<option value=\"" . $wingOption->id . "\">Wing " . $wingOption->nameWithTag . "</option>";
                    }
                    echo "</select>";
                } elseif ($wing->founderId === $wingMember->id) {
                    echo "Gründer";
                } else {
                    echo "";
                }
                echo "</td><td>
                        <a href=\"?page=messages&amp;mode=new&amp;message_user_to=" . $wingMember->id . "\">Nachricht</a><br/>
                        <a href=\"?page=userinfo&amp;id=" . $wingMember->id . "\">Profil</a></td></tr>";
            }
            tableEnd();
        }
    }


    echo "<br/><br/><input type=\"submit\" name=\"editmemberssubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;
        <input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" /></form>";
}
