<?PHP

use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\InvalidAllianceParametersException;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var AllianceRepository */
$repository = $app['etoa.alliance.repository'];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "imagecheck") {
	imagecheck($request, $repository);
} elseif ($sub == "buildingsdata") {
	advanced_form("alliancebuildings", $twig);
} elseif ($sub == "techdata") {
	advanced_form("alliancetechnologies", $twig);
} elseif ($sub == "create") {
	create($request, $repository);
} elseif ($sub == "news") {
	news();
} elseif ($sub == "crap") {
	crap($request, $repository);
} else {
	$twig->addGlobal('title', 'Allianzen');

	if (
		$request->request->has('alliance_search')
		&& $request->query->has('action')
		&& $request->query->get('action') == "search"
	) {
		searchResults($request, $repository, $twig);
	} else if ($request->query->has('sub') && $request->query->get('sub') == "edit") {
		include("alliance/edit.inc.php");
	} elseif ($request->query->has('sub') && $request->query->get('sub') == "drop" && $request->query->has('alliance_id')) {
		drop($request, $repository);
	} else {
		index($request, $repository, $twig);
	}
}

function imagecheck(Request $request, AllianceRepository $repository)
{
	global $page;
	global $sub;

	$dir = ALLIANCE_IMG_DIR . "/";
	echo "<h1>Allianz-Bilder prüfen</h1>";

	//
	// Check submit
	//
	if ($request->request->has('validate_submit')) {
		foreach ($request->request->get('validate') as $id => $v) {
			if ($v == 0) {
				if (removeAlliancePicture($repository, $id)) {
					echo "Bild entfernt!<br/><br/>";
				}
			} else {
				$repository->markPictureChecked($id);
			}
		}
	}

	//
	// Check new images
	//
	echo "<h2>Noch nicht verifizierte Bilder</h2>";
	echo "Diese Bilder gehören zu aktiven Allianzen. Bitte prüfe regelmässig, ob sie nicht gegen unsere Regeln verstossen!<br/>";
	$alliances = $repository->findAllWithUncheckedPictures();
	if (count($alliances) > 0) {
		echo "Es sind " . count($alliances) . " Bilder gespeichert!<br/><br/>";
		echo "<form action=\"\" method=\"post\">
			<table class=\"tb\"><tr><th>User</th><th>Fehler</th><th>Aktionen</th></tr>";
		foreach ($alliances as $alliance) {
			echo "<tr><td>[" . $alliance['alliance_tag'] . "] " . $alliance['alliance_name'] . "</td><td>";
			if (file_exists($dir . $alliance['alliance_img'])) {
				echo '<img src="' . $dir . $alliance['alliance_img'] . '" alt="Profil" />';
			} else {
				echo '<span style=\"color:red\">Bild existiert nicht!</span>';
			}
			echo "</td><td>
				<input type=\"radio\" name=\"validate[" . $alliance['alliance_id'] . "]\" value=\"1\" checked=\"checked\"> Bild ist in Ordnung<br/>
				<input type=\"radio\" name=\"validate[" . $alliance['alliance_id'] . "]\" value=\"0\" > Bild verstösst gegen die Regeln. Lösche es!<br/>
				</td></tr>";
		}
		echo "</table><br/>
			<input type=\"submit\" name=\"validate_submit\" value=\"Speichern\" /></form>";
	} else {
		echo "<br/><i>Keine Bilder vorhanden!</i>";
	}

	//
	// Orphans
	//
	$alliances = $repository->findAllWithPictures();
	$nr = count($alliances);
	$paths = array();
	$nicks = array();
	if ($nr > 0) {
		foreach ($alliances as $alliance) {
			$paths[$alliance['alliance_id']] = $alliance['alliance_img'];
			$nicks[$alliance['alliance_id']] = $alliance['alliance_name'];
		}
	}
	$files = array();
	if (is_dir($dir)) {
		$d = opendir($dir);
		while ($f = readdir($d)) {
			if (is_file($dir . $f)) {
				array_push($files, $f);
			}
		}
		closedir($d);
	}

	$overhead = array();
	while (count($files) > 0) {
		$k = array_pop($files);
		if (!in_array($k, $paths, true))
			array_push($overhead, $k);
	}

	if ($request->query->has('action') && $request->query->get('action') == "clearOverhead") {
		while (count($overhead) > 0) {
			unlink($dir . array_pop($overhead));
		}
		echo "Verwaiste Bilder gelöscht!<br/><bt/>";
	}
	$co = count($overhead);

	echo "<h2>Verwaiste Bilder</h2>";
	if ($co > 0) {
		echo "Diese Bilder gehören zu Allianzen, die nicht mehr in unserer Datenbank vorhanden sind.<br/>
				Es sind $co Bilder vorhanden. <a href=\"?page=$page&amp;sub=$sub&amp;action=clearOverhead\">Lösche alle verwaisten Bilder</a><br/><br/>";
		echo "<table class=\"tb\">
				<tr><th>Datei</th><th>Bild</th></tr>";
		foreach ($overhead as $v) {
			echo "<tr><td>" . $v . "</td>";
			echo '<td><img src="' . $dir . $v . '" alt="Profil" /></td></tr>';
		}
		echo "</table><br/>";
	} else {
		echo "<i>Keine vorhanden!</i>";
	}
}

