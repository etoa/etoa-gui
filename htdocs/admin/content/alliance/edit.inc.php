<?PHP

use EtoA\Alliance\AllianceBuildingRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceTechnologyRepository;

$repository = $app['etoa.alliance.repository'];
$buildingRepository = $app['etoa.alliance.building.repository'];
$technologyRepository = $app['etoa.alliance.technology.repository'];

if (isset($_GET['alliance_id'])) {
	$id = $_GET['alliance_id'];
}
if (isset($_GET['id'])) {
	$id = $_GET['id'];
}

if (isset($_POST['info_save']) && $_POST['info_save'] != "") {
	saveInfo($repository, $id);
} elseif (isset($_POST['member_save']) && $_POST['member_save'] != "") {
	saveMembers($repository);
} elseif (isset($_POST['bnd_save']) && $_POST['bnd_save'] != "") {
	saveDiplomacy($repository);
} elseif (isset($_POST['res_save']) && $_POST['res_save'] != "") {
	saveResources($repository, $id);
} elseif (isset($_POST['buildings']) && $_POST['buildings'] != "") {
	buildings($buildingRepository, $id);
} elseif (isset($_POST['techs']) && $_POST['techs'] != "") {
	technologies($technologyRepository, $id);
}
edit($repository, $buildingRepository, $technologyRepository, $id);

function saveInfo(AllianceRepository $repository, int $id)
{
	global $twig;

	//  Bild löschen wenn nötig
	if (isset($_POST['alliance_img_del'])) {
		$picture = $repository->getPicture($id);
		if ($picture != null) {
			if (file_exists('../' . ALLIANCE_IMG_DIR . "/" . $picture)) {
				unlink('../' . ALLIANCE_IMG_DIR . "/" . $picture);
			}
			$repository->clearPicture($id);
		}
	}

	$repository->update($id, [
		'name' => $_POST['alliance_name'],
		'tag' => $_POST['alliance_tag'],
		'text' => $_POST['alliance_text'],
		'template' => $_POST['alliance_application_template'],
		'url' => $_POST['alliance_url'],
		'founder' => $_POST['alliance_founder_id'],
	]);

	$twig->addGlobal('successMessage', 'Allianzdaten aktualisiert!');
}

function saveMembers(AllianceRepository $repository)
{
	global $twig;

	// Mitgliederänderungen
	if (isset($_POST['member_kick']) && count($_POST['member_kick']) > 0) {
		foreach (array_keys($_POST['member_kick']) as $userId) {
			$repository->removeUser($userId);
		}
	}
	if (count($_POST['member_rank']) > 0) {
		foreach ($_POST['member_rank'] as $userId => $rankId) {
			$repository->assignRankToUser($rankId, $userId);
		}
	}
	// Ränge speichern
	if (isset($_POST['rank_del']) && count($_POST['rank_del']) > 0) {
		foreach (array_keys($_POST['rank_del']) as $rankId) {
			$repository->removeRank($rankId);
		}
	}
	if (isset($_POST['rank_name']) && count($_POST['rank_name']) > 0) {
		foreach ($_POST['rank_name'] as $rankId => $name) {
			$repository->updateRank($rankId, $name, $_POST['rank_level'][$rankId]);
		}
	}

	$twig->addGlobal('successMessage', 'Mitglieder aktualisiert!');
}

function saveDiplomacy(AllianceRepository $repository)
{
	global $twig;

	// Bündnisse / Kriege speichern
	if (isset($_POST['alliance_bnd_del']) && count($_POST['alliance_bnd_del']) > 0) {
		foreach (array_keys($_POST['alliance_bnd_del']) as $diplomacyId) {
			$repository->deleteDiplomacy($diplomacyId);
		}
	}
	if (count($_POST['alliance_bnd_level']) > 0) {
		foreach (array_keys($_POST['alliance_bnd_level']) as $diplomacyId) {
			$repository->updateDiplomacy(
				$diplomacyId,
				$_POST['alliance_bnd_level'][$diplomacyId],
				$_POST['alliance_bnd_name'][$diplomacyId]
			);
		}
	}
	$twig->addGlobal('successMessage', 'Diplomatie aktualisiert!');
}

