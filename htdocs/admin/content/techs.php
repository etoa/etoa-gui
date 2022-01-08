<?PHP

use EtoA\Admin\Forms\TechnologiesForm;
use EtoA\Admin\Forms\TechnologyTypesForm;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Core\ObjectWithImage;
use EtoA\Ranking\RankingService;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use EtoA\Technology\TechnologyListItemSearch;
use EtoA\Technology\TechnologyPointRepository;
use EtoA\Technology\TechnologyRepository;
use EtoA\Technology\TechnologySort;
use EtoA\Universe\Planet\PlanetRepository;
use EtoA\User\UserRepository;
use Symfony\Component\HttpFoundation\Request;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

/** @var TechnologyRepository $technologyRepository */
$technologyRepository = $app[TechnologyRepository::class];

/** @var TechnologyDataRepository $technologyDataRepository */
$technologyDataRepository = $app[TechnologyDataRepository::class];

/** @var TechnologyPointRepository $technologyPointRepository */
$technologyPointRepository = $app[TechnologyPointRepository::class];

/** @var RankingService $rankingService */
$rankingService = $app[RankingService::class];

/** @var PlanetRepository $planetRepository */
$planetRepository = $app[PlanetRepository::class];

/** @var UserRepository $userRepository */
$userRepository = $app[UserRepository::class];

/** @var Request */
$request = Request::createFromGlobals();

if ($sub == "type") {
    TechnologyTypesForm::render($app, $request);
}

//
// Technologien
//
elseif ($sub == "data") {
    TechnologiesForm::render($app, $request);
}
//
// Anforderungen
//
elseif ($sub == "req") {

    define("TITLE", "Forschungsanforderungen");
    define("REQ_TBL", "tech_requirements");
    define("ITEM_ENABLE_FLD", "tech_show");

    define("ITEM_IMAGE_PATH", ObjectWithImage::BASE_PATH . "/technologies/technology<DB_TABLE_ID>_small.png");

    $objectNames = $technologyDataRepository->getTechnologyNames(true, TechnologySort::type());
    include("inc/requirements.inc.php");
}

//
// Liste
//
else {
    \EtoA\Admin\LegacyTemplateTitleHelper::$title = 'Forschungsliste';

    $build_type = [];
    $build_type[0] = "Unt&auml;tig";
    $build_type[3] = "Forschen";

    //
    // Suchformular Technologien
    //

        // Technologien laden
        /** @var TechnologyDataRepository $technologyDataRepository */
        $technologyDataRepository = $app[TechnologyDataRepository::class];
        $technologyNames = $technologyDataRepository->getTechnologyNames(true);

        echo '<div id="tabs-2">';

        // Hinzufügen
        echo "<form action=\"?page=$page&amp;sub=$sub&amp;action=search\" method=\"post\">";
        echo "<table class=\"tbl\">";
        echo "<tr><th class=\"tbltitle\">Technologie</th><td class=\"tbldata\"><select name=\"tech_id\">";
        foreach ($technologyNames as $techId => $technologyName)
            echo "<option value=\"" . $techId . "\">" . $technologyName . "</option>";
        echo "</select><br>Alle Techs <input type='checkbox' name='all_techs'></td></tr>";
        echo "<tr><th class=\"tbltitle\">Stufe</th><td class=\"tbldata\"><input type=\"text\" name=\"techlist_current_level\" value=\"1\" size=\"1\" maxlength=\"3\" /></td></tr>";
        echo "<tr><th class=\"tbltitle\">f&uuml;r den Spieler</th><td class=\"tbldata\"> <select name=\"planet_id\"><";
        $userNicks = $userRepository->searchUserNicknames();
        $mainPlanets = $planetRepository->getMainPlanets();
        foreach ($mainPlanets as $mainPlanet) {
            echo "<option value=\"" . $mainPlanet->id . ":" . $mainPlanet->userId . "\">" . $userNicks[$mainPlanet->userId] . "</option>";
        }
        echo "</select></td></tr>";
        tableEnd();
        echo "<p><input type=\"submit\" name=\"new\" value=\"Hinzuf&uuml;gen\" /></p></form>";

        echo '
                </div>
            </div>';

        $tblcnt = $technologyRepository->count();
        echo "<p>Es sind " . StringUtils::formatNumber($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.</p>";
}
