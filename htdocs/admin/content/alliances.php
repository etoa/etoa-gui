<?PHP

use EtoA\Admin\Forms\AllianceBuildingsForm;
use EtoA\Admin\Forms\AllianceTechnologiesForm;
use EtoA\Alliance\AllianceDiplomacyRepository;
use EtoA\Alliance\AllianceRankRepository;
use EtoA\Alliance\AllianceRepository;
use EtoA\Alliance\AllianceService;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var AllianceRepository $repository */
$repository = $app[AllianceRepository::class];

/** @var AllianceService $service */
$service = $app[AllianceService::class];

/** @var AllianceRankRepository $allianceRankRepository */
$allianceRankRepository = $app[AllianceRankRepository::class];

/** @var AllianceDiplomacyRepository $allianceDiplomacyRepository */
$allianceDiplomacyRepository = $app[AllianceDiplomacyRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

if ($sub == "buildingsdata") {
    AllianceBuildingsForm::render($app, $request);
} elseif ($sub == "techdata") {
    AllianceTechnologiesForm::render($app, $request);
} else {
    \EtoA\Admin\LegacyTemplateTitleHelper::$title = 'Allianzen';

    if (
        $request->request->has('alliance_search')
        && $request->query->has('action')
        && $request->query->get('action') == "search"
    ) {
        searchResults($request, $repository);
    } else if ($request->query->has('sub') && $request->query->get('sub') == "edit") {
        include("alliance/edit.inc.php");
    } elseif ($request->query->has('sub') && $request->query->get('sub') == "drop" && $request->query->has('alliance_id')) {
        drop($request, $repository);
    } else {
        index($request, $repository, $service);
    }
}

function searchResults(Request $request, AllianceRepository $repository)
{
    global $page, $app;

    \EtoA\Admin\LegacyTemplateTitleHelper::$subTitle = 'Suchergebnisse';

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

        /** @var UserRepository $userRepository */
        $userRepository = $app[UserRepository::class];
        $userNicks = $userRepository->searchUserNicknames();
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
            echo "<td>" . $userNicks[(int) $alliance['alliance_founder_id']] . "</td>";
            echo "<td>" . StringUtils::formatDate((int) $alliance['alliance_foundation_date']) . "</td>";
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
    global $page, $app;

    $alliance = $repository->getAlliance($request->query->getInt('alliance_id'));
    if ($alliance !== null) {
        /** @var UserRepository $userRepository */
        $userRepository = $app[UserRepository::class];
        echo "Soll folgende Allianz gelöscht werden?<br/><br/>";
        echo "<form action=\"?page=$page\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">ID</td>
			<td class=\"tbldata\">" . $alliance->id . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Name</td>
			<td class=\"tbldata\">" . $alliance->name . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Tag</td>
			<td class=\"tbldata\">" . $alliance->tag . "</td></tr>";
        $userNicks = $userRepository->searchUserNicknames();
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Gründer</td>
			<td class=\"tbldata\">" . $userNicks[$alliance->founderId] . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Text</td>
			<td class=\"tbldata\">" . BBCodeUtils::toHTML($alliance->text) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Gründung</td>
			<td class=\"tbldata\">" . date("Y-m-d H:i:s", $alliance->foundationTimestamp) . "</td></tr>";
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Website</td>
			<td class=\"tbldata\">" . $alliance->url . "</td></tr>";
        if ($alliance->image !== null) {
            echo "<tr><td class=\"tbltitle\" valign=\"top\">Bild</td><td class=\"tbldata\"><img src=\"" . $alliance->getImageUrl() . "\" width=\"100%\" alt=\"" . $alliance->image . "\" /></td></tr>";
        }
        echo "<tr><td class=\"tbltitle\" valign=\"top\">Mitglieder</td><td class=\"tbldata\">";
        $usersInAlliance = $repository->findUsers($alliance->id);
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
        echo "<input type=\"hidden\" name=\"alliance_id\" value=\"" . $alliance->id . "\" />";
        echo "<br/><input type=\"submit\" name=\"drop\" value=\"Löschen\" />&nbsp;";
        echo "<input type=\"button\" value=\"Zurück\" onclick=\"history.back();\" /> ";
        echo "<input type=\"button\" onclick=\"document.location='?page=$page'\" value=\"Neue Suche\" />";
        echo "</form>";
    } else {
        echo "<b>Fehler:</b> Datensatz nicht gefunden!<br/><br/>
		<a href=\"javascript:history.back();\">Zurück</a>";
    }
}

function index(Request $request, AllianceRepository $repository, AllianceService $allianceService)
{
    global $page;

    // Allianz löschen
    if ($request->request->has('drop')) {
        $alliance = $repository->getAlliance($request->request->getInt('alliance_id'));
        if ($allianceService->delete($alliance)) {
            echo "Die Allianz wurde gelöscht!<br/><br/>";
        } else {
            echo MessageBox::error("", "Allianz konnte nicht gelöscht werden (ist sie in einem aktiven Krieg?)");
        }
    }

    // Suchmaske
    \EtoA\Admin\LegacyTemplateTitleHelper::$subTitle = 'Suchmaske';

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
    echo "<br/>Es sind " . StringUtils::formatNumber($repository->count()) . " Einträge in der Datenbank vorhanden.";
}
