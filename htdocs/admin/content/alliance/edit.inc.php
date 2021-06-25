<?PHP

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyRepository;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var AllianceRepository */
$repository = $app['etoa.alliance.repository'];

/** @var AllianceBuildingRepository */
$buildingRepository = $app['etoa.alliance.building.repository'];

/** @var AllianceTechnologyRepository */
$technologyRepository = $app['etoa.alliance.technology.repository'];

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

if ($request->request->has('info_save') && $request->request->get('info_save') != "") {
	saveInfo($request, $repository, $id, $twig);
} elseif ($request->request->has('member_save') && $request->request->get('member_save') != "") {
	saveMembers($request, $repository, $twig);
} elseif ($request->request->has('bnd_save') && $request->request->get('bnd_save') != "") {
	saveDiplomacy($request, $repository, $twig);
} elseif ($request->request->has('res_save') && $request->request->get('res_save') != "") {
	saveResources($request, $repository, $id, $twig);
} elseif ($request->request->has('buildings') && $request->request->get('buildings') != "") {
	saveBuildings($request, $buildingRepository, $id, $twig);
} elseif ($request->request->has('techs') && $request->request->get('techs') != "") {
	saveTechnologies($request, $technologyRepository, $id, $twig);
}
edit($repository, $buildingRepository, $technologyRepository, $id, $twig);

function saveInfo(Request $request, AllianceRepository $repository, int $id, Environment $twig)
{
	//  Bild löschen wenn nötig
	if ($request->request->has('alliance_img_del')) {
		$picture = $repository->getPicture($id);
		if ($picture !== null) {
			if (file_exists('../' . ALLIANCE_IMG_DIR . "/" . $picture)) {
				unlink('../' . ALLIANCE_IMG_DIR . "/" . $picture);
			}
			$repository->clearPicture($id);
		}
	}

	$repository->update(
		$id,
		$request->request->get('alliance_tag'),
		$request->request->get('alliance_name'),
		$request->request->get('alliance_text'),
		$request->request->get('alliance_application_template'),
		$request->request->get('alliance_url'),
		$request->request->getInt('alliance_founder_id')
	);

	$twig->addGlobal('successMessage', 'Allianzdaten aktualisiert!');
}

function saveMembers(Request $request, AllianceRepository $repository, Environment $twig)
{
	// Mitgliederänderungen
	if ($request->request->has('member_kick') && count($request->request->get('member_kick')) > 0) {
		foreach (array_keys($request->request->get('member_kick')) as $userId) {
			$repository->removeUser($userId);
		}
	}
	if (count($request->request->get('member_rank')) > 0) {
		foreach ($request->request->get('member_rank') as $userId => $rankId) {
			$repository->assignRankToUser($rankId, $userId);
		}
	}
	// Ränge speichern
	if ($request->request->has('rank_del') && count($request->request->get('rank_del')) > 0) {
		foreach (array_keys($request->request->get('rank_del')) as $rankId) {
			$repository->removeRank($rankId);
		}
	}
	if ($request->request->has('rank_name') && count($request->request->get('rank_name')) > 0) {
		foreach ($request->request->get('rank_name') as $rankId => $name) {
			$repository->updateRank($rankId, $name, $request->request->get('rank_level')[$rankId]);
		}
	}

	$twig->addGlobal('successMessage', 'Mitglieder aktualisiert!');
}

function saveDiplomacy(Request $request, AllianceRepository $repository, Environment $twig)
{
	// Bündnisse / Kriege speichern
	if ($request->request->has('alliance_bnd_del') && count($request->request->get('alliance_bnd_del')) > 0) {
		foreach (array_keys($request->request->get('alliance_bnd_del')) as $diplomacyId) {
			$repository->deleteDiplomacy($diplomacyId);
		}
	}
	if (count($request->request->get('alliance_bnd_level')) > 0) {
		foreach (array_keys($request->request->get('alliance_bnd_level')) as $diplomacyId) {
			$repository->updateDiplomacy(
				$diplomacyId,
				$request->request->get('alliance_bnd_level')[$diplomacyId],
				$request->request->get('alliance_bnd_name')[$diplomacyId]
			);
		}
	}
	$twig->addGlobal('successMessage', 'Diplomatie aktualisiert!');
}

