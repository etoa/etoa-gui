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

if ($request->request->has('member_save') && $request->request->get('member_save') != "") {
    saveMembers($request, $repository, $allianceRankRepository);
} elseif ($request->request->has('bnd_save') && $request->request->get('bnd_save') != "") {
    saveDiplomacy($request, $allianceDiplomacyRepository);
} elseif ($request->request->has('res_save') && $request->request->get('res_save') != "") {
    saveResources($request, $repository, $id);
}
edit($repository, $buildingRepository, $technologyRepository, $historyRepository, $allianceDiplomacyRepository, $id);

function saveMembers(Request $request, AllianceRepository $repository, AllianceRankRepository $allianceRankRepository)
{
    // Mitgliederänderungen
    if ($request->request->has('member_kick') && count($request->request->all('member_kick')) > 0) {
        foreach (array_keys($request->request->all('member_kick')) as $userId) {
            $repository->removeUser($userId);
        }
    }
    if (count($request->request->all('member_rank')) > 0) {
        foreach ($request->request->all('member_rank') as $userId => $rankId) {
            $repository->assignRankToUser((int) $rankId, (int) $userId);
        }
    }
    // Ränge speichern
    if ($request->request->has('rank_del') && count($request->request->all('rank_del')) > 0) {
        foreach (array_keys($request->request->all('rank_del')) as $rankId) {
            $allianceRankRepository->removeRank($rankId);
        }
    }
    if ($request->request->has('rank_name') && count($request->request->all('rank_name')) > 0) {
        foreach ($request->request->all('rank_name') as $rankId => $name) {
            $allianceRankRepository->updateRank($rankId, $name, $request->request->all('rank_level')[$rankId]);
        }
    }

    \EtoA\Admin\LegacyTemplateTitleHelper::addFlash('success', 'Mitglieder aktualisiert!');
}

function saveDiplomacy(Request $request, AllianceDiplomacyRepository $repository)
{
    // Bündnisse / Kriege speichern
    if ($request->request->has('alliance_bnd_del') && count($request->request->all('alliance_bnd_del')) > 0) {
        foreach (array_keys($request->request->all('alliance_bnd_del')) as $diplomacyId) {
            $repository->deleteDiplomacy($diplomacyId);
        }
    }
    if (count($request->request->all('alliance_bnd_level')) > 0) {
        foreach (array_keys($request->request->all('alliance_bnd_level')) as $diplomacyId) {
            $repository->updateDiplomacy(
                $diplomacyId,
                $request->request->all('alliance_bnd_level')[$diplomacyId],
                $request->request->all('alliance_bnd_name')[$diplomacyId]
            );
        }
    }

    \EtoA\Admin\LegacyTemplateTitleHelper::addFlash('success', 'Diplomatie aktualisiert!');
}

function saveResources(Request $request, AllianceRepository $repository, int $id)
{
    $repository->updateResources(
        $id,
        StringUtils::parseFormattedNumber($request->request->get('res_metal')),
        StringUtils::parseFormattedNumber($request->request->get('res_crystal')),
        StringUtils::parseFormattedNumber($request->request->get('res_plastic')),
        StringUtils::parseFormattedNumber($request->request->get('res_fuel')),
        StringUtils::parseFormattedNumber($request->request->get('res_food')),
    );

    $repository->addResources(
        $id,
        StringUtils::parseFormattedNumber($request->request->get('res_metal_add')),
        StringUtils::parseFormattedNumber($request->request->get('res_crystal_add')),
        StringUtils::parseFormattedNumber($request->request->get('res_plastic_add')),
        StringUtils::parseFormattedNumber($request->request->get('res_fuel_add')),
        StringUtils::parseFormattedNumber($request->request->get('res_food_add')),
    );

    \EtoA\Admin\LegacyTemplateTitleHelper::addFlash('success', 'Ressourcen aktualisiert!');
}