function create(Request $request, AllianceRepository $repository)
{
	global $page;
	global $sub;

	echo "<h1>Allianz erstellen</h1>";

	if ($request->request->has('create')) {
		// TODO refactor wild mix between active-record classes and repository pattern
		$founder = new User($request->request->getInt('alliance_founder_id'));
		try {
			$id = $repository->create(
				$request->request->get('alliance_tag'),
				$request->request->get('alliance_name'),
				$founder->id,
			);
			$alliance = new Alliance($id);
			$founder->alliance = $alliance;
			$founder->addToUserLog("alliance", "{nick} hat die Allianz [b]" . $alliance . "[/b] gegründet.");
			$alliance->addHistory("Die Allianz [b]" . $alliance . "[/b] wurde von [b]" . $founder . "[/b] gegründet!");
			success_msg("Allianz wurde erstellt! [[page alliances sub=edit id=" . $alliance->id . "]Details[/page]]");
		} catch (InvalidAllianceParametersException $ex) {
			error_msg("Allianz konnte nicht erstellt werden!\n\n" . $ex->getMessage() . "");
		}
	}

	echo "<form action=\"?page=$page&amp;sub=$sub\" method=\"post\">";
	echo '<table class="tbl">';
	echo "<tr><th>Tag:</th><td>
		<input type=\"text\" name=\"alliance_tag\" value=\"\" required />
		</td></td>";
	echo "<tr><th>Name:</th><td>
		<input type=\"text\" name=\"alliance_name\" value=\"\" required />
		</td></td>";
	echo "<tr><th>Gründer:</th><td>
		<select name=\"alliance_founder_id\" />";
	foreach ($repository->listSoloUsers() as $key => $value) {
		echo "<option value=\"" . $key . "\">" . $value . "</option>";
	}
	echo "</select>
		</td></td>";
	tableEnd();
	echo "<p><input type=\"submit\" name=\"create\" value=\"Erstellen\" /></p>
		</form>";
}

function news()
{
	global $page;
	global $sub;
	global $conf;

	echo '<h1>Allianz-News</h1>';

	echo 'News entfernen die älter als <select id="timespan">
		<option value="604800">1 Woche</option>
		<option value="1209600">2 Wochen</option>
		<option value="2592000" selected="selected">1 Monat</option>
		<option value="5184000">2 Monate</option>
		<option value="7776000">3 Monate</option>
		<option value="15552000">6 Monate</option>
		</select> sind:
		<input type="button" onclick="xajax_allianceNewsRemoveOld(document.getElementById(\'timespan\').options[document.getElementById(\'timespan\').selectedIndex].value)" value="Ausführen" /><br/><br/>';

	$ban_timespan = [
		21600 => '6 Stunden',
		43200 => '12 Stunden',
		64800 => '18 Stunden',
		86400 => '1 Tag',
		172800 => '2 Tage',
		259200 => '3 Tage',
		432000 => '5 Tage',
		604800 => '1 Woche'
	];
	$ban_text = $conf['townhall_ban']['p1'] != '' ? stripslashes($conf['townhall_ban']['p1']) : 'Rathaus-Missbrauch';

	echo 'Standardeinstellung für Sperre: <select id="ban_timespan">';
	foreach ($ban_timespan as $k => $v) {
		echo '<option value="' . $k . '"';
		echo  $conf['townhall_ban']['v'] == $k ? ' selected="selected"' : '';
		echo '>' . $v . '</option>';
	}
	echo '</select> mit folgendem Text: <input type="text" id="ban_text" value="' . $ban_text . '" size="35" /> ';
	echo '<input type="button" onclick="xajax_allianceNewsSetBanTime(document.getElementById(\'ban_timespan\').options[document.getElementById(\'ban_timespan\').selectedIndex].value,document.getElementById(\'ban_text\').value)" value="Speichern" /><br/><br/>';

	echo '<form id="newsForm" action="?page=' . $page . '&amp;sub=' . $sub . '" method="post">';
	echo '<div id="newsBox">Lade...</div></form>';
	echo '<script type="text/javascript">xajax_allianceNewsLoad()</script>';
}