function saveResources(Request $request, AllianceRepository $repository, int $id, Environment $twig)
{
	$repository->updateResources(
		$id,
		nf_back($request->request->get('res_metal')),
		nf_back($request->request->get('res_crystal')),
		nf_back($request->request->get('res_plastic')),
		nf_back($request->request->get('res_fuel')),
		nf_back($request->request->get('res_food')),
	);

	$repository->addResources(
		$id,
		nf_back($request->request->get('res_metal_add')),
		nf_back($request->request->get('res_crystal_add')),
		nf_back($request->request->get('res_plastic_add')),
		nf_back($request->request->get('res_fuel_add')),
		nf_back($request->request->get('res_food_add')),
	);

	$twig->addGlobal('successMessage', 'Ressourcen aktualisiert!');
}

function saveBuildings(
	Request $request,
	AllianceBuildingRepository $buildingRepository,
	int $id,
	Environment $twig
) {
	if ($buildingRepository->existsInAlliance($id, $request->request->get('alliance_building_id'))) {
		$buildingRepository->updateForAlliance(
			$id,
			$request->request->getInt('alliance_building_id'),
			$request->request->getInt('level'),
			$request->request->getInt('amount')
		);
		$twig->addGlobal('successMessage', 'Gebäudedatensatz erfolgreich bearbeitet!');
	} else {
		$buildingRepository->addToAlliance(
			$id,
			$request->request->getInt('alliance_building_id'),
			$request->request->getInt('level'),
			$request->request->getInt('amount')
		);
		$twig->addGlobal('successMessage', 'Gebäudedatensatz erfolgreich eingefügt!');
	}
}

function saveTechnologies(
	Request $request,
	AllianceTechnologyRepository $technologyRepository,
	int $id,
	Environment $twig
): void {
	if ($technologyRepository->existsInAlliance($id, $request->request->getInt('alliance_tech_id'))) {
		$technologyRepository->updateForAlliance(
			$id,
			$request->request->getInt('alliance_tech_id'),
			$request->request->getInt('tech_level'),
			$request->request->getInt('tech_amount')
		);
		$twig->addGlobal('successMessage', 'Technologiedatensatz erfolgreich bearbeitet!');
	} else {
		$technologyRepository->addToAlliance(
			$id,
			$request->request->getInt('alliance_tech_id'),
			$request->request->getInt('tech_level'),
			$request->request->getInt('tech_amount')
		);
		$twig->addGlobal('successMessage', 'Technologiedatensatz erfolgreich eingefügt!');
	}
}

function edit(
	AllianceRepository $repository,
	AllianceBuildingRepository $buildingRepository,
	AllianceTechnologyRepository $technologyRepository,
	int $id,
	Environment $twig
): void {
	global $page;

	$alliance = $repository->getAlliance($id);

	if ($alliance === null) {
		echo 'Alliance does not exist.';
		return;
	}

	$twig->addGlobal('subtitle', "Allianz bearbeiten: [" . $alliance->tag . "] " . $alliance->name);

	$members = $repository->findUsers($id);

	$ranks = $repository->findRanks($id);

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

	infoTab($alliance, $members);

	echo '</div><div id="tabs-2">';

	membersTab($members, $ranks);

	echo '</div><div id="tabs-3">';

	diplomacyTab($repository, $id);

	echo '</div><div id="tabs-4">';

	historyTab($repository, $id);

	echo '</div><div id="tabs-5">';

	resourcesTab($alliance);

	echo '</div><div id="tabs-6">';

	depositsTab($alliance, $members);

	echo '</div><div id="tabs-7">';

	buildingsTab($repository, $buildingRepository, $id);

	echo '</div><div id="tabs-8">';

	technologiesTab($repository, $technologyRepository, $id);

	echo '<br><input type="submit" name="techs">';

	echo '
		</div>
	</div>';
}