function edit(
    AllianceRepository $repository,
    AllianceBuildingRepository $buildingRepository,
    AllianceTechnologyRepository $technologyRepository,
    AllianceHistoryRepository $historyRepository,
    AllianceDiplomacyRepository $allianceDiplomacyRepository,
    int $id
): void {
    global $page, $app;

    /** @var AllianceRankRepository $allianceRankRepository */
    $allianceRankRepository = $app[AllianceRankRepository::class];

    $alliance = $repository->getAlliance($id);

    if ($alliance === null) {
        echo 'Alliance does not exist.';
        return;
    }

    \EtoA\Admin\LegacyTemplateTitleHelper::$subTitle = "Allianz bearbeiten: " . $alliance->nameWithTag;

    $members = $repository->findUsers($id);

    $ranks = $allianceRankRepository->getRanks($id);

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
	</ul>
	<div id="tabs-1">';

    echo '</div><div id="tabs-2">';

    membersTab($members, $ranks);

    echo '</div><div id="tabs-3">';

    diplomacyTab($allianceDiplomacyRepository, $id);

    echo '</div><div id="tabs-4">';

    echo '</div><div id="tabs-5">';

    resourcesTab($alliance);

    echo '</div><div id="tabs-6">';

    depositsTab($alliance, $members);

    echo '
		</div>
	</div>';
}

/**
 * @param \EtoA\Alliance\AllianceRank[] $ranks
 */
function membersTab(array $members, array $ranks): void
{
    tableStart();
    echo "<tr>
			<th>Mitglieder</th>
		<td>";
    if (count($members) > 0) {
        echo "<table class=\"tb\">
			<tr>
				<th>Name</th>
				<th>Punkte</th>
				<th>Rang</th>
				<th>Mitgliedschaft beenden</th></tr>";
        foreach ($members as $member) {
            echo "<tr><td id=\"uifo" . $member['user_id'] . "\" style=\"display:none;\"><a href=\"?page=user&amp;sub=edit&amp;id=" . $member['user_id'] . "\">Daten</a><br/>
                <a href=\"?page=sendmessage&amp;id=" . $member['user_id'] . "\">Nachricht senden</a></td>
				<td><a href=\"?page=user&amp;sub=edit&amp;id=" . $member['user_id'] . "\" " . cTT($member['user_nick'], "uifo" . $member['user_id'] . "") . ">" . $member['user_nick'] . "</a></td>
				<td>" . StringUtils::formatNumber($member['user_points']) . " Punkte</td>
				<td><select name=\"member_rank[" . $member['user_id'] . "]\"><option value=\"0\">-</option>";
            foreach ($ranks as $rank) {
                echo "<option value=\"" . $rank->id . "\"";
                if ($member['user_alliance_rank_id'] == $rank->id) {
                    echo " selected=\"selected\"";
                }
                echo ">" . $rank->name . "</option>";
            }
            echo "</select></td>";
            echo "<td><input type=\"checkbox\" name=\"member_kick[" . $member['user_id'] . "]\" value=\"1\" /></td></tr>";
        }
        echo "</table>";
    } else
        echo "<b>KEINE MITGLIEDER!</b>";
    echo "</td></tr>";
    echo "<tr><th>R&auml;nge</th><td>";

    if (count($ranks) > 0) {
        echo "<table class=\"tb\">";
        echo "<tr><th>Name</th><th>Level</th><th>Löschen</th></tr>";
        foreach ($ranks as $rank) {
            echo "<tr><td><input type=\"text\" size=\"35\" name=\"rank_name[" . $rank->id . "]\" value=\"" . $rank->name . "\" /></td>";
            echo "<td><select name=\"rank_level[" . $rank->id . "]\">";
            for ($x = 0; $x <= 9; $x++) {
                echo "<option value=\"$x\"";
                if ($rank->level == $x) {
                    echo " selected=\"selected\"";
                }
                echo ">$x</option>";
            }
            echo "</select></td>";
            echo "<td><input type=\"checkbox\" name=\"rank_del[" . $rank->id . "]\" value=\"1\" /></td></tr>";
        }
        echo "</table>";
    } else
        echo "<b>Keine R&auml;nge vorhanden!</b>";
    echo "</td></tr>";
    tableEnd();
    echo "<p><input type=\"submit\" name=\"member_save\" value=\"Übernehmen\" /></p>";
}

