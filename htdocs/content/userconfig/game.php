<?PHP

use EtoA\Tutorial\TutorialManager;
use EtoA\User\UserPropertiesRepository;

/** @var \EtoA\Ship\ShipDataRepository $shipDataRepository */
$shipDataRepository = $app[\EtoA\Ship\ShipDataRepository::class];

/** @var TutorialManager $tutorialManager */
$tutorialManager = $app[TutorialManager::class];

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];

$properties = $userPropertiesRepository->getOrCreateProperties($cu->id);

// Datenänderung übernehmen
if (isset($_POST['data_submit']) && checker_verify()) {
    $properties->spyShipId = $_POST['spyship_id'];
    $properties->spyShipCount = $_POST['spyship_count'];
    $properties->analyzeShipId = $_POST['analyzeship_id'];
    $properties->analyzeShipCount = $_POST['analyzeship_count'];
    $properties->exploreShipId = $_POST['exploreship_id'];
    $properties->exploreShipCount = $_POST['exploreship_count'];
    $properties->startUpChat = $_POST['startup_chat'] == 1;
    $properties->showCellreports = $_POST['show_cellreports'] == 1;
    $properties->enableKeybinds = $_POST['keybinds_enable'] == 1;

    if ((strlen($_POST['chat_color']) == 4 &&
            preg_match('/^#[a-fA-F0-9]{3}$/', $_POST['chat_color'])) ||
        (strlen($_POST['chat_color']) == 7 &&
            preg_match('/^#[a-fA-F0-9]{6}$/', $_POST['chat_color']))
    ) {
        $properties->chatColor = substr($_POST['chat_color'], 1);
        if ($properties->chatColor == '000' || $properties->chatColor == '000000') {
            success_msg('Chatfarbe schwarz auf schwarz ist eine Weile ja ganz lustig, aber in ein paar Minuten bitte zur&uuml;ck&auml;ndern ;)');
        } else {
            success_msg('Benutzer-Daten wurden ge&auml;ndert!');
        }
    } else {
        $properties->chatColor = "FFF";
        error_msg('Ung&uuml;ltiger RGB-Farbwert, Standardwert #FFF wurde eingef&uuml;gt.');
    }
    $userPropertiesRepository->storeProperties($cu->id, $properties);
}


if (isset($_POST['show_tut']) && checker_verify()) {
    $tutorialManager->reopenAllTutorials($cu->id);
    echo '<script type="text/javascript">showTutorialText(1,0)</script>';
}


echo "<form action=\"?page=$page&mode=game\" method=\"post\" enctype=\"multipart/form-data\">";
$cstr = checker_init();
tableStart("Spieloptionen");

// Spy ships for direct scan
echo "<tr>
      <th><b>Spionagesonden für Direktscan:</b></th>
    <td><input type=\"text\" name=\"spyship_count\" maxlength=\"5\" size=\"5\" value=\"" . $properties->spyShipCount . "\"> ";
$shipNames = $shipDataRepository->getShipNamesWithAction('spy');
if (count($shipNames) > 0) {
    echo '<select name="spyship_id"><option value="0">(keines)</option>';
    foreach ($shipNames as $shipId => $shipName) {
        echo '<option value="' . $shipId . '"';
        if ($properties->spyShipId == $shipId) echo ' selected="selected"';
        echo '>' . $shipName . '</option>';
    }
} else {
    echo "Momentan steht kein Schiff zur Auswahl!";
}
echo "</td></tr>";

// Analyzator ships for quick analysis
echo "<tr>
      <th><b>Analysatoren für Quickanalyse:</b></th>
    <td><input type=\"text\" name=\"analyzeship_count\" maxlength=\"5\" size=\"5\" value=\"" . $properties->analyzeShipCount . "\"> ";