function infoTab(\EtoA\Alliance\Alliance $alliance, array $members): void
{
	tableStart();
	echo "<tr><th>ID</th><td>" . $alliance->id . "</td></tr>";
	echo "<tr><th>[Tag] Name</th><td>
			[<input type=\"text\" name=\"alliance_tag\" value=\"" . $alliance->tag . "\" size=\"6\" maxlength=\"6\" required />]
			<input type=\"text\" name=\"alliance_name\" value=\"" . $alliance->name . "\" size=\"30\" maxlength=\"25\" required />
		</td></tr>";
	echo "<tr><th>Gründer</th><td><select name=\"alliance_founder_id\">";
	echo "<option value=\"0\">(niemand)</option>";
	foreach ($members as $member) {
		echo "<option value=\"" . $member['user_id'] . "\"";
		if ($alliance->founderId == $member['user_id']) {
			echo " selected=\"selected\"";
		}
		echo ">" . $member['user_nick'] . "</option>";
	}
	echo "</select></td></tr>";
	echo "<tr><th>Text</th><td><textarea cols=\"45\" rows=\"10\" name=\"alliance_text\">" . stripslashes($alliance->text) . "</textarea></td></tr>";
	echo "<tr><th>Gründung</th><td>" . date("Y-m-d H:i:s", $alliance->foundationTimestamp) . "</td></tr>";
	echo "<tr><th>Website</th><td><input type=\"text\" name=\"alliance_url\" value=\"" . $alliance->url . "\" size=\"40\" maxlength=\"250\" /></td></tr>";
	echo "<tr><th>Bewerbungsvorlage</th><td><textarea cols=\"45\" rows=\"10\" name=\"alliance_application_template\">" . stripslashes($alliance->applicationTemplate) . "</textarea></td></tr>";
	echo "<tr><th>Bild</th><td>";
	if ($alliance->image != "") {
		echo '<img src="' . ALLIANCE_IMG_DIR . '/' . $alliance->image . '" alt="Profil" /><br/>';
		echo "<input type=\"checkbox\" value=\"1\" name=\"alliance_img_del\"> Bild löschen<br/>";
	} else {
		echo "Keines";
	}
	echo "</td></tr>";
	echo "</table>";
	echo "<p><input type=\"submit\" name=\"info_save\" value=\"Übernehmen\" /></p>";
}

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
				" . popupLink("sendmessage", "Nachricht senden", "", "id=" . $member['user_id']) . "</td>
				<td><a href=\"?page=user&amp;sub=edit&amp;id=" . $member['user_id'] . "\" " . cTT($member['user_nick'], "uifo" . $member['user_id'] . "") . ">" . $member['user_nick'] . "</a></td>
				<td>" . nf($member['user_points']) . " Punkte</td>
				<td><select name=\"member_rank[" . $member['user_id'] . "]\"><option value=\"0\">-</option>";
			foreach ($ranks as $rank) {
				echo "<option value=\"" . $rank['rank_id'] . "\"";
				if ($member['user_alliance_rank_id'] == $rank['rank_id']) {
					echo " selected=\"selected\"";
				}
				echo ">" . $rank['rank_name'] . "</option>";
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
			echo "<tr><td><input type=\"text\" size=\"35\" name=\"rank_name[" . $rank['rank_id'] . "]\" value=\"" . $rank['rank_name'] . "\" /></td>";
			echo "<td><select name=\"rank_level[" . $rank['rank_id'] . "]\">";
			for ($x = 0; $x <= 9; $x++) {
				echo "<option value=\"$x\"";
				if ($rank['rank_level'] == $x) {
					echo " selected=\"selected\"";
				}
				echo ">$x</option>";
			}
			echo "</select></td>";
			echo "<td><input type=\"checkbox\" name=\"rank_del[" . $rank['rank_id'] . "]\" value=\"1\" /></td></tr>";
		}
		echo "</table>";
	} else
		echo "<b>Keine R&auml;nge vorhanden!</b>";
	echo "</td></tr>";
	tableEnd();
	echo "<p><input type=\"submit\" name=\"member_save\" value=\"Übernehmen\" /></p>";
}

