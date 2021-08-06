<?PHP

use EtoA\User\UserStatRepository;
use EtoA\User\UserStatSearch;

$xajax->register(XAJAX_FUNCTION, 'showAllianceMembers');
$xajax->register(XAJAX_FUNCTION, 'showAllianceMemberAddCosts');

//Listet User einer Allianz auf
function showAllianceMembers($alliance_id = 0, $field_id)
{
    global $app;

    /** @var UserStatRepository $userStatRepository */
    $userStatRepository = $app[UserStatRepository::class];

    ob_start();
    $objResponse = new xajaxResponse();

    $out = '';
    if ($alliance_id != 0) {
        $members = "";
        $cnt = 0;
        $out = "Allianz-ID nicht angegeben!";
        $search = UserStatSearch::points()->allianceId($alliance_id);
        $entries = $userStatRepository->searchStats($search);
        if (count($entries) > 0) {
            foreach ($entries as $entry) {
                $cnt++;

                if ($entry->shift === 2) {
                    $rank =  "<img src=\"images/stats/stat_up.gif\" alt=\"up\" width=\"9\" height=\"12\" />";
                } elseif ($entry->shift === 1) {
                    $rank =  "<img src=\"images/stats/stat_down.gif\" alt=\"down\" width=\"9\" height=\"11\" />";
                } else {
                    $rank =  "<img src=\"images/stats/stat_same.gif\" alt=\"same\" width=\"21\" height=\"9\" />";
                }

                $members .= "
                <tr>
                    <td>
                        " . $entry->rank . "
                    </td>
                    <td>
                        " . $rank . "
                    </td>
                    <td>
                        <a href=\"?page=userinfo&id=" . $entry->id . "\">" . $entry->nick . "</a>
                    </td>
                    <td>
                        " . nf($entry->points) . "
                    </td>
                    <td>
                        " . nf($entry->buildingPoints) . "
                    </td>
                    <td>
                        " . nf($entry->shipPoints) . "
                    </td>
                    <td>
                        " . nf($entry->techPoints) . "
                    </td>
                    <td>
                        " . nf($entry->expPoints) . "
                    </td>
                </tr>";
            }
            $out = "<table class=\"tbl\">
                            <tr>
                                <th width=\"5%\" colspan=\"2\">Rang</th>
                                <th width=\"15%\">User</th>
                                <th>Punkte</th>
                                <th>Gebäude</th>
                                <th>Flotten</th>
                                <th>Tech</th>
                                <th>XP</th>
                            </tr>
                            " . $members . "
                            </table>";
        }
    }


    $objResponse->assign($field_id, "innerHTML", $out);


    $objResponse->assign("allianceinfo", "innerHTML", ob_get_contents());
    ob_end_clean();

    return $objResponse;
}

function showAllianceMemberAddCosts($allianceId = 0, $form)
{
    ob_start();
    $objResponse = new xajaxResponse();
    $cnt = 0;

    foreach ($form['application_answer'] as $answear) {
        if ($answear == 2) $cnt++;
    }
    if ($allianceId != 0) {
        $alliance = new Alliance($allianceId);

        echo $alliance->calcMemberCosts(false, $cnt);
    }

    $objResponse->assign("memberCostsTD", "innerHTML", ob_get_contents());
    if ($cnt > 0) {
        $objResponse->assign("memberCosts", "style.display", '');
    } else {
        $objResponse->assign("memberCosts", "style.display", 'none');
    }
    ob_end_clean();

    return $objResponse;
}
