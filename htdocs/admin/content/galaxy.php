<?PHP

use EtoA\Admin\Forms\PlanetTypesForm;
use EtoA\Admin\Forms\StarTypesForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Universe\Entity\EntityLabelSearch;
use EtoA\Universe\Entity\EntityLabelSort;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\User\UserRepository;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var Request $request */
$request = Request::createFromGlobals();

if ($sub == "map") {
    galaxyMap($config, $userRepository, $twig);
} elseif ($sub == "exploration") {
    exploration($twig);
} elseif ($sub == "uni") {
    universe();
} elseif ($sub == "galaxycheck") {
    galaxyCheck();
} elseif ($sub == "planet_types") {
    planetTypes($app, $twig, $request);
} elseif ($sub == "sol_types") {
    starTypes($app, $twig, $request);
} else {
    entities($config);
}

function galaxyMap(ConfigurationService $config, UserRepository $userRepository, Environment $twig)
{
    require("galaxy/map.inc.php");
}

function exploration(Environment $twig)
{
    require("galaxy/exploration.inc.php");
}

function universe()
{
    global $app;
    global $page;
    global $sub;
    require("galaxy/universe.php");
}

function galaxyCheck()
{
    require("galaxy/galaxycheck.php");
}

function planetTypes(Container $app, Environment $twig, Request $request)
{
    PlanetTypesForm::render($app, $twig, $request);
}

function starTypes(Container $app, Environment $twig, Request $request)
{
    StarTypesForm::render($app, $twig, $request);
}