function diplomacyTab(AllianceRepository $repository, int $id): void
{
	$diplomacies = $repository->findDiplomacies($id);
	if (count($diplomacies) > 0) {
		echo "<table class=\"tb\">";
		echo "<tr>
			<th>Allianz</th>
			<th>Bezeichnung</th>
			<th>Status / Datum</th>
			<th>Löschen</th></tr>";
		foreach ($diplomacies as $barr) {
			$opId = ($id == $barr['a2id']) ? $barr['a1id'] : $barr['a2id'];
			$opName = ($id == $barr['a2id']) ? $barr['a1name'] : $barr['a2name'];
			echo "<tr>
					<td><a href=\"?page=alliances&amp;action=edit&amp;id=" . $opId . "\">" . $opName . "</a></td>
					<td><input type=\"text\" value=\"" . $barr['name'] . "\" name=\"alliance_bnd_name[" . $barr['alliance_bnd_id'] . "]\" /></td>";
			echo "<td>
				<select name=\"alliance_bnd_level[" . $barr['alliance_bnd_id'] . "]\">";
			echo "<option value=\"0\">Bündnisanfrage</option>";
			echo "<option value=\"2\"";
			if ($barr['lvl'] == 2) echo " selected=\"selected\"";
			echo ">Bündnis</option>";
			echo "<option value=\"3\"";
			if ($barr['lvl'] == 3) echo " selected=\"selected\"";
			echo ">Krieg</option>";
			echo "<option value=\"3\"";
			if ($barr['lvl'] == 4) echo " selected=\"selected\"";
			echo ">Frieden</option>";
			echo "</select>";
			echo " &nbsp; " . df($barr['date']) . "</td>";
			echo "<td valign=\"top\"><input type=\"checkbox\" name=\"alliance_bnd_del[" . $barr['alliance_bnd_id'] . "]\" value=\"1\" /></td></tr>";
		}
		echo "</table>";
		echo "<p><input type=\"submit\" name=\"bnd_save\" value=\"Übernehmen\" /></p>";
	} else {
		echo "<p><b>Keine Bündnisse/Kriege vorhanden!</b></p>";
	}
}

function historyTab(AllianceRepository $repository, int $id): void
{
	tableStart();
	echo "<tr>
			<th style=\"width:120px;\">Datum / Zeit</th>
			<th>Ereignis</th></tr>";
	$historyEntries = $repository->findHistoryEntries($id);
	if (count($historyEntries) > 0) {
		foreach ($historyEntries as $harr) {
			echo "<tr><td>" . date("d.m.Y H:i", $harr['history_timestamp']) . "</td><td class=\"tbldata\">" . text2html($harr['history_text']) . "</td></tr>";
		}
	} else {
		echo "<tr><td colspan=\"3\" class=\"tbldata\"><i>Keine Daten vorhanden!</i></td></tr>";
	}
	tableEnd();
}

function resourcesTab(\EtoA\Alliance\Alliance $alliance): void
{
	echo '<table class="tb">';
	echo "<tr>
			<th class=\"resmetalcolor\">Titan</th>
			<td>
				<input type=\"text\" name=\"res_metal\" id=\"res_metal\" value=\"" . nf($alliance->resMetal) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_metal_add\" id=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
	echo "<th class=\"rescrystalcolor\">Silizium</th>
			<td><input type=\"text\" name=\"res_crystal\" id=\"res_crystal\" value=\"" . nf($alliance->resCrystal) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_crystal_add\" id=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
	echo "<tr><th class=\"resplasticcolor\">PVC</th>
			<td><input type=\"text\" name=\"res_plastic\" id=\"res_plastic\" value=\"" . nf($alliance->resPlastic) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_plastic_add\" id=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
	echo "<th class=\"resfuelcolor\">Tritium</th>
			<td><input type=\"text\" name=\"res_fuel\" id=\"res_fuel\" value=\"" . nf($alliance->resFuel) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_fuel_add\" id=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
	echo "<tr><th class=\"resfoodcolor\">Nahrung</th>
			<td><input type=\"text\" name=\"res_food\" id=\"res_food\" value=\"" . nf($alliance->resFood) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
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
	echo 		"</select>
		</td>
	</tr><tr>";
	tableEnd();
	echo "<p><input type=\"button\" onclick=\"xajax_showSpend(" . $alliance->id . ",xajax.getFormValues('filterForm'))\" value=\"Anzeigen\"\"/></p>";
	echo "</form>";

	echo "<div id=\"spends\">&nbsp;</div>";
}

