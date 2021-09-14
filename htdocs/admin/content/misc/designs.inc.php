<?PHP

use EtoA\Core\Configuration\ConfigurationService;
use EtoA\Design\Design;
use EtoA\Support\FileUtils;
use EtoA\User\UserPropertiesRepository;

/** @var ConfigurationService $config */
$config = $app[ConfigurationService::class];

$designs = get_designs();

$successMessage = null;
$errorMessage = null;
// Design download
if (isset($_GET['download'])) {
    $design = $_GET['download'];
    if (isset($designs[$design])) {
        $zipFile = tempnam('sys_get_temp_dir', $design);
        $dir = $designs[$design]['dir'];

        try {
            FileUtils::createZipFromDirectory($dir, $zipFile);
            header('Content-Type: application/zip');
            header('Content-disposition: attachment; filename=' . $design . '.zip');
            header('Content-Length: ' . filesize($zipFile));
            readfile($zipFile);
            unlink($zipFile);
            exit();
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }
    }
}
// Removal of custom design
else if (isset($_GET['remove'])) {
    $design = $_GET['remove'];
    if (isset($designs[$design]) && $designs[$design]['custom']) {
        $dir = $designs[$design]['dir'];
        FileUtils::removeDirectory($dir);
        $successMessage = 'Design gelöscht';
        $designs = get_designs();
    }
}

/** @var UserPropertiesRepository $userPropertiesRepository */
$userPropertiesRepository = $app[UserPropertiesRepository::class];
$designCounts = $userPropertiesRepository->getDesignStats(99);
// Show all designs
foreach ($designs as $k => $v) {
    $designs[$k]['users'] = $designCounts[$k] ?? 0;
    $designs[$k]['default'] = ($k == $config->get('default_css_style'));
    // If it is the default design, add all users who have not explicitly selected a design
    if ($k == $config->get('default_css_style')) {
        $designs[$k]['users'] += $designCounts[''] ?? 0;
    }
}

$sampleInfoFile = __DIR__ . '/../../../' . Design::DIRECTORY . "/official/" . $config->get('default_css_style') . '/' . Design::CONFIG_FILE_NAME;

echo $twig->render('admin/misc/designs.html.twig', [
    'successMessage' => $successMessage,
    'errorMessage' => $errorMessage,
    'designs' => $designs,
    'sampleInfoFile' => file_get_contents($sampleInfoFile),
    'designInfoParams' => [
        'name' => 'Name des Designs (sollte identisch mit dem Namen des Verzeichnisses sein',
        'changed' => 'Datum der letzten Änderung',
        'version' => 'Version',
        'author' => 'Autor',
        'email' => 'E-Mail Adresse des Autors',
        'description' => 'Kurzbeschreibung des Designs',
        'restricted' => 'Wenn auf \'true\' gesetzt, können nur als Admin oder Entwickler markierte Spieler dieses Design auswählen',
    ],
    'knownTemplateVars' => [
        'currentPlanetImage' => 'Pfad zum aktuellen Planetenbild',
        'prevPlanetId' => 'ID des vorherigen Planeten',
        'nextPlanetId' => 'ID des nächsten Planeten',
        'page' => 'Name der aktuellen Seite',
        'selectField' => 'Auswahlfeld (&lt;select&gt;) aller Planeten',
        'planetList' => 'Liste aller Planeten [label, url, current, image]',
        'messages' => 'Anzahl neuer Nachrichten',
        'newreports' => 'Anzahl neuer Berichte',
        'notes' => 'Anzahl vorhandener Notizen',
        'fleetAttack' => 'Anzahl fremder angreifender Flotten',
        'ownFleetCount' => 'Anzahl eigener Flotten',
        'buddys' => 'Anzahl Freunde welche online sind',
        'bugreportUrl' => 'URL zur Seite wo man Fehler melden kann',
        'helpcenterOnclick' => 'JavaScript-Code zum Öffnen des Help-Centers als Popup',
        'urlForum' => 'URL zum Forum',
        'chatOnclick' => 'JavaScript-Code zum Öffnen des Chats',
        'teamspeakOnclick' => 'JavaScript-Code zum Öffnen der TeamSpeak Seite als Popup',
        'rulesOnclick' => 'JavaScript-Code zum Öffnen der Regeln-Seite als Popup',
        'serverTime' => 'Die aktuelle Zeit, wird automatisch aktualisiert',
        'serverTimeUnix' => 'Die aktuelle Zeit als Unix-Zeitstempel',
        'content_for_layout' => 'Die eigentliche Inhalt der Seite',
        'templateDir' => 'Pfad zum Template-Verzeichnis (z.B. zum Laden von Bildern)',
        'topNav' => 'Array mit allen Elementen der Header-Navigation',
        'mainNav' => 'Array mit allen Elementen der Haupt-Spielnavigation',
        'usersOnline' => 'Anzahl User welche online sind',
        'usersTotal' => 'Anzahl registrierter User',
        'renderTime' => 'Zeit welche gebraucht wurde, um den Inhalt der Seite zusammenzustellen',
        'userNick' => 'Name des Spielers',
        'userPoints' => 'Punkte des Spielers',
        'isAdmin' => 'Ist wahr falls der Spieler ein Admin ist',
        'buddyreq' => 'Ist wahr falls Freundschaftsanfragen vorhanden sind',
        'infoText' => 'InGame Infotext, falls definiert',
        'enableKeybinds' => 'Ist wahr wenn die Tastaturnavigation aktiviert ist',
    ],
    'additionalCommonCssFiles' => [
        [
            'name' => 'reset.css',
            'description' => 'Resets all element dimensions',
            'linkUrl' => '../web/css/reset.css',
            'url' => '../../../web/css/reset.css',
        ],
        [
            'name' => 'game.css',
            'description' => 'Common definitions for some ingame elements, colors, icons, ...',
            'linkUrl' => '../web/css/game.css',
            'url' => '../../../web/css/game.css',
        ],
    ],
]);
exit();
