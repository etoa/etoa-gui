<?PHP

use EtoA\Alliance\AllianceRankRepository;

$xajax->register(XAJAX_FUNCTION, "allianceRankSelector");

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