function saveResources(AllianceRepository $repository, int $id)
{
	global $twig;

	$repository->updateResources($id, [
		'metal' => nf_back($_POST['res_metal']),
		'crystal' => nf_back($_POST['res_crystal']),
		'plastic' => nf_back($_POST['res_plastic']),
		'fuel' => nf_back($_POST['res_fuel']),
		'food' => nf_back($_POST['res_food']),
		'addmetal' => nf_back($_POST['res_metal_add']),
		'addcrystal' => nf_back($_POST['res_crystal_add']),
		'addplastic' => nf_back($_POST['res_plastic_add']),
		'addfuel' => nf_back($_POST['res_fuel_add']),
		'addfood' => nf_back($_POST['res_food_add']),
	]);

	$twig->addGlobal('successMessage', 'Ressourcen aktualisiert!');
}

function buildings(AllianceBuildingRepository $buildingRepository, int $id)
{
	global $twig;

	if ($buildingRepository->existsInAlliance($id, $_POST['selected'])) {
		$buildingRepository->updateForAlliance($id, $_POST['selected'], $_POST['level'], $_POST['amount']);
		$twig->addGlobal('successMessage', 'Datensatz erfolgreich bearbeitet!');
	} else {
		$buildingRepository->addToAlliance($id, $_POST['selected'], $_POST['level'], $_POST['amount']);
		$twig->addGlobal('successMessage', 'Datensatz erfolgreich eingefügt!');
	}
}

function technologies(AllianceTechnologyRepository $technologyRepository, int $id): void
{
	global $twig;

	if ($technologyRepository->existsInAlliance($id, $_POST['selected'])) {
		$technologyRepository->updateForAlliance($id, $_POST['selected'], $_POST['level'], $_POST['amount']);
		$twig->addGlobal('successMessage', 'Datensatz erfolgreich bearbeitet!');
	} else {
		$technologyRepository->addToAlliance($id, $_POST['selected'], $_POST['level'], $_POST['amount']);
		$twig->addGlobal('successMessage', 'Datensatz erfolgreich eingefügt!');
	}
}

function edit(
	AllianceRepository $repository,
	AllianceBuildingRepository $buildingRepository,
	AllianceTechnologyRepository $technologyRepository,
	int $id
): void {
	global $twig;
	global $page;

	$arr = $repository->find($id);

	$twig->addGlobal('subtitle', "Allianz bearbeiten: [" . $arr['alliance_tag'] . "] " . $arr['alliance_name']);

	$members = collect($repository->findUsers($id))
		->mapWithKeys(fn ($arr) => [$arr['user_id'] => $arr])
		->toArray();

	$ranks = collect($repository->findRanks($id))
		->mapWithKeys(fn ($arr) => [$arr['rank_id'] => $arr])
		->toArray();

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

	infoTab($arr, $members);

	echo '</div><div id="tabs-2">';

	membersTab($members, $ranks);

	echo '</div><div id="tabs-3">';

	diplomacyTab($repository, $id);

	echo '</div><div id="tabs-4">';

	historyTab($repository, $id);

	echo '</div><div id="tabs-5">';

	resourcesTab($arr);

	echo '</div><div id="tabs-6">';

	depositsTab($arr, $members);

	echo '</div><div id="tabs-7">';

	buildingsTab($repository, $buildingRepository, $id);

	echo '</div><div id="tabs-8">';

	technologiesTab($repository, $technologyRepository, $id);

	echo '<br><input type="submit" name="techs">';

	echo '
		</div>
	</div>';
}

