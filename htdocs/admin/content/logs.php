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

$twig->addGlobal('title', 'Logs');

echo "<div id=\"logsinfo\"></div>"; //nur zu entwicklungszwecken!

if ($sub == "errorlog") {
    errorlog($twig);
} elseif (isset($_GET['sub']) && $_GET['sub'] == "battlelogs") {
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

function errorlog(Environment $twig)
{
    global $page;
    global $sub;

    if (isset($_POST['purgelog_submit'])) {
        file_put_contents(EException::LOG_FILE, '');
        forward('?page=' . $page . '&sub=' . $sub);
    }

    $logFile = null;
    if (is_file(EException::LOG_FILE)) {
        $logFile = file_get_contents(EException::LOG_FILE);
    }

    echo $twig->render('admin/logs/errorlog.html.twig', [
        'logFile' => $logFile,
    ]);
    exit();
}

function battleLog()
{
    echo "Battle Log im aufbau!<br>";
}

function checkFights()
{
    echo "<h2>Angriffsverletzung</h2>";

?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyAttackAbuseLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            const dateTimeString = DateTime.fromISO('<?= date(DateTime::ISO8601) ?>')
                .setZone('<?= date_default_timezone_get() ?>')
                .toISO({ includeOffset: false, suppressMilliseconds: true })
                .slice(0,16);
            document.getElementById('searchtime').value = dateTimeString;
            document.getElementById('searchfuser').value = '';
            document.getElementById('searcheuser').value = '';
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
    echo "<label for=\"logcat\">Aktion:</label>
    <select id=\"flaction\" name=\"flaction\" onchange=\"applyFilter(0)\">
    <option value=\"\">(Egal)</option>";
    foreach (FleetAction::getAll() as $k => $v) {
        if ($v->attitude() == 3)
            echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";
    echo " <label for=\"searchtime\">Zeit:</label> ";
    echo '<input type="datetime-local" value="'.date("Y-m-d\TH:i", time()).'" name="searchtime" id="searchtime">';
    echo "&nbsp; ";
    echo "<br/><br/>";
    echo " <label for=\"searchfuser\">Angreifer:</label> <input type=\"text\" id=\"searchfuser\" name=\"searchfuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"searcheuser\">Verteidiger:</label> <input type=\"text\" id=\"searcheuser\" name=\"searcheuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";


    echo "<input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showAttackAbuseLogs();
    echo "</div>";
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
    echo "<h2>Trümmerfeld Logs</h2>";

    ?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyDebrisLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            const dateTimeString = DateTime.fromISO('<?= date(DateTime::ISO8601) ?>')
                .setZone('<?= date_default_timezone_get() ?>')
                .toISO({ includeOffset: false, suppressMilliseconds: true })
                .slice(0,16);
            document.getElementById('searchtime').value = dateTimeString;
            document.getElementById('searchuser').value = '';
            document.getElementById('searchadmin').value = '';
            applyFilter(0);
        }
    </script>
<?PHP

    echo '<fieldset style="width:950px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";

    echo " <label for=\"searchuser\">User:</label> <input type=\"text\" id=\"searchuser\" name=\"searchuser\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    echo " <label for=\"searchadmin\">Admin:</label> <input type=\"text\" id=\"searchadmin\" name=\"searchadmin\" value=\"\" autocomplete=\"off\" /> &nbsp; ";
    // Todo: extend to any entity
    echo "<br/><br/>";

    echo " <label for=\"searchtime\">Zeit:</label> ";
    echo '<input type="datetime-local" value="'.date("Y-m-d\TH:i", time()).'" name="searchtime" id="searchtime">';

    echo " &nbsp; <input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showDebrisLogs();
    echo "</div>";
}

function newCommonLog()
{
    echo "<h2>Allgemeine Logs</h2>";

?>
    <script type="text/javascript">
        function applyFilter(limit) {
            xajax_applyLogFilter(xajax.getFormValues('filterform'), limit);
        }

        function resetFilter() {
            document.getElementById('logcat').value = 0;
            document.getElementById('logsev').value = 0;
            document.getElementById('searchtext').value = '';
            applyFilter(0);
            document.getElementById('searchtext').focus();
        }
    </script>
<?PHP

    echo '<fieldset style="width:900px"><legend>Filter</legend>';
    echo "<form action=\".\" method=\"post\" id=\"filterform\">";
    echo "<label for=\"logsev\">Ab Schweregrad:</label>
    <select id=\"logsev\" name=\"logsev\" onchange=\"applyFilter(0)\">";
    foreach (LogSeverity::SEVERITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo "<label for=\"logcat\">Kategorie:</label>
    <select id=\"logcat\" name=\"logcat\" onchange=\"applyFilter(0)\">
    <option value=\"0\">(Alle)</option>";
    foreach (LogFacility::FACILITIES as $k => $v) {
        echo "<option value=\"" . $k . "\">" . $v . "</option>";
    }
    echo "</select> &nbsp; ";

    echo " <label for=\"searchtext\">Suchtext:</label> <input type=\"text\" id=\"searchtext\" name=\"searchtext\" value=\"\" /> &nbsp;
    <input type=\"submit\" value=\"Anwenden\" onclick=\"applyFilter(0);document.getElementById('searchtext').select();return false;\" /> &nbsp;
    <input type=\"button\" value=\"Reset\" onclick=\"resetFilter();\" />";
    echo "</form>";
    echo '</fieldset>';

    echo "<div id=\"log_contents\">";
    showLogs();
    echo "</div>";

    global $app;
    /** @var LogRepository $logRepository */
    $logRepository = $app[LogRepository::class];
    $tblcnt = $logRepository->count();
    echo "<p>Es sind " . StringUtils::formatNumber($tblcnt) . " Eintr&auml;ge in der Datenbank vorhanden.</p>";
}
