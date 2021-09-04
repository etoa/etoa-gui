<?PHP

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRight;
use EtoA\Alliance\AllianceRights;
use EtoA\User\UserRepository;

/** @var array<int, AllianceRight> $rights */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

if ($userAlliancePermission->checkHasRights(AllianceRights::RANKS, $page)) {
    /** @var AllianceRankRepository $allianceRankRepository */
    $allianceRankRepository = $app[AllianceRankRepository::class];

    echo "<h2>R&auml;nge</h2>";

    // Ränge speichern
    if (count($_POST) > 0 && checker_verify()) {
        if (isset($_POST['ranknew'])) {
            $allianceRankRepository->add($cu->allianceId());
        }
        if (isset($_POST['ranksubmit']) || isset($_POST['ranknew'])) {
            if (isset($_POST['rank_name']) && count($_POST['rank_name']) > 0) {
                foreach ($_POST['rank_name'] as $id => $name) {
                    $id = intval($id);
                    $allianceRankRepository->deleteRights($id);
                    if (isset($_POST['rank_del'][$id]) && $_POST['rank_del'][$id] == 1) {
                        $allianceRankRepository->removeRank($id);
                        $userRepository->setAllianceId($id, $cu->allianceId(), 0);
                    } else {
                        $allianceRankRepository->updateRank($id, $name, $_POST['rank_level'][$id]);
                        if (isset($_POST['rankright']) && isset($_POST['rankright'][$id])) {
                            foreach ($_POST['rankright'][$id] as $rid => $rv) {
                                $rid = intval($rid);
                                $allianceRankRepository->addRankRight($id, $rid);
                            }
                        }
                    }
                }
            }
            success_msg("Änderungen wurden übernommen!");
        }
    }
    echo "<form action=\"?page=$page&action=ranks\" method=\"post\">";
    checker_init();

    $ranks = $allianceRankRepository->getRanks($cu->allianceId());
    if (count($ranks) > 0) {
        tableStart("Verf&uuml;gbare R&auml;nge");
        echo "<tr>
                                <th>Rangname:</th>
                                <th>Rechte:</th>
                                <th>L&ouml;schen:</th>
                            </tr>";
        foreach ($ranks as $rank) {
            $rightIds = $allianceRankRepository->getRightIds($rank->id);

            echo "<tr>
                                    <td>
                                        <input type=\"text\" name=\"rank_name[" . $rank->id . "]\" value=\"" . $rank->name . "\" /><br/>
                                        Level: <input type=\"text\" name=\"rank_level[" . $rank->id . "]\" value=\"" . $rank->level . "\" maxlength=\"1\" size=\"2\" />
                                    </td>
                                    <td>";
            foreach ($rights as $right) {
                echo "<input type=\"checkbox\" name=\"rankright[" . $rank->id . "][" . $right->id . "]\" value=\"1\" ";
                if (in_array($right->id, $rightIds, true))
                    echo " checked=\"checked\" /><span style=\"color:#0f0;\">" . $right->description . "</span><br/>";
                else
                    echo " /> <span style=\"color:#f50;\">" . $right->description . "</span><br/>";
            }
            echo "</td>";

            echo "<td><input type=\"checkbox\" name=\"rank_del[" . $rank->id . "]\" value=\"1\" /></td></tr>";
        }
        tableEnd();
        echo "<input type=\"submit\" name=\"ranksubmit\" value=\"&Uuml;bernehmen\" />&nbsp;&nbsp;&nbsp;";
    } else {
        error_msg("Keine R&auml;nge vorhanden!");
    }
    echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />&nbsp;&nbsp;&nbsp;
                        <input type=\"submit\" name=\"ranknew\" value=\"Neuer Rang\" /></form>";
}