function buildingsTab(AllianceRepository $repository, AllianceBuildingRepository $buildingRepository, int $id): void
{
	$buildListItems = $repository->findBuildings($id);
	$buildings = $buildingRepository->findAll();

	tableStart();
	echo "<tr>
			<th>Gebäude</th>
			<th>Stufe</th>
			<th>Useranzahl</th>
			<th>Status</th>
		</tr>";
	if (count($buildListItems) > 0) {
		foreach ($buildListItems as $item) {
			echo "<tr><td>" . $item['alliance_building_name'] . "</td>
			<td>" . $item['alliance_buildlist_current_level'] . "</td>
			<td>" . $item['alliance_buildlist_member_for'] . "</td><td>";
			if ($item['alliance_buildlist_build_end_time'] > time()) echo "Bauen";
			elseif ($item['alliance_buildlist_build_end_time'] > 0) echo "Bau abgeschlossen";
			else echo "Untätig";
			echo "</td>";
			echo "</tr>";
		}
	} else {
		echo "<tr><td colspan=\"4\">Keine Gebäude vorhanden!</td></tr>";
	}

	tableEnd();

	echo '<br><h2>Gebäude hinzufügen</h2>';

	tableStart();

	echo "<tr>
			<th>Gebäude</th>
			<th>Stufe</th>
			<th>Useranzahl</th>
		</tr>";
	echo '<tr><td>';

	if (count($buildings) > 0) {
		echo '<select name="alliance_building_id">';
		foreach ($buildings as $building) {
			echo "<option value=\"" . $building['alliance_building_id'] . "\">" . $building['alliance_building_name'] . "</option>";
		}
		echo "</select>";
	}

	echo '</td><td><input type="number" value=1 name="level"></td>
	<td><input type="number" value="1" name="amount"></td></tr>';

	tableEnd();

	echo '<br><input type="submit" name="buildings">';
}

function technologiesTab(AllianceRepository $repository, AllianceTechnologyRepository $technologyRepository, int $id)
{
	$techListItems = $repository->findTechnologies($id);
	$technologies = $technologyRepository->findAll();

	tableStart();
	echo "<tr>
			<th>Technologie</th><th>Stufe</th><th>Useranzahl</th><th>Status</th>
		</tr>";
	if (count($techListItems) > 0) {
		foreach ($techListItems as $item) {
			echo "<tr><td>" . $item['alliance_tech_name'] . "</td>
			<td>" . $item['alliance_techlist_current_level'] . "</td>
			<td>" . $item['alliance_techlist_member_for'] . "</td><td>";
			if ($item['alliance_techlist_build_end_time'] > time()) echo "Forschen";
			elseif ($item['alliance_techlist_build_end_time'] > 0) echo "Forschen abgeschlossen";
			else echo "Untätig";
			echo "</td>";
			echo "</tr>";
		}
	} else {
		echo "<tr><td colspan=\"4\">Keine Technologien vorhanden!</td></tr>";
	}
	tableEnd();

	echo '<br><h2>Technologien hinzufügen</h2>';

	tableStart();

	echo "<tr>
			<th>Technologie</th><th>Stufe</th><th>Useranzahl</th>
		</tr>";
	echo '<tr><td>';

	if (count($technologies) > 0) {
		echo '<select name="alliance_tech_id">';
		foreach ($technologies as $technology) {
			echo "<option value=\"" . $technology['alliance_tech_id'] . "\">" . $technology['alliance_tech_name'] . "</option>";
		}
		echo "</select>";
	}

	echo '</td><td><input type="number" value="1" name="tech_level"></td>
	<td><input type="number" value="1" name="tech_amount"></td></tr>';

	tableEnd();
}