$shipNames = $shipDataRepository->getShipNamesWithAction('analyze');
if (count($shipNames) > 0) {
    echo '<select name="analyzeship_id"><option value="0">(keines)</option>';
    foreach ($shipNames as $shipId => $shipName) {
        echo '<option value="' . $shipId . '"';
        if ($properties->analyzeShipId == $shipId) echo ' selected="selected"';
        echo '>' . $shipName . '</option>';
    }
} else {
    echo "Momentan steht kein Schiff zur Auswahl!";
}
echo "</td></tr>";

// Default explore ship
echo "<tr>
      <th><b>Erkundungsschiffe für Direkterkundung:</b></th>
    <td>
        <input type=\"text\" name=\"exploreship_count\" maxlength=\"5\" size=\"5\" value=\"" . $properties->exploreShipCount . "\"> ";
$shipNames = $shipDataRepository->getShipNamesWithAction('explore');
if (count($shipNames) > 0) {
    echo '<select name="exploreship_id"><option value="0">(keines)</option>';
    foreach ($shipNames as $shipId => $shipName) {
        echo '<option value="' . $shipId . '"';
        if ($properties->exploreShipId == $shipId) echo ' selected="selected"';
        echo '>' . $shipName . '</option>';
    }
} else {
    echo "Momentan steht kein Schiff zur Auswahl!";
}
echo "</td></tr>";

//Berichte im Sonnensystem (Aktiviert/Deaktiviert)
echo "<tr>
                <th>Berichte im Sonnensystem:</th>
                <td>
              <input type=\"radio\" name=\"show_cellreports\" value=\"1\" ";
if ($properties->showCellreports) echo " checked=\"checked\"";
echo "/> Aktiviert &nbsp;

              <input type=\"radio\" name=\"show_cellreports\" value=\"0\" ";
if (!$properties->showCellreports) echo " checked=\"checked\"";
echo "/> Deaktiviert
            </td>
          </tr>";
//Notizbox (Aktiviert/Deaktiviert)
echo "<tr>
                <th>Chat beim Login öffnen:</th>
                <td>
              <input type=\"radio\" name=\"startup_chat\" value=\"1\" ";
if ($properties->startUpChat) echo " checked=\"checked\"";
echo "/> Aktiviert &nbsp;

              <input type=\"radio\" name=\"startup_chat\" value=\"0\" ";
if (!$properties->startUpChat) echo " checked=\"checked\"";
echo "/> Deaktiviert
            </td>
          </tr>";

echo "<tr>
            <th>Tutorial:</th>
            <td>
                <input type=\"submit\" name=\"show_tut\" value=\"Anzeigen\"/>
            </td>
        </tr>";

// Chat font color
echo "<tr>
              <th>Chat Schriftfarbe:</th>
              <td>
            <input type=\"color\"

                    id=\"chat_color\"
                    name=\"chat_color\"
                    size=\"6\"
                    value=\"#" . $properties->chatColor . "\"
                    onkeyup=\"addFontColor(this.id,'chatPreview')\"
                    onchange=\"addFontColor(this.id,'chatPreview')\"/>&nbsp;
            <div id=\"chatPreview\" style=\"color:#" . $properties->chatColor . ";\">&lt;" . $cu . " | " . date("H:i", time()) . "&gt; Chatvorschau </div>
          </td>
        </tr>";
//Keybinds (Aktiviert/Deaktiviert)
echo "<tr>
            <th>Keybinds (Navigation mit Tastatur):</th>
            <td>
              <input type=\"radio\" name=\"keybinds_enable\" value=\"1\" ";
if ($properties->enableKeybinds) echo " checked=\"checked\"";
echo "/> Aktiviert &nbsp;
              <input type=\"radio\" name=\"keybinds_enable\" value=\"0\" ";
if (!$properties->enableKeybinds) echo " checked=\"checked\"";
echo "/> Deaktiviert
        </td>
          </tr>";


tableEnd();
echo "<input type=\"submit\" name=\"data_submit\" value=\"&Uuml;bernehmen\"/>";
echo "</form><br/><br/>";