function crap(Request $request, AllianceRepository $repository)
{
	global $page;
	global $sub;

	echo "<h1>Überflüssige Daten</h1>";

	if ($request->query->has('action') && $request->query->get('action') == "cleanupRanks") {
		if ($repository->deleteOrphanedRanks() > 0) {
			echo "Fehlerhafte Daten gelöscht.";
		}
	} elseif ($request->query->has('action') && $request->query->get('action') == "cleanupDiplomacy") {
		if ($repository->deleteOrphanedDiplomacies() > 0) {
			echo "Fehlerhafte Daten gelöscht.";
		}
	} elseif ($request->query->has('action') && $request->query->get('action') == "cleanupEmptyAlliances") {
		$alliances = $repository->findAll();
        $cnt = 0;
		if (count($alliances) > 0) {
			foreach ($alliances as $alliance) {
				if ($repository->countUsers($alliance['alliance_id']) == 0) {
					if ($repository->remove($alliance['alliance_id'])) {
						$cnt++;
					}
				}
			}
		}
		echo "$cnt leere Allianzen wurden gelöscht.<br/>";
	}

	// Ränge ohne Allianz
	echo "<h2>Ränge ohne Allianz</h2>";
	$ranksWithoutAlliance = $repository->countOrphanedRanks();
	if ($ranksWithoutAlliance > 0) {
		echo "$ranksWithoutAlliance Ränge ohne Allianz.
			<a href=\"?page=$page&amp;sub=$sub&amp;action=cleanupRanks\">Löschen?</a>";
	} else {
		echo "Keine fehlerhaften Daten gefunden.";
	}

	// Bündnisse/Kriege ohne Allianz
	echo "<h2>Bündnisse/Kriege ohne Allianz</h2>";
	$bndWithoutAlliance = $repository->countOrphanedDiplomacies();
	if ($bndWithoutAlliance > 0) {
		echo "$bndWithoutAlliance Bündnisse/Kriege ohne Allianz.
			<a href=\"?page=$page&amp;sub=$sub&amp;action=cleanupDiplomacy\">Löschen?</a>";
	} else {
		echo "Keine fehlerhaften Daten gefunden.";
	}

	// Allianzen ohne Gründer
	echo "<h2>Allianzen ohne Gründer</h2>";
	$alliancesWithoutFounder = $repository->findAllWithoutFounder();
	if (count($alliancesWithoutFounder) > 0) {
		echo "<table class=\"tbl\">";
		echo "<tr><th class=\"tbltitle\">Tag</th>
			<th class=\"tbltitle\">Name</th>
			<th>&nbsp;</th></tr>";
		foreach ($alliancesWithoutFounder as $alliance) {
			echo "<tr><td class=\"tbldata\">" . $alliance['alliance_name'] . "</td>
				<td class=\"tbldata\">" . $alliance['alliance_tag'] . "</td>
				<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=edit&amp;alliance_id=" . $alliance['alliance_id'] . "\">detail</a></td></tr>";
		}
		echo "</table><br/>";
		echo count($alliancesWithoutFounder) . " Allianzen ohne Gründer.";
	} else {
		echo "Keine fehlerhaften Daten gefunden.";
	}

	// User mit fehlerhafter Allianz-Verknüpfung
	echo "<h2>User mit fehlerhafter Allianz-Verknüpfung</h2>";
	$usersWithInvalidAlliances = $repository->findAllSoloUsers();
	if (count($usersWithInvalidAlliances) > 0) {
		echo "<table class=\"tbl\">";
		echo "<tr><th class=\"tbltitle\">Nick</th>
			<th class=\"tbltitle\">E-Mail</th>
			<th>&nbsp;</th></tr>";
		foreach ($usersWithInvalidAlliances as $users) {
			echo "<tr><td class=\"tbldata\">" . $users['user_nick'] . "</td>
				<td class=\"tbldata\">" . $users['user_email'] . "</td>
				<td class=\"tbldata\"><a href=\"?page=user&amp;sub=edit&amp;user_id=" . $users['user_id'] . "\">detail</a></td></tr>";
		}
		echo "</table><br/>";
		echo count($usersWithInvalidAlliances) . " User mit fehlerhafter Verknüpfung.";
	} else {
		echo "Keine fehlerhaften Daten gefunden.";
	}

	// Leere Allianzen
	echo "<h2>Leere Allianzen (Allianzen ohne User)</h2>";
	$alliancesWithoutUsers = $repository->findAllWithoutUsers();
	if (count($alliancesWithoutUsers) > 0) {
		echo "<table class=\"tbl\">";
		echo "<tr><th class=\"tbltitle\">Name</th>
			<th class=\"tbltitle\">Tag</th><th>&nbsp;</th>
			<th>&nbsp;</th></tr>";
		foreach ($alliancesWithoutUsers as $alliance) {
			echo "<tr><td class=\"tbldata\">" . $alliance['alliance_name'] . "</td>
				<td class=\"tbldata\">" . $alliance['alliance_tag'] . "</td>
				<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=edit&amp;alliance_id=" . $alliance['alliance_id'] . "\">detail</a></td>
				<td class=\"tbldata\"><a href=\"?page=$page&amp;sub=drop&amp;alliance_id=" . $alliance['alliance_id'] . "\">löschen</a></td></tr>";
		}
		echo "</table><br/>";
		echo count($alliancesWithoutUsers) . " Allianzen sind leer.
			<a href=\"?page=$page&amp;sub=$sub&amp;action=cleanupEmptyAlliances\">Löschen?</a>";
	} else {
		echo "Keine fehlerhaften Daten gefunden.";
	}
}