function diplomacyTab(AllianceDiplomacyRepository $repository, int $id): void
{
    $diplomacies = $repository->getDiplomacies($id);
    if (count($diplomacies) > 0) {
        echo "<table class=\"tb\">";
        echo "<tr>
			<th>Allianz</th>
			<th>Bezeichnung</th>
			<th>Status / Datum</th>
			<th>Löschen</th></tr>";
        foreach ($diplomacies as $diplomacy) {
            echo "<tr>
					<td><a href=\"?page=alliances&amp;action=edit&amp;id=" . $diplomacy->otherAllianceId . "\">" . $diplomacy->otherAllianceName . "</a></td>
					<td><input type=\"text\" value=\"" . $diplomacy->name . "\" name=\"alliance_bnd_name[" . $diplomacy->id . "]\" /></td>";
            echo "<td>
				<select name=\"alliance_bnd_level[" . $diplomacy->id . "]\">";
            echo "<option value=\"0\">Bündnisanfrage</option>";
            echo "<option value=\"2\"";
            if ($diplomacy->level === AllianceDiplomacyLevel::BND_CONFIRMED) echo " selected=\"selected\"";
            echo ">Bündnis</option>";
            echo "<option value=\"3\"";
            if ($diplomacy->level === AllianceDiplomacyLevel::WAR) echo " selected=\"selected\"";
            echo ">Krieg</option>";
            echo "<option value=\"3\"";
            if ($diplomacy->level === AllianceDiplomacyLevel::PEACE) echo " selected=\"selected\"";
            echo ">Frieden</option>";
            echo "</select>";
            echo " &nbsp; " . StringUtils::formatDate($diplomacy->date) . "</td>";
            echo "<td valign=\"top\"><input type=\"checkbox\" name=\"alliance_bnd_del[" . $diplomacy->id . "]\" value=\"1\" /></td></tr>";
        }
        echo "</table>";
        echo "<p><input type=\"submit\" name=\"bnd_save\" value=\"Übernehmen\" /></p>";
    } else {
        echo "<p><b>Keine Bündnisse/Kriege vorhanden!</b></p>";
    }
}

function resourcesTab(\EtoA\Alliance\Alliance $alliance): void
{
    echo '<table class="tb">';
    echo "<tr>
			<th class=\"resmetalcolor\">Titan</th>
			<td>
				<input type=\"text\" name=\"res_metal\" id=\"res_metal\" value=\"" . StringUtils::formatNumber($alliance->resMetal) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_metal_add\" id=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
    echo "<th class=\"rescrystalcolor\">Silizium</th>
			<td><input type=\"text\" name=\"res_crystal\" id=\"res_crystal\" value=\"" . StringUtils::formatNumber($alliance->resCrystal) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_crystal_add\" id=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
    echo "<tr><th class=\"resplasticcolor\">PVC</th>
			<td><input type=\"text\" name=\"res_plastic\" id=\"res_plastic\" value=\"" . StringUtils::formatNumber($alliance->resPlastic) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_plastic_add\" id=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
    echo "<th class=\"resfuelcolor\">Tritium</th>
			<td><input type=\"text\" name=\"res_fuel\" id=\"res_fuel\" value=\"" . StringUtils::formatNumber($alliance->resFuel) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_fuel_add\" id=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
    echo "<tr><th class=\"resfoodcolor\">Nahrung</th>
			<td><input type=\"text\" name=\"res_food\" id=\"res_food\" value=\"" . StringUtils::formatNumber($alliance->resFood) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_food_add\" id=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td><td colspan=\"2\">";
    tableEnd();
    echo "<p><input type=\"submit\" name=\"res_save\" value=\"Übernehmen\" /></p>";
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