function entities(ConfigurationService $config)
{
    global $app;
    global $cu;
    global $page;
    global $sub;

    $order_array = array();
    $order_array['id'] = "Objekt-ID";
    $order_array['planet_name'] = "Objekt-Name";
    $order_array['user_nick'] = "Besitzer-Name";

    echo "<h1>Raumobjekte (Entitäten)</h1>";

    $sa = array();
    $so = array();

    // Create search query if cell id is requested
    if (isset($_GET['cell_id'])) {
        $_GET['sq'] = base64_encode("cell_id:=:" . intval($_GET['cell_id']));
    }

    //
    // Details bearbeiten
    //
    if ($sub == "edit") {
        require("galaxy/edit.php");
    }

    //
    // Search query and result
    //
    elseif (searchQueryArray($sa, $so)) {
        /** @var EntityRepository $entityRepository */
        $entityRepository = $app[EntityRepository::class];

        $search = EntityLabelSearch::create();
        if (isset($sa['id'])) {
            $search->id((int) $sa['id']);
        }
        if (isset($sa['code'])) {
            $search->codeIn($sa['code'][1]);
        }
        if (isset($sa['cell_id'])) {
            $search->cellId((int) $sa['cell_id']);
        }
        if (isset($sa['cell_cx'])) {
            $search->cx((int) $sa['cell_cx']);
        }
        if (isset($sa['cell_cy'])) {
            $search->cy((int) $sa['cell_cy']);
        }
        if (isset($sa['cell_c'])) {
            $val = explode("_", $sa['cell_c'][1]);
            $search
                ->cx((int) $val[0])
                ->cy((int) $val[1]);
        }
        if (isset($sa['cell_sx'])) {
            $search->sx((int) $sa['cell_sx']);
        }
        if (isset($sa['cell_sy'])) {
            $search->sy((int) $sa['cell_sy']);
        }
        if (isset($sa['cell_s'])) {
            $val = explode("_", $sa['cell_s'][1]);
            $search
                ->sx((int) $val[0])
                ->sy((int) $val[1]);
        }
        if (isset($sa['cell_pos'])) {
            $search->pos((int) $sa['cell_pos']);
        }

        if (isset($sa['name'])) {
            $search->likePlanetName($sa['name'][1]);
        }
        if (isset($sa['user_id'])) {
            $search->planetUserId((int) $sa['user_id']);
        }
        if (isset($sa['user_main']) && $sa['user_main'][1] < 2) {
            $search->planetUserMain((bool) $sa['user_main'][1]);
        }
        if (isset($sa['debris']) && $sa['debris'][1] < 2) {
            $search->planetDebris($sa['debris'][1] == 1);
        }
        if (isset($sa['user_nick'])) {
            $search->likePlanetUserNick($sa['user_nick'][1]);
        }
        if (isset($sa['desc']) && $sa['desc'][1] < 2) {
            $search->planetHasDescription($sa['desc'][1] == 1);
        }

        $sort = EntityLabelSort::id();
        if (count($so) > 1) {
            foreach ($so as $k => $v) {
                if (!in_array($k, ['limit', 'id'], true)) {
                    if ($k === 'planet_name') {
                        $sort = EntityLabelSort::planetName();
                    } else {
                        $sort = EntityLabelSort::userNick();
                    }
                }
            }
        }

        $entities = $entityRepository->searchEntityLabels($search, $sort, (int) $so['limit']);

        // Execute query
        $nr = count($entities);

        // Save query
        searchQuerySave($sa, $so);

        // Select total found rows
        $enr = $entityRepository->countEntityLabels($search);

        echo "<h2>Suchresultate</h2>";
        echo "<form acton=\"?page=" . $page . "\" method=\"post\">";

        echo "<b>Abfrage:</b> ";
        $cnt = 0;
        $n = count($sa);
        foreach ($sa as $k => $v) {
            echo "<i>$k</i> " . searchFieldOptionsName($v[0]) . " ";
            if (is_array($v[1])) {
                $scnt = 0;
                $sn = count($v[1]);
                foreach ($v[1] as $sv) {
                    echo "'$sv'";
                    $scnt++;
                    if ($scnt < $sn)
                        echo " oder ";
                }
            } else
                echo "'" . $v[1] . "'";
            $cnt++;
            if ($cnt < $n)
                echo ", ";
        }
        echo "<br/><b>Ergebnis:</b> " . $nr . " Datens&auml;tze (" . $enr . " total)<br/>";
        //, Sortierung: ".$order_array[$_POST['order']]."<br/>";
        echo "<b>Anzeigen:</b> <select name=\"search_limit\">";
        for ($x = 100; $x <= 2000; $x += 100) {
            echo "<option value=\"$x\"";
            if ($so['limit'] == $x)
                echo " selected=\"selected\"";
            echo ">$x</option>";
        }
        echo "</select> Datensätze sortiert nach <select name=\"search_order\">";
        foreach ($order_array as $k => $v) {
            echo "<option value=\"" . $k . "\"";
            if (isset($so[$k]))
                echo " selected=\"selected\"";
            echo ">" . $v . "</option>";
        }
        echo "</select> <input type=\"submit\" value=\"Anzeigen\" name=\"search_resubmit\" /></form><br/>";

        if ($nr > 0) {
            /** @var PlanetTypeRepository $planetTypeRepository */
            $planetTypeRepository = $app[PlanetTypeRepository::class];
            $planetTypeNames = $planetTypeRepository->getPlanetTypeNames(true);
            /** @var SolarTypeRepository $starTypeRepository */
            $starTypeRepository = $app[SolarTypeRepository::class];
            $starTypeNames = $starTypeRepository->getSolarTypeNames(true);
            if ($nr > 20) {
                echo button("Neue Suche", "?page=$page&amp;newsearch") . "<br/><br/>";
            }

            echo "<table class=\"tb\">";
            echo "<tr>";
            echo "<th style=\"width:40px;\">ID</th>";
            echo "<th style=\"width:90px;\">Koordinaten</th>";
            echo "<th>Entitätstyp</th>";
            echo "<th>Subtyp</th>";
            echo "<th>Name</th>";
            echo "<th>Besitzer</th>";
            echo "<th style=\"width:20px;\">&nbsp;</th>";
            echo "</tr>";
            foreach ($entities as $entity) {
                echo "<tr>";
                echo "<td>
                    <a href=\"?page=$page&sub=edit&id=" . $entity->id . "\">
                    " . $entity->id . "
                    </a></td>";
                echo "<td>
                    <a href=\"?page=$page&sub=edit&id=" . $entity->id . "\">
                    " . $entity->coordinatesString() . "
                    </a>  </td>";
                echo "<td style=\"color:" . Entity::$entityColors[$entity->code] . "\">";
                echo $entity->codeString();
                echo " " . ($entity->ownerMain ? "(Hauptplanet)" : '') . "";
                echo "</td>";
                $typeName = null;
                if ($entity->code === EntityType::PLANET) {
                    $typeName = $planetTypeNames[$entity->typeId];
                } elseif ($entity->code === EntityType::STAR) {
                    $typeName = $planetTypeNames[$entity->typeId];
                }
                echo "<td>" . $typeName . "</td>";
                echo "<td>" . $entity->displayName() . "</td>";
                echo "<td>";
                if ($entity->ownerId > 0) {
                    echo "<a href=\"?page=user&amp;sub=edit&amp;user_id=" . $entity->ownerId . "\" title=\"Spieler bearbeiten\">
                        " . $entity->ownerNick . "</a>";
                }
                echo "
                </td>";
                echo "<td>" . edit_button("?page=$page&sub=edit&id=" . $entity->id) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            echo "<br/>" . button("Neue Suche", "?page=$page&amp;newsearch");
        } else {
            searchQueryReset();
            echo "Die Suche lieferte keine Resultate!<br/><br/>
            " . button("Neue Suche", "?page=$page&amp;newsearch");
        }
    }

    //
    // Suchmaske
    //

    else {
        echo "<h2>Suchmaske</h2>";
        echo "<form action=\"?page=$page\" method=\"post\" name=\"dbsearch\" autocomplete=\"off\">";
        echo "<table class=\"tb\" style=\"width:auto;margin:0px\">";
        echo "<tr>
            <th>ID:</th>
            <td><input type=\"text\" name=\"search_id\" value=\"\" size=\"5\" maxlength=\"10\" /></td></tr>";
        echo "<tr>
            <th style=\"width:160px\">Name:</th>
            <td>" . searchFieldTextOptions('name') . " <input type=\"text\" name=\"search_name\" value=\"\" size=\"20\" maxlength=\"250\" /> </td></tr>";
        echo "<tr>
            <th>Koordinaten:</th>
            <td><select name=\"search_cell_s\">";
        echo "<option value=\"\">(egal)</option>";
        for ($x = 1; $x <= $config->param1Int('num_of_sectors'); $x++) {
            for ($y = 1; $y <= $config->param2Int('num_of_sectors'); $y++) {
                echo "<option value=\"" . $x . "_" . $y . "\">$x / $y</option>";
            }
        }
        echo "</select> : <select name=\"search_cell_c\">";
        echo "<option value=\"\">(egal)</option>";
        for ($x = 1; $x <= $config->param1Int('num_of_cells'); $x++) {
            for ($y = 1; $y <= $config->param2Int('num_of_cells'); $y++) {
                echo "<option value=\"" . $x . "_" . $y . "\">$x / $y</option>";
            }
        }
        echo "</select> : <select name=\"search_cell_pos\">";
        echo "<option value=\"\">(egal)</option>";
        for ($x = 0; $x <= $config->param2Int('num_planets'); $x++)
            echo "<option value=\"$x\">$x</option>";
        echo "</select></td></tr>";
        echo "<tr>
            <th style=\"width:160px\">Entitätstyp:<br/><br/>
            <a href=\"javascript:;\" onclick=\"if (this.innerHTML=='Alles auswählen') { this.innerHTML='Auswahl aufheben';for(i=0;i<=7;i++) {document.getElementById('code_'+i).checked=true} } else {for(i=0;i<=7;i++) {document.getElementById('code_'+i).checked=false};this.innerHTML='Alles auswählen';}\">Alles auswählen</a>
            </tH>
            <td>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_0\" value=\"p\" /> Planet<br/>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_1\" value=\"s\" /> Stern<br/>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_2\" value=\"n\" /> Nebel<br/>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_3\" value=\"a\" /> Asteroidenfeld<br/>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_4\" value=\"w\" /> Wurmloch<br/>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_5\" value=\"m\" /> Marktplanet<br/>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_6\" value=\"x\" /> Allianzplanet<br/>
                <input type=\"checkbox\" name=\"search_code[]\" id=\"code_7\" value=\"e\" /> Leerer Raum
            </td></tr>";
        echo "<tr>
            <th>Besitzer-ID:</th>
            <td><input type=\"text\" name=\"search_user_id\" value=\"\" size=\"5\" maxlength=\"10\" /></td>";
        echo "<tr>
            <th>Besitzer:</th>
            <td>" . searchFieldTextOptions('user_nick') . " <input type=\"text\" name=\"search_user_nick\" value=\"\" size=\"20\" maxlength=\"250\" autocomplete=\"off\"  />&nbsp;";
        echo "</td></tr>";
        echo "<tr>
            <td style=\"height:2px\" colspan=\"2\"></td></tr>";
        echo "<tr>
            <th>Hauptplanet:</th>
            <td><input type=\"radio\" name=\"search_user_main\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
            <input type=\"radio\" name=\"search_user_main\" value=\"0\" /> Nein &nbsp;
            <input type=\"radio\" name=\"search_user_main\" value=\"1\" /> Ja</td>";
        echo "<tr>
            <th>Tr&uuml;mmerfeld:</th>
            <td><input type=\"radio\" name=\"search_debris\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
            <input type=\"radio\" name=\"search_debris\" value=\"0\" /> Nein &nbsp;
            <input type=\"radio\" name=\"search_debris\" value=\"1\"  /> Ja </td>";
        echo "<tr><th>Bemerkungen:</th>
            <td><input type=\"radio\" name=\"search_desc\" value=\"2\" checked=\"checked\" /> Egal &nbsp;
            <input type=\"radio\" name=\"search_desc\" value=\"0\" /> Keine &nbsp;
            <input type=\"radio\" name=\"search_desc\" value=\"1\"  /> Vorhanden</td></tr>";
        echo "</table>";
        echo "<br/>
        <select name=\"search_limit\">";
        for ($x = 100; $x <= 2000; $x += 100)
            echo "<option value=\"$x\">$x</option>";
        echo "</select> Datensätze sortiert nach <select name=\"search_order\">";
        foreach ($order_array as $k => $v) {
            echo "<option value=\"" . $k . "\">" . $v . "</option>";
        }
        echo "
        </select> <input type=\"submit\" name=\"search_submit\" value=\"Suchen\" /></form>";
        /** @var EntityRepository $entityRepository */
        $entityRepository = $app[EntityRepository::class];
        echo "<br/>Es sind " . nf($entityRepository->countEntityLabels()) . " Eintr&auml;ge in der Datenbank vorhanden.";

        echo "<script type=\"text/javascript\">document.forms['dbsearch'].elements[2].focus();</script>";
    }
}
