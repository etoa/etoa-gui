<?PHP

use EtoA\Alliance\Alliance;
use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceDiplomacyLevel;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceHistoryRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var AllianceRepository $repository */
$repository = $app[AllianceRepository::class];
/** @var AllianceRankRepository $allianceRankRepository */
$allianceRankRepository = $app[AllianceRankRepository::class];
/** @var AllianceHistoryRepository $historyRepository */
$historyRepository = $app[AllianceHistoryRepository::class];

/** @var AllianceBuildingRepository $buildingRepository */
$buildingRepository = $app[AllianceBuildingRepository::class];

/** @var AllianceTechnologyRepository $technologyRepository */
$technologyRepository = $app[AllianceTechnologyRepository::class];
/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($request->query->has('alliance_id')) {
    $id = $request->query->getInt('alliance_id');
}
if ($request->query->has('id')) {
    $id = $request->query->getInt('id');
}
if (!isset($id)) {
    echo "Invalid request.";
    return;
}

edit($repository, $id);


function edit(
    AllianceRepository $repository,
    int $id
): void {
    global $page;

    $alliance = $repository->getAlliance($id);

    $members = $repository->findUsers($id);


    echo "<form action=\"?page=$page&amp;sub=edit&amp;id=" . $id . "\" method=\"post\">";

    echo '<div class="tabs">
	<ul>
		<li><a href="#tabs-1">Info</a></li>
		<li><a href="#tabs-2">Mitglieder</a></li>
		<li><a href="#tabs-3">Diplomatie</a></li>
		<li><a href="#tabs-4">Geschichte</a></li>
		<li><a href="#tabs-5">Rohstoffe</a></li>
		<li><a href="#tabs-6">Einzahlungen</a></li>
		<li><a href="#tabs-7">Gebäude</a></li>
		<li><a href="#tabs-8">Technologien</a></li>
	</ul>';
    echo '</div><div id="tabs-6">';

    depositsTab($alliance, $members);

    echo '
		</div>
	</div>';
}

function depositsTab(\EtoA\Alliance\Alliance $alliance, array $members): void
{
    echo "<form id=\"filterForm\">";
    tableStart("Filter");
    echo "<tr>
		<th>Ausgabe:</th>
		<td>
			<input type=\"radio\" name=\"output\" id=\"output\" value=\"0\" checked=\"checked\"/> Einzeln / <input type=\"radio\" name=\"output\" id=\"output\" value=\"1\"/> Summiert
		</td>
	</tr><tr>
		<th>Einzahlungen:</th>
		<td>
			<select id=\"limit\" name=\"limit\">
				<option value=\"0\" checked=\"checked\">alle</option>
				<option value=\"1\">die letzte</option>
				<option value=\"5\">die letzten 5</option>
				<option value=\"20\">die letzten 20</option>
			</select>
		</td>
	</tr><tr>
		<th>Von User:</th>
		<td>
			<select id=\"user_spends\" name=\"user_spends\">
				<option value=\"0\">alle</option>";
    // Allianzuser
    foreach ($members as $member) {
        echo "<option value=\"" . $member['user_id'] . "\">" . $member['user_nick'] . "</option>";
    }
    echo         "</select>
		</td>
	</tr><tr>";
    tableEnd();
    echo "<p><input type=\"button\" onclick=\"xajax_showSpend(" . $alliance->id . ",xajax.getFormValues('filterForm'))\" value=\"Anzeigen\"\"/></p>";
    echo "</form>";

    echo "<div id=\"spends\">&nbsp;</div>";
}
