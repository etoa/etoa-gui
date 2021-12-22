<?PHP

use EtoA\Building\BuildingDataRepository;
use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Defense\DefenseDataRepository;
use EtoA\Log\FleetLogFacility;
use EtoA\Log\GameLogFacility;
use EtoA\Log\LogFacility;
use EtoA\Log\LogRepository;
use EtoA\Log\LogSeverity;
use EtoA\Ship\ShipDataRepository;
use EtoA\Support\StringUtils;
use EtoA\Technology\TechnologyDataRepository;
use Twig\Environment;

// TODO
global $app;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

\EtoA\Admin\LegacyTemplateTitleHelper::$title = 'Logs';

echo "<div id=\"logsinfo\"></div>"; //nur zu entwicklungszwecken!

if (isset($_GET['sub']) && $_GET['sub'] == "battlelogs") {
    battleLog();
} elseif ($sub == "check_fights") {
    checkFights();
} elseif ($sub == "gamelogs") {
    newGamelogs();
} elseif ($sub == "fleetlogs") {
    newFleetLogs();
} elseif ($sub == "debrislog") {
    debrisLog();
} else {
    newCommonLog();
}

function battleLog()
{
    echo "Battle Log im aufbau!<br>";
}

function checkFights()
{
    header('Location: /admin/attack-ban/');
    die();
}

function newGamelogs()
{
    global $app;

    echo "<h2>Spiellogs</h2>";

    ?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyGameLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            document.getElementById('logcat').value = 0;
            document.getElementById('logsev').value = 0;
            document.getElementById('searchtext').value = '';
            document.getElementById('searchuser').value = '';
            document.getElementById('searchalliance').value = '';
            document.getElementById('searchentity').value = '';
            fillObjectSelection();
            applyFilter(0);
            document.getElementById('searchtext').focus();
        }

        function fillObjectSelection() {
            elem = document.getElementById('object_id');
            elem.length = 0;
            elem.options[elem.options.length] = new Option('(Alle)', 0);
            switch (document.getElementById('logcat').value) {
                case '1':
                    <?PHP
                    /** @var BuildingDataRepository $buildingRepository */
                    $buildingRepository = $app[BuildingDataRepository::class];
                    foreach ($buildingRepository->getBuildingNames(true) as $buildingId => $buildingName) {
                        echo "elem.options[elem.options.length] = new Option('$buildingName',$buildingId);";
                    }
                    ?>
                    break;
                case '2':
                    <?PHP
                    /** @var TechnologyDataRepository $technologyRepository */
                    $technologyRepository = $app[TechnologyDataRepository::class];
                    foreach ($technologyRepository->getTechnologyNames(true) as $techId => $technologyName) {
                        echo "elem.options[elem.options.length] = new Option('$technologyName',$techId);";
                    }
                    ?>
                    break;
                case '3':
                    <?PHP
                    /** @var ShipDataRepository $shipRepository */
                    $shipRepository = $app[ShipDataRepository::class];
                    foreach ($shipRepository->getShipNames(true) as $shipId => $shipName) {
                        echo "elem.options[elem.options.length] = new Option('$shipName',$shipId);";
                    }
                    ?>
                    break;
                case '4':
                    <?PHP
                    /** @var DefenseDataRepository $defenseRepository */
                    $defenseRepository = $app[DefenseDataRepository::class];
                    foreach ($defenseRepository->getDefenseNames(true) as $defenseId => $defenseName) {
                        echo "elem.options[elem.options.length] = new Option('$defenseId','$defenseName');";
                    }
                    ?>
                    break;
                case '5':
                    <?PHP
                    $quests = require dirname(__DIR__) . '/../../data/quests.php';
                    foreach ($quests as $quest) {
                        echo "elem.options[elem.options.length] = new Option('" . $quest['title'] . "','" . $quest['id'] . "');";
                    }
                    ?>
                    break;
            }
        }
    </script>
    <?PHP

    echo '<fieldset style="width:950px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";
    echo "<label for=\"logsev\">Ab Schweregrad:</label>
    <select id=\"logsev\" name=\"logsev\" onchange=\"applyFilter(0)\">";
    foreach (LogSeverity::SEVERITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logcat\">Kategorie:</label>
    <select id=\"logcat\" name=\"logcat\" onchange=\"fillObjectSelection();applyFilter(0)\">
    <option value=\"0\">(Alle)</option>";
    foreach (GameLogFacility::FACILITIES as $k => $v) {
        if ($k > 0)
            echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"object_id\">Objekt:</label>
    <select id=\"object_id\" name=\"object_id\" onchange=\"applyFilter(0)\">
    <option value=\"0\">(Alle)</option>";
    echo "</select> &nbsp; ";

    echo " <label for=\"searchtext\">Suchtext:</label> <input type=\"text\" id=\"searchtext\" name=\"searchtext\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo "<br/><br/>";

    echo " <label for=\"searchuser\">User:</label> <input type=\"text\" id=\"searchuser\" name=\"searchuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"searchalliance\">Allianz:</label> <input type=\"text\" id=\"searchalliance\" name=\"searchalliance\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    // Todo: extend to any entity
    echo " <label for=\"searchentity\">Planet:</label> <input type=\"text\" id=\"searchentity\" name=\"searchentity\" value=\"\" autocomplete=\"off\" /> &nbsp; ";


    echo " &nbsp; <input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showGameLogs();
    echo "</div>";
}

function newFleetLogs()
{
    echo "<h2>Flottenlogs</h2>";

    ?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyFleetLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            document.getElementById('flaction').value = 0;
            document.getElementById('logsev').value = 0;
            document.getElementById('searchuser').value = '';
            applyFilter(0);
        }
    </script>
    <?PHP

    echo '<fieldset style="width:800px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";
    echo "<label for=\"logsev\">Ab Schweregrad:</label>
    <select id=\"logsev\" name=\"logsev\" onchange=\"applyFilter(0)\">";
    foreach (LogSeverity::SEVERITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logfac\">Facility:</label>
    <select id=\"logfac\" name=\"logfac\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Alle)</option>";
    foreach (FleetLogFacility::FACILITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logcat\">Aktion:</label>
    <select id=\"flaction\" name=\"flaction\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Egal)</option>";
    foreach (FleetAction::getAll() as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp;
    <select id=\"flstatus\" name=\"flstatus\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Egal)</option>";
    foreach (FleetAction::$statusCode as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select><br/><br/> ";

    echo " <label for=\"searchuser\">Flottenuser:</label> <input type=\"text\" id=\"searchuser\" name=\"searchuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";

    echo " <label for=\"searcheuser\">Entityuser:</label> <input type=\"text\" id=\"searcheuser\" name=\"searcheuser\" value=\"\" autocomplete=\"off\" /> &nbsp;<br/><br/>";

    echo " <label for=\"start\">Start:</label> <input type=\"text\" id=\"start\" name=\"start\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"target\">Ziel:</label> <input type=\"text\" id=\"target\" name=\"target\" value=\"\" autocomplete=\"off\" /> &nbsp; ";

    echo "<input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showFleetLogs();
    echo "</div>";
}

function debrisLog()
{
    header('Location: /admin/logs/debris/');
    die();
}

function newCommonLog()
{
    header('Location: /admin/logs/');
    die();
}
