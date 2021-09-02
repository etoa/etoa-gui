<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\DefaultItem\DefaultItemRepository;
use EtoA\Message\MessageCategoryId;
use EtoA\Race\RaceDataRepository;
use EtoA\Support\BBCodeUtils;
use EtoA\Support\StringUtils;
use EtoA\Text\TextRepository;
use EtoA\UI\Tooltip;
use EtoA\Universe\Entity\EntityRepository;
use EtoA\Universe\Entity\EntityType;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\Universe\Planet\PlanetService;
use EtoA\Universe\Planet\PlanetTypeRepository;
use EtoA\Universe\Star\SolarTypeRepository;
use EtoA\Universe\Star\StarRepository;
use EtoA\User\UserRepository;
use EtoA\User\UserService;
use EtoA\User\UserSetupService;
use Symfony\Component\HttpFoundation\Request;

/** @var TextRepository $textRepo */
$textRepo = $app[TextRepository::class];

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var PlanetRepository $planetRepo */
$planetRepo = $app[PlanetRepository::class];

/** @var PlanetService $planetService */
$planetService = $app[PlanetService::class];

/** @var SolarTypeRepository  $solarTypeRepository */
$solarTypeRepository = $app[SolarTypeRepository::class];

/** @var PlanetTypeRepository $planetTypeRepository */
$planetTypeRepository = $app[PlanetTypeRepository::class];

/** @var EntityRepository $entityRepository */
$entityRepository = $app[EntityRepository::class];

/** @var StarRepository $starRepository */
$starRepository = $app[StarRepository::class];

