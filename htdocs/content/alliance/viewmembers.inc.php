<?PHP

use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceRights;
use EtoA\Support\StringUtils;

/** @var \EtoA\Alliance\Alliance $alliance */
/** @var \EtoA\Alliance\UserAlliancePermission $userAlliancePermission */

if ($userAlliancePermission->checkHasRights(AllianceRights::VIEW_MEMBERS, $page)) {

    /** @var AllianceRankRepository $allianceRankRepository */
    $allianceRankRepository = $app[AllianceRankRepository::class];
    /** @var AllianceRepository $allianceRepository */
    $allianceRepository = $app[AllianceRepository::class];

    echo "<h2>Allianzmitglieder</h2>";
    $rank = [];
    $ranks = $allianceRankRepository->getRanks($cu->allianceId());
    foreach ($ranks as $r) {
        $rank[$r->id] = $r->name;
    }
    echo "<form action=\"?page=$page\" method=\"post\">";
    tableStart();
    echo "<tr>
        <th>Nick</th>
        <th>Heimatplanet</th>
        <th>Punkte</th>
        <th>Rasse</th>
        <th>Rang</th>
        <th>Attack</th>
        <th>Online</th>
        <th>Aktionen</th>
        </tr>";
    $allianceMembers = $allianceRepository->getAllianceMembers($alliance->id);
    $time = time();
    foreach ($allianceMembers as $member) {
        $tp = Planet::getById($member->mainPlanetId);
        echo "<tr>";
        echo "<td>" . $member->nick . "</td>
            <td>" . $tp . "</td>
            <td>" . StringUtils::formatNumber($member->points) . "</td>
            <td>" . $member->raceName . "</td>";
        if ($alliance->founderId === $member->id) {
            echo "<td>Gr&uuml;nder</td>";
        } elseif (isset($rank[$member->rankId])) {
            echo "<td>" . $rank[$member->rankId] . "</td>";
        } else {
            echo "<td>-</td>";
        }
        $num = check_fleet_incomming($member->id);
        if ($num > 0)
            echo "<td BGCOLOR=\"#FF0000\" align=\"center\">" . $num . "</td>";
        else
            echo "<td>-</td>";

        if ($member->timeAction !== null)
            echo "<td style=\"color:#0f0;\">online</td>";
        elseif ($member->lastLog !== null)
            echo "<td>" . date("d.m.Y H:i", $member->lastLog) . "</td>";
        else
            echo "<td>Noch nicht eingeloggt!</td>";

        echo "<td class=\"tbldata\">";
        if ($cu->getId() !== $member->id)
            echo "<a href=\"?page=messages&amp;mode=new&amp;message_user_to=" . $member->id . "\">Nachricht</a> ";

        echo "<a href=\"?page=userinfo&amp;id=" . $member->id . "\">Profil</a>";
        echo "</td></tr>";
    }
    tableEnd();
    echo "<input type=\"button\" onclick=\"document.location='?page=$page';\" value=\"Zur&uuml;ck\" />";
}