function searchResults(Request $request, AllianceRepository $repository, Environment $twig)
{
	global $page;

	$twig->addGlobal('subtitle', 'Suchergebnisse');

	$alliances = $repository->findByFormData($request->request->all());

	$nr = count($alliances);
	if ($nr == 1) {
		$alliance = $alliances[0];
		echo "<script>document.location='?page=$page&sub=edit&id=" . $alliance['alliance_id'] . "';</script>
			Klicke <a href=\"?page=$page&sub=edit&id=" . $alliance['alliance_id'] . "\">hier</a> falls du nicht automatisch weitergeleitet wirst...";
	} elseif ($nr > 0) {

		echo $nr . " Datensätze vorhanden<br/><br/>";
		if ($nr > 20) {
			echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /><br/><br/>";
		}

		$users = get_user_names();
		echo "<table class=\"tb\">";
		echo "<tr>";
		echo "<th>ID</th>";
		echo "<th>Name</th>";
		echo "<th>Gründer</th>";
		echo "<th>Gründung</th>";
		echo "<th>User</th>";
		echo "<th>&nbsp;</th>";
		echo "</tr>";
		foreach ($alliances as $alliance) {
			echo "<tr>";
			echo "<td>" . $alliance['alliance_id'] . "</td>";
			echo "<td>[" . $alliance['alliance_tag'] . "] <a href=\"?page=$page&sub=edit&alliance_id=" . $alliance['alliance_id'] . "\">" . $alliance['alliance_name'] . "</a></td>";
			echo "<td>" . $users[$alliance['alliance_founder_id']]['nick'] . "</td>";
			echo "<td>" . df($alliance['alliance_foundation_date']) . "</td>";
			echo "<td>" . $alliance['cnt'] . "</td>";
			echo "<td style=\"width:50px;\">";
			echo del_button("?page=$page&sub=drop&alliance_id=" . $alliance['alliance_id']) . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		echo "<br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" /> ";
		echo "<input type=\"button\" onclick=\"document.location='?page=$page&amp;action=search'\" value=\"Aktualisieren\" /> ";
	} else {
		echo "Die Suche lieferte keine Resultate!<br/><br/><input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Zurück\" />";
	}
}

function drop(Request $request, AllianceRepository $repository)
{
	global $page;

	$alliance = $repository->find($request->query->getInt('alliance_id'));
	if ($alliance !== null) {
		echo "Soll folgende Allianz gelöscht werden?<br/><br/>";
		echo "<form action=\"?page=$page\" method=\"post\">";
		echo "<table class=\"tbl\">";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td>
			<td class=\"tbldata\">" . $alliance['alliance_id'] . "</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Name</td>
			<td class=\"tbldata\">" . $alliance['alliance_name'] . "</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Tag</td>
			<td class=\"tbldata\">" . $alliance['alliance_tag'] . "</td></tr>";
		$users = get_user_names();
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Gründer</td>
			<td class=\"tbldata\">" . $users[$alliance['alliance_founder_id']]['nick'] . "</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td>
			<td class=\"tbldata\">" . text2html($alliance['alliance_text']) . "</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Gründung</td>
			<td class=\"tbldata\">" . date("Y-m-d H:i:s", $alliance['alliance_foundation_date']) . "</td></tr>";
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Website</td>
			<td class=\"tbldata\">" . $alliance['alliance_url'] . "</td></tr>";
		if (isset($alliance['alliance_img'])) {
			echo "<tr><td class=\"tbltitle\" valign=\"top\">Bild</td><td class=\"tbldata\"><img src=\"" . ALLIANCE_IMG_DIR . '/' . $alliance['alliance_img'] . "\" width=\"100%\" alt=\"" . $alliance['alliance_img'] . "\" /></td></tr>";
		}
		echo "<tr><td class=\"tbltitle\" valign=\"top\">Mitglieder</td><td class=\"tbldata\">";
		$usersInAlliance = $repository->findUsers($alliance['alliance_id']);
		if (count($usersInAlliance) > 0) {
			echo "<table style=\"width:100%\">";
			foreach ($usersInAlliance as $uarr)
				echo "<tr><td>" . $uarr['user_nick'] . "</td>
					<td>" . $uarr['user_points'] . " Punkte</td>
					<td>[<a href=\"?page=user&amp;sub=edit&amp;user_id=" . $uarr['user_id'] . "\">details</a>] [<a href=\"?page=messages&amp;sub=sendmsg&amp;user_id=" . $uarr['user_id'] . "\">msg</a>]</td></tr>";
			echo "</table>";
		} else {
			echo "<b>KEINE MITGLIEDER!</b>";
		}
		echo "</td></tr>";
		echo "</table>";
		echo "<input type=\"hidden\" name=\"alliance_id\" value=\"" . $alliance['alliance_id'] . "\" />";
		echo "<br/><input type=\"submit\" name=\"drop\" value=\"Löschen\" />&nbsp;";
		echo "<input type=\"button\" value=\"Zurück\" onclick=\"history.back();\" /> ";
		echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
		echo "</form>";
	} else {
		echo "<b>Fehler:</b> Datensatz nicht gefunden!<br/><br/>
		<a href=\"javascript:history.back();\">Zurück</a>";
	}
}

function index(Request $request, AllianceRepository $repository, Environment $twig)
{
	global $page;

	// Allianz löschen
	if ($request->request->has('drop')) {
		$ally = new Alliance($request->request->getInt('alliance_id'));
		if ($ally->delete()) {
			echo "Die Allianz wurde gelöscht!<br/><br/>";
		} else {
			echo MessageBox::error("", "Allianz konnte nicht gelöscht werden (ist sie in einem aktiven Krieg?)");
		}
	}

	// Suchmaske
	$twig->addGlobal("subtitle", 'Suchmaske');

	echo "<form action=\"?page=$page&amp;action=search\" method=\"post\">";
	echo "<table class=\"tbl\">";
	echo "<tr><td class=\"tbltitle\">ID</td>
			<td class=\"tbldata\">
				<input type=\"text\" name=\"alliance_id\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
	echo "</td></tr>";
	echo "<tr><td class=\"tbltitle\">Tag</td>
			<td class=\"tbldata\">
				<input type=\"text\" name=\"alliance_tag\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
	echo fieldComparisonSelectBox('alliance_tag');
	echo "</td></tr>";
	echo "<tr><td class=\"tbltitle\">Name</td>
			<td class=\"tbldata\">
				<input type=\"text\" name=\"alliance_name\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\" onkeyup=\"xajax_searchAlliance(this.value,'alliance_name','citybox2');\"/> ";
	echo fieldComparisonSelectBox('alliance_name');
	echo "<br><div class=\"citybox\" id=\"citybox2\">&nbsp;</div></td></tr>";
	echo "<tr><td class=\"tbltitle\">Text</td>
			<td class=\"tbldata\">
				<input type=\"text\" name=\"alliance_text\" value=\"\" size=\"20\" maxlength=\"250\" /> ";
	echo fieldComparisonSelectBox('alliance_text');
	echo "</td></tr>";
	echo "</table>";
	echo "<br/><input type=\"submit\" name=\"alliance_search\" value=\"Suche starten\" /> (wenn nichts eingegeben wird werden alle Datensätze angezeigt)</form>";
	echo "<br/>Es sind " . nf($repository->count()) . " Einträge in der Datenbank vorhanden.";
}

function removeAlliancePicture(AllianceRepository $repository, int $allianceId): bool
{
	$picture = $repository->getPicture($allianceId);
	if ($picture != null) {
		if (file_exists(ALLIANCE_IMG_DIR . "/" . $picture)) {
			unlink(ALLIANCE_IMG_DIR . "/" . $picture);
		}
		return $repository->clearPicture($allianceId);
	}
	return false;
}
