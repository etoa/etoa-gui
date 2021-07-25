<?PHP

/** @var mixed[] $arr alliance data */

use EtoA\Alliance\AlliancePollRepository;

/** @var AlliancePollRepository $alliancePollRepository */
$alliancePollRepository = $app[AlliancePollRepository::class];

echo "<h2>Umfragen</h2>";

if (isset($_POST['vote_submit']) && checker_verify() && isset($_GET['vote']) && intval($_GET['vote']) > 0 && isset($_POST['poll_answer']) && intval($_POST['poll_answer']) > 0) {
    $vid = intval($_GET['vote']);
    $pa = intval($_POST['poll_answer']);
    $updated = $alliancePollRepository->addVote($vid, $cu->allianceId(), $cu->getId(), $pa);
}

$polls = $alliancePollRepository->getPolls($cu->allianceId());
if (count($polls) > 0) {
    define("POLL_BAR_WIDTH", 120);
    $chk = checker_init();
    $votes = $alliancePollRepository->getUserVotes($cu->getId(), $cu->allianceId());

    foreach ($polls as $poll) {
        $answers = [
            1 => $poll->answer1,
            2 => $poll->answer2,
            3 => $poll->answer3,
            4 => $poll->answer4,
            5 => $poll->answer5,
            6 => $poll->answer6,
            7 => $poll->answer7,
            8 => $poll->answer8,
        ];

        if (isset($votes[$poll->id]) || !$poll->active) {
            tableStart(stripslashes($poll->title));
            echo "<tr><th colspan=\"2\">" . stripslashes($poll->question) . "</th></tr>";
            $num_votes = $poll->answer1Count + $poll->answer2Count + $poll->answer3Count + $poll->answer4Count + $poll->answer5Count + $poll->answer6Count + $poll->answer7Count + $poll->answer8Count;

            $answerCounts = [
                1 => $poll->answer1Count,
                2 => $poll->answer2Count,
                3 => $poll->answer3Count,
                4 => $poll->answer4Count,
                5 => $poll->answer5Count,
                6 => $poll->answer6Count,
                7 => $poll->answer7Count,
                8 => $poll->answer8Count,
            ];
            for ($x = 1; $x <= 8; $x++) {
                if ($answers[$x] != "") {
                    echo "<tr><td>" . stripslashes($answers[$x]) . "</td>";
                    if ($answerCounts[$x] > 0) {
                        $p = 100 / $num_votes * $answerCounts[$x];
                        $iw = (POLL_BAR_WIDTH / $num_votes * $answerCounts[$x]) + 1;
                    } else {
                        $p = 0;
                        $iw = 1;
                    }
                    $iiw = POLL_BAR_WIDTH - $iw;
                    $img = "poll" . $x;
                    echo "<td style=\"width:250px;\"><img src=\"images/" . $img . ".jpg\" width=\"$iw\" height=\"10\" alt=\"Poll\" /><img src=\"images/blank.gif\" width=\"$iiw\" height=\"10\"> " . round($p, 2) . " % (" . $answerCounts[$x] . " Stimmen)</td></tr>";
                }
            }
            tableEnd();
        } else {
            echo "<form action=\"?page=$page&amp;action=" . $_GET['action'] . "&amp;vote=" . $poll->id . "\" method=\"post\">";
            echo $chk;
            tableStart(stripslashes($poll->title));
            echo "<tr><th colspan=\"2\" class=\"tbltitle\">" . stripslashes($poll->question) . "</th></tr>";
            for ($x = 1; $x <= 8; $x++) {
                if ($answers[$x] != "")
                    echo "<tr><td class=\"tbldata\"><input type=\"radio\" name=\"poll_answer\" value=\"$x\" /> " . stripslashes($answers[$x]) . "</td>";
            }
            tableEnd();
            echo "<input type=\"submit\" value=\"Abstimmen!\" name=\"vote_submit\"></form><br/><br/>";
        }
    }
} else
    error_msg("Keine Umfragen vorhanden");
echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