function infoTab(array $arr, array $members): void
{
	tableStart();
	echo "<tr><th>ID</th><td>" . $arr['alliance_id'] . "</td></tr>";
	echo "<tr><th>[Tag] Name</th><td>
			[<input type=\"text\" name=\"alliance_tag\" value=\"" . $arr['alliance_tag'] . "\" size=\"6\" maxlength=\"6\" required />]
			<input type=\"text\" name=\"alliance_name\" value=\"" . $arr['alliance_name'] . "\" size=\"30\" maxlength=\"25\" required />
		</td></tr>";
	echo "<tr><th>Gründer</th><td><select name=\"alliance_founder_id\">";
	echo "<option value=\"0\">(niemand)</option>";
	foreach ($members as $uid => $uarr) {
		echo "<option value=\"$uid\"";
		if ($arr['alliance_founder_id'] == $uarr['user_id'])
			echo " selected=\"selected\"";
		echo ">" . $uarr['user_nick'] . "</option>";
	}
	echo "</select></td></tr>";
	echo "<tr><th>Text</th><td><textarea cols=\"45\" rows=\"10\" name=\"alliance_text\">" . stripslashes($arr['alliance_text']) . "</textarea></td></tr>";
	echo "<tr><th>Gründung</th><td>" . date("Y-m-d H:i:s", $arr['alliance_foundation_date']) . "</td></tr>";
	echo "<tr><th>Website</th><td><input type=\"text\" name=\"alliance_url\" value=\"" . $arr['alliance_url'] . "\" size=\"40\" maxlength=\"250\" /></td></tr>";
	echo "<tr><th>Bewerbungsvorlage</th><td><textarea cols=\"45\" rows=\"10\" name=\"alliance_application_template\">" . stripslashes($arr['alliance_application_template']) . "</textarea></td></tr>";
	echo "<tr><th>Bild</th><td>";
	if ($arr['alliance_img'] != "") {
		echo '<img src="' . ALLIANCE_IMG_DIR . '/' . $arr['alliance_img'] . '" alt="Profil" /><br/>';
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
		foreach ($members as $uid => $uarr) {
			echo "<tr><td id=\"uifo" . $uarr['user_id'] . "\" style=\"display:none;\"><a href=\"?page=user&amp;sub=edit&amp;id=" . $uarr['user_id'] . "\">Daten</a><br/>
				" . popupLink("sendmessage", "Nachricht senden", "", "id=" . $uarr['user_id']) . "</td>
				<td><a href=\"?page=user&amp;sub=edit&amp;id=" . $uarr['user_id'] . "\" " . cTT($uarr['user_nick'], "uifo" . $uarr['user_id'] . "") . ">" . $uarr['user_nick'] . "</a></td>
				<td>" . nf($uarr['user_points']) . " Punkte</td>
				<td><select name=\"member_rank[$uid]\"><option value=\"0\">-</option>";
			foreach ($ranks as $k => $v) {
				echo "<option value=\"$k\"";
				if ($uarr['user_alliance_rank_id'] == $k)
					echo " selected=\"selected\"";
				echo ">" . $v['rank_name'] . "</option>";
			}
			echo "</select></td>";
			echo "<td><input type=\"checkbox\" name=\"member_kick[" . $uid . "]\" value=\"1\" /></td></tr>";
		}
		echo "</table>";
	} else
		echo "<b>KEINE MITGLIEDER!</b>";
	echo "</td></tr>";
	echo "<tr><th>R&auml;nge</th><td>";

	if (count($ranks) > 0) {
		echo "<table class=\"tb\">";
		echo "<tr><th>Name</th><th>Level</th><th>Löschen</th></tr>";
		foreach ($ranks as $rid => $rarr) {
			echo "<tr><td><input type=\"text\" size=\"35\" name=\"rank_name[" . $rarr['rank_id'] . "]\" value=\"" . $rarr['rank_name'] . "\" /></td>";
			echo "<td><select name=\"rank_level[" . $rarr['rank_id'] . "]\">";
			for ($x = 0; $x <= 9; $x++) {
				echo "<option value=\"$x\"";
				if ($rarr['rank_level'] == $x) echo " selected=\"selected\"";
				echo ">$x</option>";
			}
			echo "</select></td>";
			echo "<td><input type=\"checkbox\" name=\"rank_del[" . $rarr['rank_id'] . "]\" value=\"1\" /></td></tr>";
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

function resourcesTab(array $arr): void
{
	echo '<table class="tb">';
	echo "<tr>
			<th class=\"resmetalcolor\">Titan</th>
			<td>
				<input type=\"text\" name=\"res_metal\" id=\"res_metal\" value=\"" . nf($arr['alliance_res_metal']) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_metal_add\" id=\"res_metal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
	echo "<th class=\"rescrystalcolor\">Silizium</th>
			<td><input type=\"text\" name=\"res_crystal\" id=\"res_crystal\" value=\"" . nf($arr['alliance_res_crystal']) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_crystal_add\" id=\"res_crystal_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
	echo "<tr><th class=\"resplasticcolor\">PVC</th>
			<td><input type=\"text\" name=\"res_plastic\" id=\"res_plastic\" value=\"" . nf($arr['alliance_res_plastic']) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_plastic_add\" id=\"res_plastic_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td>";
	echo "<th class=\"resfuelcolor\">Tritium</th>
			<td><input type=\"text\" name=\"res_fuel\" id=\"res_fuel\" value=\"" . nf($arr['alliance_res_fuel']) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_fuel_add\" id=\"res_fuel_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td></tr>";
	echo "<tr><th class=\"resfoodcolor\">Nahrung</th>
			<td><input type=\"text\" name=\"res_food\" id=\"res_food\" value=\"" . nf($arr['alliance_res_food']) . "\" size=\"12\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/><br/>
			+/-: <input type=\"text\" name=\"res_food_add\" id=\"res_food_add\" value=\"0\" size=\"8\" maxlength=\"20\" autocomplete=\"off\" onfocus=\"this.select()\" onclick=\"this.select()\" onkeyup=\"FormatNumber(this.id,this.value,'','','');\" onkeypress=\"return nurZahlen(event)\"/></td><td colspan=\"2\">";
	tableEnd();
	echo "<p><input type=\"submit\" name=\"res_save\" value=\"Übernehmen\" /></p>";
}

function depositsTab(array $arr, array $members): void
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
	foreach ($members as $mid => $data) {
		echo "<option value=\"" . $mid . "\">" . $data['user_nick'] . "</option>";
	}
	echo 		"</select>
		</td>
	</tr><tr>";
	tableEnd();
	echo "<p><input type=\"button\" onclick=\"xajax_showSpend(" . $arr['alliance_id'] . ",xajax.getFormValues('filterForm'))\" value=\"Anzeigen\"\"/></p>";
	echo "</form>";

	echo "<div id=\"spends\">&nbsp;</div>";
}

function buildingsTab(AllianceRepository $repository, AllianceBuildingRepository $buildingRepository, int $id): void
{
	$buildListData = $repository->findBuildings($id);
	$buildings = $buildingRepository->findAll();

	tableStart();
	echo "<tr>
			<th>Gebäude</th><th>Stufe</th><th>Useranzahl</th><th>Status</th>
		</tr>";
	if (count($buildListData) > 0) {
		foreach ($buildListData as $arr) {
			echo "<tr><td>" . $arr['alliance_building_name'] . "</td><td>" . $arr['alliance_buildlist_current_level'] . "</td><td>" . $arr['alliance_buildlist_member_for'] . "</td><td>";
			if ($arr['alliance_buildlist_build_end_time'] > time()) echo "Bauen";
			elseif ($arr['alliance_buildlist_build_end_time'] > 0) echo "Bau abgeschlossen";
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
			<th>Gebäude</th><th>Stufe</th><th>Useranzahl</th>
		</tr>";
	echo '<tr><td>';

	if (count($buildings) > 0) {
		echo '<select name="selected">';
		foreach ($buildings as $arr) {
			echo "<option>" . $arr['alliance_building_name'] . "</option>";
		}
		echo "</select>";
	}

	echo '</td><td><input type=number value=1 name="level"></td><td><input type=number value=1 name="amount"></td></tr>';

	tableEnd();

	echo '<br><input type="submit" name="buildings">';
}

function technologiesTab(AllianceRepository $repository, AllianceTechnologyRepository $technologyRepository, int $id)
{
	$techlistData = $repository->findTechnologies($id);
	$techs = $technologyRepository->findAll();

	tableStart();
	echo "<tr>
			<th>Technologie</th><th>Stufe</th><th>Useranzahl</th><th>Status</th>
		</tr>";
	if (count($techlistData) > 0) {
		foreach ($techlistData as $arr) {
			echo "<tr><td>" . $arr['alliance_tech_name'] . "</td><td>" . $arr['alliance_techlist_current_level'] . "</td><td>" . $arr['alliance_techlist_member_for'] . "</td><td>";
			if ($arr['alliance_techlist_build_end_time'] > time()) echo "Forschen";
			elseif ($arr['alliance_techlist_build_end_time'] > 0) echo "Forschen abgeschlossen";
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

	if (count($techs) > 0) {
		echo '<select name="selected_tech">';
		foreach ($techs as $arr) {
			echo "<option>" . $arr['alliance_tech_name'] . "</option>";
		}
		echo "</select>";
	}

	echo '</td><td><input type=number value=1 name="tech_level"></td><td><input type=number value=1 name="tech_amount"></td></tr>';

	tableEnd();
}