/** @var UserSetupService $userSetupService */
$userSetupService = $app[UserSetupService::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];
/** @var RaceDataRepository $raceRepository */
$raceRepository = $app[RaceDataRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

$sx_num = $config->param1Int('num_of_sectors');
$sy_num = $config->param2Int('num_of_sectors');
$cx_num = $config->param1Int('num_of_cells');
$cy_num = $config->param2Int('num_of_cells');

echo "<h1>Willkommen in Andromeda</h1>";

echo "<div class=\"userSetupContainer\">";

$mode = null;

$currentUser = $userRepository->getUser($cu->getId());

// Apply chosen itemset
/** @var UserSession $s */
if (isset($s->itemset_key) && $request->request->has(md5($s->itemset_key)) && $request->request->has('itemset_id')) {
    $userSetupService->addItemSetListToPlanet($s->itemset_planet, $currentUser->id, $request->request->getInt('itemset_id'));
    $s->itemset_key = null;
    $s->itemset_planet = null;
    $userRepository->setSetupFinished($currentUser->id);
    $mode = "finished";
} elseif ($request->request->has('submit_chooseplanet') && $request->request->getInt('choosenplanetid') > 0 && checker_verify() && !isset($cp)) {
    $planetId = $request->request->getInt('choosenplanetid');
    $planet = $planetRepo->find($planetId);

    if ($planet !== null && $planetTypeRepository->isHabitable($planet->typeId) && $planet->userId == 0 && $planet->fields > $config->getInt('user_min_fields')) {

        $planetRepo->reset($planetId);
        $planetRepo->assignToUser($planetId, $currentUser->id, true);
        $planetService->setDefaultResources($planetId);

        $entity = $entityRepository->findIncludeCell($planetId);

        /** @var UserService $userService */
        $userService = $app[UserService::class];
        $userService->addToUserLog($currentUser->id, "planets", "{nick} wählt [b]" . $entity->toString() . "[/b] als Hauptplanet aus.");

        /** @var DefaultItemRepository $defaultItemRepository */
        $defaultItemRepository = $app[DefaultItemRepository::class];
        $sets = $defaultItemRepository->getSets();
        if (count($sets) > 1) {
            $mode = "itemsets";
        } elseif (count($sets) === 1) {
            $userSetupService->addItemSetListToPlanet($planetId, $currentUser->id, $sets[0]->id);
            $userRepository->setSetupFinished($currentUser->id);
            $mode = "finished";
        } else {
            $userRepository->setSetupFinished($currentUser->id);
            $mode = "finished";
        }
    }
} elseif (
    $request->query->has('setup_sx')
    && $request->query->getInt('setup_sx') > 0
    && $request->query->has('setup_sy')
    && $request->query->getInt('setup_sy') > 0
    && $request->query->getInt('setup_sx') <= $sx_num
    && $request->query->getInt('setup_sy') <= $sy_num
) {
    $pid = $planetRepo->getRandomFreePlanetId(
        $request->query->getInt('setup_sx'),
        $request->query->getInt('setup_sy'),
        $config->getInt('user_min_fields'),
        $request->query->get('filter_p'),
        $request->query->get('filter_s')
    );
    if ($pid !== null) {
        $mode = "checkplanet";
    } else {
        echo "Leider konnte kein geeigneter Planet in diesem Sektor gefunden werden.<br/>
        Bitte wähle einen anderen Sektor!<br/><br/>";
        $mode = "choosesector";
    }
} elseif ($currentUser->raceId > 0 && !isset($cp)) {
    $mode = "choosesector";
} elseif ($request->request->has('submit_setup1') && $request->request->getInt('register_user_race_id') > 0 && checker_verify()) {
    $currentUser->raceId = $request->request->getInt('register_user_race_id');
    $userRepository->save($currentUser);
    $mode = "choosesector";
} elseif ($currentUser->raceId === 0) {
    $mode = "race";
}

if ($mode == "itemsets" && isset($planet)) {
    /** @var DefaultItemRepository $defaultItemRepository */
    $defaultItemRepository = $app[DefaultItemRepository::class];
    $sets = $defaultItemRepository->getSets();

    $k = mt_rand(10000, 99999);
    $s->temset_key = $k;
    $s->itemset_planet = $planet->id;
    iBoxStart("Start-Objekte");
    echo "<form action=\"?\" method=\"post\">";
    checker_init();
    echo "Euch stehen mehrere Vorlagen von Start-Objekte zur Auswahl. Bitte wählt eine Vorlage aus, die darin definierten Objekte
    werden dann eurem Hauptplanet hinzugefügt: <br/><br/><select name=\"itemset_id\">";
    foreach ($sets as $set) {
        echo "<option value=\"" . $set->id . "\">" . $set->name . "</option>";
    }
    echo "</select> <input type=\"submit\" value=\"Weiter\" name=\"" . md5((string) $k) . "\" /></form>";
    iBoxEnd();
} elseif ($mode == "checkplanet" && isset($pid)) {
    echo "<form action=\"?\" method=\"post\">";
    checker_init();

    echo "<h2>Planetenwahl bestätigen</h2>";

    $planet = $planetRepo->find($pid);
    $planetType = $planetTypeRepository->find($planet->typeId);
    $entity = $entityRepository->findIncludeCell($planet->id);
    $starEntity = $entityRepository->findByCellAndPosition($entity->cellId, 0);
    $star = $starRepository->find($starEntity->id);
    $starType = $solarTypeRepository->find($star->typeId);
    $race = $raceRepository->getRace($currentUser->raceId);

    echo "<input type=\"hidden\" name=\"choosenplanetid\" value=\"" . $planet->id . "\" />";
    echo "Folgender Planet wurde für Euch ausgewählt:<br/><br/>";
    tableStart("Daten", 300);
    echo "<tr><th>Koordinaten:</th><td>" . $entity->coordinatesString() . "</td></tr>";
    echo "<tr>
        <th>Sonnentyp:</th>
        <td>" . $starType->name . "</td></tr>";
    echo "<tr>
        <th>Planettyp:</th>
        <td>" . $planetType->name . "</td></tr>";
    echo "<tr>
        <th>Felder:</td>
        <td>" . $planet->fields . " total</td></tr>";
    echo "<tr>
        <th>Temperatur:</td>
        <td>" . $planet->tempFrom . "&deg;C bis " . $planet->tempTo . "&deg;C";
    echo "</td></tr>";
    echo "<tr><th>Ansicht:</th><td style=\"background:#000;text-align:center;\"><img src=\"" . $planetService->imagePath($planet, "m") . "\" style=\"border:none;\" alt=\"planet\" /></td></tr>
    </table>";
    echo "<table class='tb'>
    <tr>
    <td>
    Du kannst einmal während des Spiels eine andere Kolonie zum Hauptplaneten bestimmen.
    </td>
    </tr>
    </table>";
    tableStart("Filter", 300);

    echo "<tr>
        <th>Sonnentyp:</th>
        <td>

        <select name=\"filter_sol_id\" id=\"filter_sol_id\">
        <option value=\"0\">Bitte wählen...</option>";
    $solarTypeNames = $solarTypeRepository->getSolarTypeNames();
    foreach ($solarTypeNames as $solarTypeId => $solarTypeName) {
        $selected = 0;

        if ($request->query->getInt('filter_s') == $solarTypeId) {
            $selected = 'selected';
        }
        echo "<option value=\"" . $solarTypeId . "\"";
        echo "$selected>" . $solarTypeName . "</option>";
    }
    echo "</select>

        </td></tr>";
    echo "<tr>
        <th>Planettyp:</th>
        <td><select name=\"filter_planet_id\" id=\"filter_planet_id\">
        <option value=\"0\">Bitte wählen...</option>";
    $planetTypeNames = $planetTypeRepository->getPlanetTypeNames();
    foreach ($planetTypeNames as $planetTypeId => $planetTypeName) {
        $selected = 0;

        if ($request->query->getInt('filter_p') == $planetTypeId) {
            $selected = 'selected';
        }

        echo "<option value=\"" . $planetTypeId . "\"";
        echo "$selected>" . $planetTypeName . "</option>";
    }
    echo "</select></td></tr>
    </table>";

    tableStart("Bonis dieser Zusammenstellung", 600);
    echo "<tr><th>Rohstoff</th>
    <th>" . $planetType->name . "</th>";
    echo "<th>" . $race->name . "</th>";
    echo "<th>" . $starType->name . "</th>";
    echo "<th>TOTAL</th></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_METAL . "Produktion " . RES_METAL . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->metal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->metal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->metal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->metal, $race->metal, $starType->metal], true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_CRYSTAL . "Produktion " . RES_CRYSTAL . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->crystal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->crystal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->crystal, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->crystal, $race->crystal, $starType->crystal], true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_PLASTIC . "Produktion " . RES_PLASTIC . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->plastic, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->plastic, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->plastic, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->plastic, $race->plastic, $starType->plastic], true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_FUEL . "Produktion " . RES_FUEL . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->fuel, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->fuel, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->fuel, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->fuel, $race->fuel, $starType->fuel], true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_FOOD . "Produktion " . RES_FOOD . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->food, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->food, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->food, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->food, $race->food, $starType->food], true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_POWER . "Produktion Energie</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->power, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->power, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->power, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->power, $race->power, $starType->power], true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_PEOPLE . "Bevölkerungswachstum</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->people, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->population, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->people, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->people, $race->population, $starType->people], true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_TIME . "Forschungszeit</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->researchTime, true, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->researchTime, true, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->researchTime, true, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->researchTime, $race->researchTime, $starType->researchTime], true, true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_TIME . "Bauzeit (Geb&auml;ude)</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($planetType->buildTime, true, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->buildTime, true, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($starType->buildTime, true, true) . "</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString([$planetType->buildTime, $race->buildTime, $starType->buildTime], true, true) . "</td></tr>";

    echo "<tr><td class=\"tbldata\">" . RES_ICON_TIME . "Fluggeschwindigkeit</td>";
    echo "<td class=\"tbldata\">-</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->fleetTime, true) . "</td>";
    echo "<td class=\"tbldata\">-</td>";
    echo "<td class=\"tbldata\">" . StringUtils::formatPercentString($race->fleetTime, true) . "</td></tr>";
    tableEnd();

    echo "<input type=\"submit\" name=\"submit_chooseplanet\" value=\"Auswählen\" />
    <input type=\"button\" onclick=\"setSelectUrl()\"
    value=\"Einen neuen Planeten auswählen\" />
    <input type=\"submit\" name=\"redo\" value=\"Einen neuen Sektor auswählen\" />";
    echo "</form>";
} elseif ($mode == "choosesector") {
    echo "<form action=\"?\" method=\"post\">";
    checker_init();
    echo "<h2>Heimatsektor auswählen</h2>";
    echo "Wählt einen Sektor aus, in dem sich euer Heimatplanet befinden soll:<br/><br/>";

    echo "Anzeigen: <select onchange=\"document.getElementById('img').src='misc/map.image.php'+this.options[this.selectedIndex].value;\">
    <option value=\"?t=" . time() . "\">Normale Galaxieansicht</option>
    <option value=\"?type=populated&t=" . time() . "\">Bevölkerte Systeme</option>

    </select><br/><br/>";
    echo "<img src=\"misc/map.image.php\" alt=\"Galaxiekarte\" id=\"img\" usemap=\"#Galaxy\" style=\"border:none;\"/>";

    echo "<map name=\"Galaxy\">\n";
    $sec_x_size = GALAXY_MAP_WIDTH / $sx_num;
    $sec_y_size = GALAXY_MAP_WIDTH / $sy_num;
    $xcnt = 1;
    $ycnt = 1;
    for ($x = 0; $x < GALAXY_MAP_WIDTH; $x += $sec_x_size) {
        $ycnt = 1;
        for ($y = 0; $y < GALAXY_MAP_WIDTH; $y += $sec_y_size) {
            $countStars = $entityRepository->countEntitiesOfCodeInSector($xcnt, $ycnt, EntityType::STAR);
            $countPlanets = $entityRepository->countEntitiesOfCodeInSector($xcnt, $ycnt, EntityType::PLANET);
            $countInhabitedPlanets = $planetRepo->countWithUserInSector($xcnt, $ycnt);

            $tt = new Tooltip();
            $tt->addTitle("Sektor $xcnt/$ycnt");
            $tt->addText("Sternensysteme: " . $countStars);
            $tt->addText("Planeten: " . $countPlanets);
            $tt->addGoodCond("Bewohnte Planeten: " . $countInhabitedPlanets);
            $tt->addComment("Klickt hier um euren Heimatplaneten in Sektor <b>" . $xcnt . "/" . $ycnt . "</b> anzusiedeln!");

            echo "<area shape=\"rect\" coords=\"$x," . (GALAXY_MAP_WIDTH - $y) . "," . ($x + $sec_x_size) . "," . (GALAXY_MAP_WIDTH - $y - $sec_y_size) . "\" href=\"?setup_sx=" . $xcnt . "&amp;setup_sy=" . $ycnt . "\" alt=\"Sektor $xcnt / $ycnt\" " . $tt->toString() . ">\n";
            $ycnt++;
        }
        $xcnt++;
    }
    echo "</map>\n";

    echo "</form>";
} elseif ($mode == "race") {
    echo "<form action=\"?\" method=\"post\">";
    checker_init();

    echo "<h2>Rasse auswählen</h2>
    Bitte wählt die Rasse eures Volkes aus.<br/>
    Jede Rasse hat Vor- und Nachteile sowie einige Spezialeinheiten:<br/><br/>";

    /** @var RaceDataRepository $raceRepository */
    $raceRepository = $app[RaceDataRepository::class];

    $raceNames = $raceRepository->getRaceNames();

    echo "<select name=\"register_user_race_id\" id=\"register_user_race_id\">
    <option value=\"0\">Bitte wählen...</option>";
    foreach ($raceNames as $raceId => $raceName) {
        echo "<option value=\"" . $raceId . "\"";
        echo ">" . $raceName . "</option>";
    }
    echo "</select>";

    echo " &nbsp; <input type=\"button\" name=\"random\" id=\"random\" value=\"Zufällige Rasse auswählen\"  onclick=\"rdm()\"/>";

    // xajax content will be placed in the following cell
    echo "<br/><br/><div id=\"raceInfo\"></div>";
    echo "<br/><br/><input type=\"submit\" name=\"submit_setup1\" id=\"submit_setup1\" value=\"Weiter\" />";
    echo "</form>";
} elseif ($mode == "finished") {
    echo "<h2>Einrichtung abgeschlossen</h2>";

    $welcomeText = $textRepo->find('welcome_message');
    if ($welcomeText->isEnabled()) {
        iBoxStart("Willkommen");
        echo BBCodeUtils::toHTML($welcomeText->content);
        iBoxEnd();

        /** @var \EtoA\Message\MessageRepository $messageRepository */
        $messageRepository = $app[\EtoA\Message\MessageRepository::class];
        $messageRepository->createSystemMessage($currentUser->id, MessageCategoryId::USER, 'Willkommen', $welcomeText->content);
    }
    echo '<input type="button" value="Zum Heimatplaneten" onclick="document.location=\'?page=planetoverview\'" />';
} else {
    echo "Fehler";
}
echo "</div>";
